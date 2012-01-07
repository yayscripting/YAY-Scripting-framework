<?php

class shweta_simple_html_dom_node {
    public $nodetype = SHW_TYPE_TEXT;
    public $tag = 'text';
    public $attr = array();
    public $children = array();
    public $nodes = array();
    public $parent = null;
    public $_ = array();
    private $dom = null;

    function __construct($dom) {
        $this->dom = $dom;
        $dom->nodes[] = $this;
    }

    function __destruct() {
        $this->clear();
    }

    function __toString() {
        return $this->outertext();
    }

	function clear() {
        $this->dom = null;
        $this->nodes = null;
        $this->parent = null;
        $this->children = null;
    }
    
    function dump($show_attr=true) {
        dump_html_tree($this, $show_attr);
    }

    function parent() {
        return $this->parent;
    }

    function children($idx=-1) {
        if ($idx===-1) return $this->children;
        if (isset($this->children[$idx])) return $this->children[$idx];
        return null;
    }

    function first_child() {
        if (count($this->children)>0) return $this->children[0];
        return null;
    }

    function last_child() {
        if (($count=count($this->children))>0) return $this->children[$count-1];
        return null;
    }

    function next_sibling() {
        if ($this->parent===null) return null;
        $idx = 0;
        $count = count($this->parent->children);
        while ($idx<$count && $this!==$this->parent->children[$idx])
            ++$idx;
        if (++$idx>=$count) return null;
        return $this->parent->children[$idx];
    }

    function prev_sibling() {
        if ($this->parent===null) return null;
        $idx = 0;
        $count = count($this->parent->children);
        while ($idx<$count && $this!==$this->parent->children[$idx])
            ++$idx;
        if (--$idx<0) return null;
        return $this->parent->children[$idx];
    }

    function innertext() {
        if (isset($this->_[SHW_INFO_INNER])) return $this->_[SHW_INFO_INNER];
        if (isset($this->_[SHW_INFO_TEXT])) return $this->dom->restore_noise($this->_[SHW_INFO_TEXT]);

        $ret = '';
        foreach($this->nodes as $n)
            $ret .= $n->outertext();
        return $ret;
    }

    function outertext() {
        if ($this->tag==='root') return $this->innertext();

        if ($this->dom->callback!==null)
            call_user_func_array($this->dom->callback, array($this));

        if (isset($this->_[SHW_INFO_OUTER])) return $this->_[SHW_INFO_OUTER];
        if (isset($this->_[SHW_INFO_TEXT])) return $this->dom->restore_noise($this->_[SHW_INFO_TEXT]);

        $ret = $this->dom->nodes[$this->_[SHW_INFO_BEGIN]]->makeup();

        if (isset($this->_[SHW_INFO_INNER]))
            $ret .= $this->_[SHW_INFO_INNER];
        else {
            foreach($this->nodes as $n)
                $ret .= $n->outertext();
        }

        if(isset($this->_[SHW_INFO_END]) && $this->_[SHW_INFO_END]!=0)
            $ret .= '</'.$this->tag.'>';
        return $ret;
    }

    function text() {
        if (isset($this->_[SHW_INFO_INNER])) return $this->_[SHW_INFO_INNER];
        switch ($this->nodetype) {
            case SHW_TYPE_TEXT: return $this->dom->restore_noise($this->_[SHW_INFO_TEXT]);
            case SHW_TYPE_COMMENT: return '';
            case SHW_TYPE_UNKNOWN: return '';
        }
        if (strcasecmp($this->tag, 'script')===0) return '';
        if (strcasecmp($this->tag, 'style')===0) return '';

        $ret = '';
        foreach($this->nodes as $n)
            $ret .= $n->text();
        return $ret;
    }
    
    function xmltext() {
        $ret = $this->innertext();
        $ret = str_ireplace('<![CDATA[', '', $ret);
        $ret = str_replace(']]>', '', $ret);
        return $ret;
    }

    function makeup() {
        if (isset($this->_[SHW_INFO_TEXT])) return $this->dom->restore_noise($this->_[SHW_INFO_TEXT]);

        $ret = '<'.$this->tag;
        $i = -1;

        foreach($this->attr as $key=>$val) {
            ++$i;

            if ($val===null || $val===false)
                continue;

            $ret .= $this->_[SHW_INFO_SPACE][$i][0];
            if ($val===true)
                $ret .= $key;
            else {
                switch($this->_[SHW_INFO_QUOTE][$i]) {
                    case SHW_QUOTE_DOUBLE: $quote = '"'; break;
                    case SHW_QUOTE_SINGLE: $quote = '\''; break;
                    default: $quote = '';
                }
                $ret .= $key.$this->_[SHW_INFO_SPACE][$i][1].'='.$this->_[SHW_INFO_SPACE][$i][2].$quote.$val.$quote;
            }
        }
        $ret = $this->dom->restore_noise($ret);
        return $ret . $this->_[SHW_INFO_ENDSPACE] . '>';
    }

    function find($selector, $idx=null) {
        $selectors = $this->parse_selector($selector);
        if (($count=count($selectors))===0) return array();
        $found_keys = array();

        for ($c=0; $c<$count; ++$c) {
            if (($levle=count($selectors[0]))===0) return array();
            if (!isset($this->_[SHW_INFO_BEGIN])) return array();

            $head = array($this->_[SHW_INFO_BEGIN]=>1);

            for ($l=0; $l<$levle; ++$l) {
                $ret = array();
                foreach($head as $k=>$v) {
                    $n = ($k===-1) ? $this->dom->root : $this->dom->nodes[$k];
                    $n->seek($selectors[$c][$l], $ret);
                }
                $head = $ret;
            }

            foreach($head as $k=>$v) {
                if (!isset($found_keys[$k]))
                    $found_keys[$k] = 1;
            }
        }

        ksort($found_keys);

        $found = array();
        foreach($found_keys as $k=>$v)
            $found[] = $this->dom->nodes[$k];

        if (is_null($idx)) return $found;
		else if ($idx<0) $idx = count($found) + $idx;
        return (isset($found[$idx])) ? $found[$idx] : null;
    }

    protected function seek($selector, &$ret) {
        list($tag, $key, $val, $exp, $no_key) = $selector;

        if ($tag && $key && is_numeric($key)) {
            $count = 0;
            foreach ($this->children as $c) {
                if ($tag==='*' || $tag===$c->tag) {
                    if (++$count==$key) {
                        $ret[$c->_[SHW_INFO_BEGIN]] = 1;
                        return;
                    }
                }
            } 
            return;
        }

        $end = (!empty($this->_[SHW_INFO_END])) ? $this->_[SHW_INFO_END] : 0;
        if ($end==0) {
            $parent = $this->parent;
            while (!isset($parent->_[SHW_INFO_END]) && $parent!==null) {
                $end -= 1;
                $parent = $parent->parent;
            }
            $end += $parent->_[SHW_INFO_END];
        }

        for($i=$this->_[SHW_INFO_BEGIN]+1; $i<$end; ++$i) {
            $node = $this->dom->nodes[$i];
            $pass = true;

            if ($tag==='*' && !$key) {
                if (in_array($node, $this->children, true))
                    $ret[$i] = 1;
                continue;
            }

            if ($tag && $tag!=$node->tag && $tag!=='*') {$pass=false;}
            if ($pass && $key) {
                if ($no_key) {
                    if (isset($node->attr[$key])) $pass=false;
                }
                else if (!isset($node->attr[$key])) $pass=false;
            }
            if ($pass && $key && $val  && $val!=='*') {
                $check = $this->match($exp, $val, $node->attr[$key]);
                if (!$check && strcasecmp($key, 'class')===0) {
                    foreach(explode(' ',$node->attr[$key]) as $k) {
                        $check = $this->match($exp, $val, $k);
                        if ($check) break;
                    }
                }
                if (!$check) $pass = false;
            }
            if ($pass) $ret[$i] = 1;
            unset($node);
        }
    }

    protected function match($exp, $pattern, $value) {
        switch ($exp) {
            case '=':
                return ($value===$pattern);
            case '!=':
                return ($value!==$pattern);
            case '^=':
                return preg_match("/^".preg_quote($pattern,'/')."/", $value);
            case '$=':
                return preg_match("/".preg_quote($pattern,'/')."$/", $value);
            case '*=':
                if ($pattern[0]=='/')
                    return preg_match($pattern, $value);
                return preg_match("/".$pattern."/i", $value);
        }
        return false;
    }

    protected function parse_selector($selector_string) {
        $pattern = "/([\w-:\*]*)(?:\#([\w-]+)|\.([\w-]+))?(?:\[@?(!?[\w-]+)(?:([!*^$]?=)[\"']?(.*?)[\"']?)?\])?([\/, ]+)/is";
        preg_match_all($pattern, trim($selector_string).' ', $matches, PREG_SET_ORDER);
        $selectors = array();
        $result = array();

        foreach ($matches as $m) {
            $m[0] = trim($m[0]);
            if ($m[0]==='' || $m[0]==='/' || $m[0]==='//') continue;
            if ($m[1]==='tbody') continue;

            list($tag, $key, $val, $exp, $no_key) = array($m[1], null, null, '=', false);
            if(!empty($m[2])) {$key='id'; $val=$m[2];}
            if(!empty($m[3])) {$key='class'; $val=$m[3];}
            if(!empty($m[4])) {$key=$m[4];}
            if(!empty($m[5])) {$exp=$m[5];}
            if(!empty($m[6])) {$val=$m[6];}

            if ($this->dom->lowercase) {$tag=strtolower($tag); $key=strtolower($key);}
            if (isset($key[0]) && $key[0]==='!') {$key=substr($key, 1); $no_key=true;}

            $result[] = array($tag, $key, $val, $exp, $no_key);
            if (trim($m[7])===',') {
                $selectors[] = $result;
                $result = array();
            }
        }
        if (count($result)>0)
            $selectors[] = $result;
        return $selectors;
    }

    function __get($name) {
        if (isset($this->attr[$name])) return $this->attr[$name];
        switch($name) {
            case 'outertext': return $this->outertext();
            case 'innertext': return $this->innertext();
            case 'plaintext': return $this->text();
            case 'xmltext': return $this->xmltext();
            default: return array_key_exists($name, $this->attr);
        }
    }

    function __set($name, $value) {
        switch($name) {
            case 'outertext': return $this->_[SHW_INFO_OUTER] = $value;
            case 'innertext':
                if (isset($this->_[SHW_INFO_TEXT])) return $this->_[SHW_INFO_TEXT] = $value;
                return $this->_[SHW_INFO_INNER] = $value;
        }
        if (!isset($this->attr[$name])) {
            $this->_[SHW_INFO_SPACE][] = array(' ', '', ''); 
            $this->_[SHW_INFO_QUOTE][] = SHW_QUOTE_DOUBLE;
        }
        $this->attr[$name] = $value;
    }

    function __isset($name) {
        switch($name) {
            case 'outertext': return true;
            case 'innertext': return true;
            case 'plaintext': return true;
        }
        return (array_key_exists($name, $this->attr)) ? true : isset($this->attr[$name]);
    }

    function __unset($name) {
        if (isset($this->attr[$name]))
            unset($this->attr[$name]);
    }

    function getAllAttributes() {return $this->attr;}
    function getAttribute($name) {return $this->__get($name);}
    function setAttribute($name, $value) {$this->__set($name, $value);}
    function hasAttribute($name) {return $this->__isset($name);}
    function removeAttribute($name) {$this->__set($name, null);}
    function getElementById($id) {return $this->find("#$id", 0);}
    function getElementsById($id, $idx=null) {return $this->find("#$id", $idx);}
    function getElementByTagName($name) {return $this->find($name, 0);}
    function getElementsByTagName($name, $idx=null) {return $this->find($name, $idx);}
    function parentNode() {return $this->parent();}
    function childNodes($idx=-1) {return $this->children($idx);}
    function firstChild() {return $this->first_child();}
    function lastChild() {return $this->last_child();}
    function nextSibling() {return $this->next_sibling();}
    function previousSibling() {return $this->prev_sibling();}
}

?>
<?php
define('SHW_TYPE_ELEMENT', 1);
define('SHW_TYPE_COMMENT', 2);
define('SHW_TYPE_TEXT',    3);
define('SHW_TYPE_ENDTAG',  4);
define('SHW_TYPE_ROOT',    5);
define('SHW_TYPE_UNKNOWN', 6);
define('SHW_QUOTE_DOUBLE', 0);
define('SHW_QUOTE_SINGLE', 1);
define('SHW_QUOTE_NO',     3);
define('SHW_INFO_BEGIN',   0);
define('SHW_INFO_END',     1);
define('SHW_INFO_QUOTE',   2);
define('SHW_INFO_SPACE',   3);
define('SHW_INFO_TEXT',    4);
define('SHW_INFO_INNER',   5);
define('SHW_INFO_OUTER',   6);
define('SHW_INFO_ENDSPACE',7);

require_once('shweta_simple_html_dom_node.php');
require_once('shweta_simple_html_dom.php');

function file_get_html() {
    $dom = new shweta_simple_html_dom;
    $args = func_get_args();
    $dom->load(call_user_func_array('file_get_contents', $args), true);
    return $dom;
}

function str_get_html($str, $lowercase=true) {
    $dom = new shweta_simple_html_dom;
    $dom->load($str, $lowercase);
    return $dom;
}

function dump_html_tree($node, $show_attr=true, $deep=0) {
    $lead = str_repeat('    ', $deep);
    echo $lead.$node->tag;
    if ($show_attr && count($node->attr)>0) {
        echo '(';
        foreach($node->attr as $k=>$v)
            echo "[$k]=>\"".$node->$k.'", ';
        echo ')';
    }
    echo "\n";

    foreach($node->nodes as $c)
        dump_html_tree($c, $show_attr, $deep+1);
}

function file_get_dom() {
    $dom = new shweta_simple_html_dom;
    $args = func_get_args();
    $dom->load(call_user_func_array('file_get_contents', $args), true);
    return $dom;
}

function str_get_dom($str, $lowercase=true) {
    $dom = new shweta_simple_html_dom;
    $dom->load($str, $lowercase);
    return $dom;
}

function save_image($img){
	$fullpath = 'image/'.strtolower(basename($img));
    $ch = curl_init ($img);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
    $rawdata=curl_exec($ch);
    curl_close ($ch);
    if(file_exists($fullpath)){
        unlink($fullpath);
    }
    $fp = fopen($fullpath,'x');
    fwrite($fp, $rawdata);
    fclose($fp);
}

?>
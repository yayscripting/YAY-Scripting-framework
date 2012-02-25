<?php /* Smarty version Smarty-3.0.7, created on 2012-02-25 11:02:54
         compiled from "application/views/en/errors/404.tpl" */ ?>
<?php /*%%SmartyHeaderCode:11657680804f48b1ce619bb5-94628058%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e32cd5b479645903b1ae26389ae097e1dd6b3af1' => 
    array (
      0 => 'application/views/en/errors/404.tpl',
      1 => 1329926858,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '11657680804f48b1ce619bb5-94628058',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<h1>404 Not Found</h1>

<p>
The server cannot found the resource you are looking for.
</p>
<?php if ($_smarty_tpl->getVariable('error')->value!=''){?>
<p><em><?php echo $_smarty_tpl->getVariable('error')->value;?>
</em></p>
<?php }?>
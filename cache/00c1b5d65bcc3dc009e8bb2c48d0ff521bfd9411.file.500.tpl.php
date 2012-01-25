<?php /* Smarty version Smarty-3.0.7, created on 2012-01-25 17:41:13
         compiled from "application/views/errors/500.tpl" */ ?>
<?php /*%%SmartyHeaderCode:11466033964f2030a90a4db6-84199675%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '00c1b5d65bcc3dc009e8bb2c48d0ff521bfd9411' => 
    array (
      0 => 'application/views/errors/500.tpl',
      1 => 1327160058,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '11466033964f2030a90a4db6-84199675',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<h1>Internal server error</h1>

<p>
We encountered an internal server error. Please try again later.
</p>
<?php if ($_smarty_tpl->getVariable('error')->value!=''){?>
<p><em><?php echo $_smarty_tpl->getVariable('error')->value;?>
</em></p>
<?php }?>
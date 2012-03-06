<?php /* Smarty version Smarty-3.0.7, created on 2012-03-06 19:57:35
         compiled from "application/views/en/errors/500.tpl" */ ?>
<?php /*%%SmartyHeaderCode:4227321644f565e1f235b79-93249297%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a2c25f225d37902378e89bdd4385b60bac66e409' => 
    array (
      0 => 'application/views/en/errors/500.tpl',
      1 => 1329926862,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '4227321644f565e1f235b79-93249297',
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
<?php /* Smarty version Smarty-3.0.7, created on 2012-01-25 16:26:00
         compiled from "application/views/pages/home.tpl" */ ?>
<?php /*%%SmartyHeaderCode:3697771944f201f08513330-48004317%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8de64e19592fe054054889e31d9683ed9dfba11e' => 
    array (
      0 => 'application/views/pages/home.tpl',
      1 => 1325937470,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3697771944f201f08513330-48004317',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>


<h1>Login</h1>
<?php echo $_smarty_tpl->getVariable('_box')->value;?>


You can login into the admin panel with this form.
<form method="post" action="/home.html">
<?php echo $_smarty_tpl->getVariable('form')->value['form'];?>

	<table>
		<tr>
			<td>Username</td>
			<td><?php echo $_smarty_tpl->getVariable('form')->value['username'][0];?>
<small>Hint: its admin</small></td>
		</tr>
		<tr>
			<td>Password</td>
			<td><?php echo $_smarty_tpl->getVariable('form')->value['password'][0];?>
<small>Hint: its password</small></td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" value="Login" /></td>
	</table>

</form>
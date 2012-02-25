<?php /* Smarty version Smarty-3.0.7, created on 2012-02-25 11:02:53
         compiled from "application/views/en/pages/home.tpl" */ ?>
<?php /*%%SmartyHeaderCode:18705702244f48b1cd6d3aa3-50697947%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9c421734db696a603a8b7c7f8267bb37d16fc7cd' => 
    array (
      0 => 'application/views/en/pages/home.tpl',
      1 => 1329927665,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '18705702244f48b1cd6d3aa3-50697947',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>


<h1>Login</h1>
<?php echo $_smarty_tpl->getVariable('_box')->value;?>


<p>Via the form below you are enabled to access the adminstrator environment.</p>

<p><small>Since the Home controller is the default controller (see applicatoin/config/script.cfg.php) and the option 'force_seo' is enabled the Router won't accept /home.html as GET request. You can still send POST requests but we recommend not to.</small></p>
<form method="post" action="/<?php echo $_smarty_tpl->getVariable('lang')->value;?>
.html">
<?php echo $_smarty_tpl->getVariable('form')->value['form'];?>

	<table>
		<tr>
			<td>Username</td>
			<td><?php echo $_smarty_tpl->getVariable('form')->value['username'][0];?>
<small>Hint: it is admin</small></td>
		</tr>
		<tr>
			<td>Password</td>
			<td><?php echo $_smarty_tpl->getVariable('form')->value['password'][0];?>
<small>Hint: it is password</small></td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" value="Login" /></td>
	</table>

</form>
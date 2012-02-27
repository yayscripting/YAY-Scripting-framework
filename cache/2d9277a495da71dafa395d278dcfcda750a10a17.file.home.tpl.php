<?php /* Smarty version Smarty-3.0.7, created on 2012-02-27 11:42:24
         compiled from "application/views/nl/pages/home.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1658271464f4b5e103dfbc1-15798856%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2d9277a495da71dafa395d278dcfcda750a10a17' => 
    array (
      0 => 'application/views/nl/pages/home.tpl',
      1 => 1329928997,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1658271464f4b5e103dfbc1-15798856',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>


<h1>Inloggen</h1>
<?php echo $_smarty_tpl->getVariable('_box')->value;?>


<p>Via het onderstaande formulier kunt u inloggen tot het beheerderspaneel.</p>

<p><small>Omdat de hoofdcontroller 'home' is(zie: applicatoin/config/script.cfg.php) en de optie 'force_seo' aan staat, zal de router /home.html niet als een GET-request accepteren. POST-requests zullen nog steeds werken, maar dit raden wij af.</small></p>
<form method="post" action="/<?php echo $_smarty_tpl->getVariable('lang')->value;?>
.html">
<?php echo $_smarty_tpl->getVariable('form')->value['form'];?>

	<table>
		<tr>
			<td>Gebruikersnaam</td>
			<td><?php echo $_smarty_tpl->getVariable('form')->value['username'][0];?>
<small>Hint: het is 'admin'</small></td>
		</tr>
		<tr>
			<td>Wachtwoord</td>
			<td><?php echo $_smarty_tpl->getVariable('form')->value['password'][0];?>
<small>Hint: het is 'password'</small></td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" value="Inloggen" /></td>
	</table>

</form>
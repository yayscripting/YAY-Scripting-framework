{* This template works with Smarty. The documentation of the Smarty template parser can be found on the Smarty website. *}

<h1>Login</h1>

{* In case of an error or success, this variable will echo a box, containing this error. *}
{$_box}

<p>Via the form below you are enabled to access the adminstrator environment.</p>

<p><small>Since the Home controller is the default controller (see applicatoin/config/script.cfg.php) and the option 'force_seo' is enabled the Router won't accept /home.html as GET request. You can still send POST requests but we recommend not to.</small></p>
<form method="post" action="/{$lang}.html">

{* This line is required, because it makes sure that the form-parser always picks the right form when there are more than 1 form on one page. *}
{$form.form}

	{* For nice outlining *}
	<table>
		<tr>
			<td>Username</td>
			{* $form.username[0] contains an HTML-element. If you had radio-buttons with the same name, for example, they would be found under $form.name[0] and $form.name[1] *}
			<td>{$form.username[0]}<small>Hint: it is admin</small></td>
		</tr>
		<tr>
			<td>Password</td>
			<td>{$form.password[0]}<small>Hint: it is password</small></td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" value="Login" /></td>
	</table>

</form>
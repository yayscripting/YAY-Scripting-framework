{* This template works with Smarty. The documentation of the Smarty template parser can be found on the Smarty website. *}

<h1>Login</h1>

{* In case of an error or success, this variable will echo a box, containing this error. *}
{$_box}

You can login into the admin panel with this form.
<form method="post" action="/home.html">

{* This line is required, because it makes sure that the form-parser always picks the right form when there are more than 1 form on one page. *}
{$form.form}

	{* For nice outlining *}
	<table>
		<tr>
			<td>Username</td>
			{* $form.username[0] contains an HTML-element. If you had radio-buttons with the same name, for example, they would be found under $form.name[0] and $form.name[1] *}
			<td>{$form.username[0]}<small>Hint: its admin</small></td>
		</tr>
		<tr>
			<td>Password</td>
			<td>{$form.password[0]}<small>Hint: its password</small></td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" value="Login" /></td>
	</table>

</form>
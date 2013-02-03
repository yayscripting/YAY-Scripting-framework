{* This template works with Smarty. The documentation of the Smarty template parser can be found on the Smarty website. *}

<h1>Inloggen</h1>

{* In case of an error or success, this variable will echo a box, containing this error. *}
{$_box}

<p>Via het onderstaande formulier kunt u inloggen tot het beheerderspaneel.</p>

<p><small>Omdat de hoofdcontroller 'home' is(zie: applicatoin/config/script.cfg.php) en de optie 'force_seo' aan staat, zal de router /home.html niet als een GET-request accepteren. POST-requests zullen nog steeds werken, maar dit raden wij af.</small></p>
<form method="post" action="">

{* This line is required, because it makes sure that the form-parser always picks the right form when there are more than 1 form on one page. *}
{$form.form}

	{* For nice outlining *}
	<table>
		<tr>
			<td>Gebruikersnaam</td>
			{* $form.username[0] contains an HTML-element. If you had radio-buttons with the same name, for example, they would be found under $form.name[0] and $form.name[1] *}
			<td>{$form.username[0]}<small>Hint: het is 'admin'</small></td>
		</tr>
		<tr>
			<td>Wachtwoord</td>
			<td>{$form.password[0]}<small>Hint: het is 'password'</small></td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" value="Inloggen" /></td>
	</table>

</form>
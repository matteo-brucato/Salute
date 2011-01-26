<form method="post" action="/homepage/login" id="login-form">
	<fieldset>
	<legend>Login</legend>
	<label for="firstname">E-mail</label><br />
	<input type="text" name="email" id="email-input" /><br />
	<label for="lastname">Pasword</label><br />
	<input type="password" name="password" id="password-input" />
	<p>
		<input type="submit" name="submit" value="Submit" id="submit-button" />
	</p>
	</fieldset>
</form>
<div><a href="/homepage/retrieve_password" class="ajaxlink">Forgot your password?</a></div>
<div>Don't have an account? <a href="/homepage/register" class="ajaxlink">Register</a></div>

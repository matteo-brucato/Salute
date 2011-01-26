<form method="post" action="/homepage/login" id="login-form">
	<fieldset>
	<legend>Login</legend>
	<label for="email-input">E-mail</label><br />
	<input type="text" name="email" id="email-input" class="input-field" /><br />
	<label for="password-input">Pasword</label><br />
	<input type="password" name="password" id="password-input" class="input-field" />
	<p>
		<input type="submit" name="submit" value="Submit" class="submit-button" />
	</p>
	</fieldset>
</form>
<div><a href="/homepage/retrieve_password" class="ajaxlink">Forgot your password?</a></div>
<div>Don't have an account? <a href="/homepage/register" class="ajaxlink">Register</a></div>

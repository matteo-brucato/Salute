<form method="post" action="/settings/change_email_do" id="change-email">
	<fieldset id="change-email-fieldset">
		<table>
			<tr>
				<td><label for="email-input">Please Enter New Email:</label></td>
				<td><input type="text" name="email" class="input-field" value="<?php echo $this->auth->get_email() ;?>" /><br /></td>
			</tr>
		</table>
		<p>
			<input type="submit" name="submit" value="Submit" class="submit-button" />
		</p>
	</fieldset>
</form>


<form method="post" action="/profile/edit_do" id="edit-info">
	<fieldset id="edit-info-fieldset">
		<legend>Edit Info</legend>
		<table>
			<tr>
				<td><label for="firstname-input">First Name</label></td>
				<td><input type="text" name="firstname" class="input-field" /><br /></td>
			</tr>
			
			<tr>
				<td><label for="middlename-input">Middle Name</label></td>
				<td><input type="text" name="middlename" class="input-field" /><br /></td>
			</tr>
			<tr>
				<td><label for="lastname-input">Last Name</label></td>
				<td><input type="text" name="lastname" class="input-field" /><br /></td>
			</tr>
			<tr>
				<td><label for="dob-input">Date of birth</label></td>
				<td><input type="text" name="dob" class="input-field" /><br /></td>
			</tr>
			<tr>
				<td><label for="sex-input">Sex</label></td>
				<td><input type="text" name="sex" class="input-field" /><br /></td>
			</tr>
			<tr>
				<td><label for="tel-input">Tel. number</label></td>
				<td><input type="text" name="tel" class="input-field" /><br /></td>
			</tr>
			<tr>
				<td><label for="fax-input">Fax. number</label></td>
				<td><input type="text" name="fax" class="input-field" /><br /></td>
			</tr>
			<tr>
				<td><label for="address-input">Address</label></td>
				<td><input type="text" name="address" class="input-field" /><br /></td>
			</tr>
		</table>
		
		<p>
			<input type="submit" name="submit" value="Submit" class="submit-button" />
			<input type="reset" />
		</p>
	</fieldset>
</form>

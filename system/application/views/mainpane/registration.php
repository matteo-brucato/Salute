<div id="type-selection">
	Select the type of account you want to create:
	<br />
	<a href="javascript:show_patient_form();">Patient</a><br />
	<a href="javascript:show_hcp_form();">Health Care Provider</a>
</div>

<form method="post" action="/home/register_do" id="registration-form">
	<fieldset id="registration-patient-form">
		<legend>Register to <?php echo SITENAME; ?> as a patient</legend>
		<table>
			<tr>
				<td><label for="email-input">Email</label></td>
				<td><input type="text" name="email" class="input-field" /><br /></td>
			</tr>
			<tr>
				<td><label for="password-input">Password</label></td>
				<td><input type="password" name="password" class="input-field" /><br /></td>
			</tr>
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
				<td><label for="ssn-input">Ssn</label></td>
				<td><input type="text" name="ssn" class="input-field" /><br /></td>
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
		</p>
	</fieldset>
	<fieldset id="registration-hcp-form">
		<legend>Register to <?php echo SITENAME; ?> as an Health Care Provider</legend>
			<table>
			<tr>
				<td><label for="email-input">Email</label></td>
				<td><input type="text" name="email" class="input-field" /><br /></td>
			</tr>
			<tr>
				<td><label for="password-input">Password</label></td>
				<td><input type="password" name="password" class="input-field" /><br /></td>
			</tr>
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
				<td><label for="ssn-input">Ssn</label></td>
				<td><input type="text" name="ssn" class="input-field" /><br /></td>
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
				<td><label for="spec-input">Specialization</label></td>
				<td><input type="text" name="spec" class="input-field" /><br /></td>
			</tr>
			<tr>
				<td><label for="org-input">Organization name</label></td>
				<td><input type="text" name="org" class="input-field" /><br /></td>
			</tr>
			<tr>
				<td><label for="address-input">Address</label></td>
				<td><input type="text" name="address" class="input-field" /><br /></td>
			</tr>
		</table>
		
		<p>
			<input type="submit" name="submit" value="Submit" class="submit-button" />
		</p>
	</fieldset>
</form>

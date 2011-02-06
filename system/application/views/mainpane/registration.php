<div id="type-selection">
	Select the type of account you want to create:
	
	<a href="javascript:show_patient_form();">Patient</a>
	<a href="javascript:show_hcp_form();">Health Care Provider</a>
</div>

<form method="post" action="/home/register_do/patient" id="registration-patient">
	<fieldset id="registration-patient-fieldset">
		<legend>Register to <?php echo SITENAME; ?> as a patient</legend>
		<table>
			<tr>
				<td><label for="email-input">Email</label></td>
				<td><input type="text" name="email" class="input-field" /></td>
			</tr>
			<tr>
				<td><label for="password-input">Password</label></td>
				<td><input type="password" name="password" class="input-field" /></td>
			</tr>
			<tr>
				<td><label for="firstname-input">First Name</label></td>
				<td><input type="text" name="firstname" class="input-field" /></td>
			</tr>
			
			<tr>
				<td><label for="middlename-input">Middle Name</label></td>
				<td><input type="text" name="middlename" class="input-field" /></td>
			</tr>
			<tr>
				<td><label for="lastname-input">Last Name</label></td>
				<td><input type="text" name="lastname" class="input-field" /></td>
			</tr>
			<tr>
				<td><label for="ssn-input">Ssn</label></td>
				<td><input type="text" name="ssn" class="input-field" /></td>
			</tr>
			<tr>
				<td><label for="dob-input">Date of birth</label></td>
				<td><input type="text" name="dob" class="input-field" /></td>
			</tr>
			<tr>
				<td><label for="sex-input">Sex</label></td>
				<td><input type="text" name="sex" class="input-field" /></td>
			</tr>
			<tr>
				<td><label for="tel-input">Tel. number</label></td>
				<td><input type="text" name="tel" class="input-field" /></td>
			</tr>
			<tr>
				<td><label for="fax-input">Fax. number</label></td>
				<td><input type="text" name="fax" class="input-field" /></td>
			</tr>
			<tr>
				<td><label for="address-input">Address</label></td>
				<td><input type="text" name="address" class="input-field" /></td>
			</tr>
		</table>
		
		<p>
			<input type="submit" name="submit" value="Submit" class="submit-button" />
			<input type="reset" />
		</p>
	</fieldset>
</form>

<form method="post" action="/home/register_do/hcp" id="registration-hcp">
	<fieldset id="registration-hcp-fieldset">
		<legend>Register to <?php echo SITENAME; ?> as an Health Care Provider</legend>
			<table>
			<tr>
				<td><label for="email-input">Email</label></td>
				<td><input type="text" name="email" class="input-field" /></td>
			</tr>
			<tr>
				<td><label for="password-input">Password</label></td>
				<td><input type="password" name="password" class="input-field" /></td>
			</tr>
			<tr>
				<td><label for="firstname-input">First Name</label></td>
				<td><input type="text" name="firstname" class="input-field" /></td>
			</tr>
			
			<tr>
				<td><label for="middlename-input">Middle Name</label></td>
				<td><input type="text" name="middlename" class="input-field" /></td>
			</tr>
			<tr>
				<td><label for="lastname-input">Last Name</label></td>
				<td><input type="text" name="lastname" class="input-field" /></td>
			</tr>
			<tr>
				<td><label for="ssn-input">Ssn</label></td>
				<td><input type="text" name="ssn" class="input-field" /></td>
			</tr>
			<tr>
				<td><label for="dob-input">Date of birth</label></td>
				<td><input type="text" name="dob" class="input-field" /></td>
			</tr>
			<tr>
				<td><label for="sex-input">Sex</label></td>
				<td><input type="text" name="sex" class="input-field" /></td>
			</tr>
			<tr>
				<td><label for="tel-input">Tel. number</label></td>
				<td><input type="text" name="tel" class="input-field" /></td>
			</tr>
			<tr>
				<td><label for="fax-input">Fax. number</label></td>
				<td><input type="text" name="fax" class="input-field" /></td>
			</tr>
			<tr>
				<td><label for="spec-input">Specialization</label></td>
				<td><input type="text" name="spec" class="input-field" /></td>
			</tr>
			<tr>
				<td><label for="org-input">Organization name</label></td>
				<td><input type="text" name="org" class="input-field" /></td>
			</tr>
			<tr>
				<td><label for="address-input">Address</label></td>
				<td><input type="text" name="address" class="input-field" /></td>
			</tr>
		</table>
		
		<p>
			<input type="submit" name="submit" value="Submit" class="submit-button" />
			<input type="reset" />
		</p>
	</fieldset>
</form>

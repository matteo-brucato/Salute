<h2>Delete access to this medical record to an HCP</h2>

<form method="post" action="/medical_records/remove_permission_do/<?php echo $medrec_id;?>" id="remove-permission">
	<fieldset id="remove-permission-fieldset">
		<legend>Add permission</legend>
		<table>
			<tr>
				<td><label for="account_id">HCP id</label></td>
				<td><input type="text" name="account_id" class="input-field" /></td>
			</tr>
		</table>
		
		<p>
			<input type="submit" name="submit" value="Submit" class="submit-button" />
			<input type="reset" />
		</p>
	</fieldset>
</form>

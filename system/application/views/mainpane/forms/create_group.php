<form method="post" action="/groups/create" id="create">
	<fieldset id="create-fieldset">
		<legend>Create New Group</legend>
		<table>
			<tr>
				<td><label for="name-input">Group Name</label></td>
				<td><input type="text" name="name" class="input-field" /></td>
			</tr>
			<tr>
				<td><label for="description-input">Description</label></td>
				<td><input type="text" name="description" class="input-field" /></td>
			</tr>
			<tr>
				<td><label for="privacy-input">Privacy</label></td>
				<td><input type="text" name="public_private" class="input-field" /></td>
			</tr>
			
			<tr>
				<td><label for="grouptype-input">Group Type</label></td>
				<td><input type="text" name="group_type" class="input-field" /></td>
			</tr>
		</table>
		<p>
			<input type="submit" name="submit" value="Submit" class="submit-button" />
			<input type="reset" />
		</p>
	</fieldset>
</form>

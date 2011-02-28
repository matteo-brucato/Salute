<form method="post" action="/groups/edit_do" id="edit-group">
	<fieldset id="edit-group-fieldset">
		<legend>Edit Group</legend>
		<table>
			<tr>
				<td><label for="name-input">Group Name</label></td>
				<td><input type="text" name="name" class="input-field" 
							value="<?php echo $curr_info['name'];?>" /><br /></td>
			</tr>
			<tr>
				<td><label for="description-input">Description</label></td>
				<td><textarea name="description" cols="50" rows="5" wrap="hard">
								<?php echo $curr_info['description'];?></textarea><br /></td>
			</tr>
			<tr>
				<td><label for="privacy-input">Privacy</label></td>
				<td>
					<input type="radio" name="public_private" value="0">Public<br>
					<input type="radio" name="public_private" value="1">Private<br>
				</td>
			</tr>
			<tr>
				<td><label for="grouptype-input">Group Type</label></td>
				<td>
					<input type="radio" name="group_type" value="0">Patients Only<br>
					<input type="radio" name="group_type" value="1">Healthcare Providers Only<br>
					<input type="radio" name="group_type" value="2">Patients and Healthcare Providers<br>
				</td>
			</tr>
		</table>
		<p>
			<input type="submit" name="submit" value="Submit" class="submit-button" />
			<input type="reset" />
		</p>
	</fieldset>
</form>

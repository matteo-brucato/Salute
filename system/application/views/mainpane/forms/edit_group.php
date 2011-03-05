<form method="post" action="/groups/edit_do/<?php echo $group_id; ?>" id="edit-group">
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
				<td><textarea name="description" cols="50" rows="5" wrap="hard"><?php echo $curr_info['description'];?></textarea><br /></td>
			</tr>
		</table>
		<p>
			<input type="submit" name="submit" value="Submit" class="submit-button" />
			<input type="reset" />
		</p>
	</fieldset>
</form>


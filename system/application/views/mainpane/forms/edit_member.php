<form method="post" action="/groups/members/edit_do/<? echo $group_id; ?>/<? echo $account_id; ?>" id="edit-member">
	<fieldset id="edit-member-fieldset">
		<legend>Edit Member</legend>
			<td><label for="permissions-input"><b>Permission level:</b></label></td><br>
			<td>
				<input type="radio" name="permissions" value="0" 
					<?php if($curr_info['permissions'] == '0') echo 'checked="checked"';?> />Request Connection Only<br>
				<input type="radio" name="permissions" value="1"
					<?php if($curr_info['permissions'] == '1') echo 'checked="checked"';?> />Request Connection and Invite<br>
				<input type="radio" name="permissions" value="2"
					<?php if($curr_info['permissions'] == '2') echo 'checked="checked"';?> />Request Connection, Invite, Edit Members, and Delete Members<br>
				<input type="radio" name="permissions" value="3"
					<?php if($curr_info['permissions'] == '3') echo 'checked="checked"';?> />Request Connection, Invite, Edit Members,Delete Members, Edit Group, Delete Group<br>
			</td>
		</tr>
		<p>
			<input type="submit" name="submit" value="Submit" class="submit-button" />
			<input type="reset" />
		</p>
	</fieldset>
</form>

<form method="post" action="/groups/members/edit_do/<? echo $group['group_id']; ?>/<? echo $account_id; ?>" id="edit-member">
	<fieldset id="edit-member-fieldset">
		<legend>Edit Member Permissions</legend>
			<td><label for="permissions-input"><b>Permission level:</b></label></td><br>
			<td>
				<input type="radio" name="permissions" value="0" 
					<?php if($curr_info['permissions'] == '0') echo 'checked="checked"';?>
					<?php if($curr_info['account_id'] == $group['account_id']) echo 'disabled="disable"';?>
					 /> 0<br>
				<input type="radio" name="permissions" value="1"
					<?php if($curr_info['permissions'] == '1') echo 'checked="checked"';?>
					<?php if($curr_info['account_id'] == $group['account_id']) echo 'disabled="disable"';?>
					 /> 1<br>
				<input type="radio" name="permissions" value="2"
					<?php if($curr_info['permissions'] == '2') echo 'checked="checked"';?>
					<?php if($curr_info['account_id'] == $group['account_id']) echo 'disabled="disable"';?>
					 /> 2<br>
				<input type="radio" name="permissions" value="3"
					<?php if($curr_info['permissions'] ==  '3') echo 'checked="checked"';?>
					<?php if($curr_info['account_id'] == $group['account_id']) echo 'disabled="disable"';?>
					 /> 3<br>
			</td>
		</tr>
		<br />
		<p>
			<input type="submit" name="submit" value="Submit" class="submit-button" />
			<input type="reset" />
		</p>
	</fieldset>
</form>

<br />
<table>
	<tr>
		<th colspan="2">Legend</td>
	</tr>
	<tr>
		<td width="50"><b>Level 0:</b></td>
		<td>Allow member to make connection requests to other members in this group</td>
	</tr>
	<tr>
		<td width="50"><b>Level 1:</b></td>
		<td>Level 0 + allow member to invite their connected friends to this group</td>
	</tr>
	<tr>
		<td width="50"><b>Level 2:</b></td>
		<td>Level 1 + allow member to change other member's permissions up to your level and delete other members from this group</td>
	</tr>
	<tr>
		<td width="50"><b>Level 3:</b></td>
		<td>Level 2 + allow member to edit and delete this group (Administrator Privileges)</td>
	</tr>
</table>

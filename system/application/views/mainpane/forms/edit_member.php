<form method="post" action="/groups/members/edit_do" id="edit-member">
	<fieldset id="edit-member-fieldset">
		<legend>Edit Member</legend>
			<td><label for="permissions-input"><b>Permission level:</b></label></td><br>
			<td>
				<input type="radio" name="permissions" value="0">Post Only<br>
				<input type="radio" name="permissions" value="1">Post and Invite<br>
				<input type="radio" name="permissions" value="2">Post,Invite, Edit Members, and Delete Members<br>
				<input type="radio" name="permissions" value="3">Post,Invite, Edit Members,Delete Members, Edit Group, Delete Group<br>
			</td>
		</tr>
		<p>
			<input type="submit" name="submit" value="Submit" class="submit-button" />
			<input type="reset" />
		</p>
	</fieldset>
</form>

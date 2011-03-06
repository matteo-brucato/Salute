<form method="post" action="/upload/account_picture/<?php echo $this->auth->get_account_id(); ?>" enctype="multipart/form-data" class="noajax">
<fieldset id="upload-medrec-fd">
<legend>Change Picture</legend>
<table>
	<tr>
		<td ><img class="profile-picture" src="/download/account_picture/<?php echo $this->auth->get_account_id(); ?>"></td>
	</tr>
	<tr>
		<td>NOTE: the file must be a .jpg of 240x180 pixels, max 20KB size</td>
	</tr>
	<tr>
		<td><input type="file" name="userfile" size="20" /></td>
	</tr>
	<tr>
		<td><a href="/settings/remove_picture/<?php echo $this->auth->get_account_id(); ?>" class="noajax confirm">Remove Picture</a></td>
	</tr>
</table>

<br /><br />

<input type="submit" value="Upload" />

</fieldset>
</form>

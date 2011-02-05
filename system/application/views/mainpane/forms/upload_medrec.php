<h2>Upload a medical record</h2>

<?php echo form_open_multipart('upload/upload_do/medical_records');?>

<input type="hidden" name="patient_id" value="<?php echo $patient_id;?>" /><br />

<table>
	<tr>
		<td>Issue: <input type="text" name="issue" /><br /></td>
		<td>Info: <input type="text" name="info" /><br /></td>
		<td><input type="file" name="userfile" size="20" /></td>
	</tr>
</table>

<br /><br />

<input type="submit" value="Upload" />

</form>

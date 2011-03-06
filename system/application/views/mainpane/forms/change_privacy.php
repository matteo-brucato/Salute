<form action="/settings/change_privacy_do/" method="post">
<fieldset>
	<legend>Change Privacy Level</legend>
		<td><label for="privacy-input"><b>Privacy Level:</b></label></td><br>
		<td>
		<br>		
				<input type="radio" name="level" value="0" 
					<?php if($privacy === TRUE) echo 'checked="checked"';?> /> Public<br><br>
				<input type="radio" name="level" value="1" 
					<?php if($privacy === FALSE) echo 'checked="checked"';?> /> Private<br><br>
		</td>
	<p>
	<input type="submit" value="Submit" />
	</p>
	
</fieldset>
</form>

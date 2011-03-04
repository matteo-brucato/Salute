<form action="/connections/change_level_do/<?php echo $aid;?>" method="post">
<fieldset>
	<legend>Change connection level</legend>
		<td><label for="permissions-input"><b>Permission level:</b></label></td><br>
		<td>
		<br>		
				<input type="radio" name="level" value="0" 
					<?php if($con_level === '0') echo 'checked="checked"';?> /> 0  Accept Referrals Manually, Give Permisssion to Medical Records Manually<br><br>
				<input type="radio" name="level" value="1" 
					<?php if($con_level === '1') echo 'checked="checked"';?> /> 1  Accept Referrals Manually, Give Permisssion to Medical Records Automatically<br><br>
				<input type="radio" name="level" value="2" 
					<?php if($con_level === '2') echo 'checked="checked"';?> /> 2  Accept Referrals Automatically, Give Permisssion to Medical Records Manually<br><br>
				<input type="radio" name="level" value="3" 
					<?php if($con_level === '3') echo 'checked="checked"';?> /> 3  Accept Referrals Automatically, Give Permisssion to Medical Records Automatically<br><br>
		</td>
	
	<p>
	<input type="submit" value="Submit" />
	</p>
	
</fieldset>
</form>

<form action="/appointments/request_do/<?php echo $hcp['account_id'];?>" method="post">
<fieldset>
	<legend>Request appointment to <?php echo $hcp['first_name'].' '.$hcp['last_name'];?></legend>
	
	<label for="description">Description</label><br />
	<input type="text" name="description" class="input-field" /><br />
	<label for="time">Time</label><br />
	<input type="text" name="time" value="YYYY-MM-DD hh:mm:ss" class="input-field" />
	
	<br /><br />
	
	<input type="submit" value="Submit" />
	
</fieldset>
</form>

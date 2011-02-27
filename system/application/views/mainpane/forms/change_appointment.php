<form action="/appointments/reschedule_do/<?php echo $app['appointment_id'];?>" method="post">
<fieldset>
	<legend>Reschedule appointment <?php echo $app['descryption'];?></legend>
	
	<label for="time">Time</label><br />
	<input type="text" name="time" value="<?php echo $app['date_time'];?>" class="input-field" />
	
	<br /><br />
	
	<input type="submit" value="Submit" />
	
</fieldset>
</form>

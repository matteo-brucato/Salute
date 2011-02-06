<form method="post" action="/messages/send/<?php echo $acc['account_id'];?>" id="send-message">
	<fieldset id="send-message-fieldset">
		<legend>Compose Email</legend>
		
		<b>To: </b><?php echo $info['first_name'].' '.$info['middle_name'].' '.$info['last_name'].' &lt;'.$acc['email'].'&gt;';?>
			
		<br/><label for="subject">Subject: </label>
		<input type="text" name="subject" class="input-field"/><br />
		<textarea name="body" cols="70" rows="10" wrap="hard">Write your message here...</textarea>
		
		<p>
			<input type="submit" name="submit" value="Send" class="submit-button" />
		</p>
	</fieldset>
</form>


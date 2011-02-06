<form method="post" action="/messages/send/<?php echo $acc['account_id'];?>" id="send-message">
	<fieldset id="send-message-fieldset">
		<legend>Compose Email</legend>
		<table>
			<tr>
				<td><body><b>To: </b><?php echo $info['first_name'].' '.$info['middle_name'].' '.$info['last_name'].' &lt;'.$acc['email'].'&gt;';?></body></td>
			</tr>
			<tr>
				<td colspan="2"><textarea name="body" cols="70" rows="10" wrap="hard">Write your message here...</textarea></td>
			</tr>
		</table>
		<p>
			<input type="submit" name="submit" value="Send" class="submit-button" />
		</p>
	</fieldset>
</form>


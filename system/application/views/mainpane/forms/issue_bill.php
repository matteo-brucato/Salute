<h2>Issue Bill</h2>


<form method="post" action="/bills/issue_new_bill" id="issue-bill">
	<fieldset id="issue-bill-fieldset">
		<legend> Issue bill to <?php echo $results[0]['first_name'].' '.$results[0]['last_name'];?> </legend>
		<input type="hidden" name="patient_id" value="<?php echo $results[0]['account_id'];?>" />
		<table>
			<tr>
				<td><label for="amount-input">Amount(e.g. <i>20</i> or <i>20.00</i>)</label></td>
				<td><input type="text" name="amount" class="input-field" /><br /></td>
			</tr>			<tr>
				<td><label for="description-input">Description(e.g. <i>check up</i>)</label></td>
				<td><input type="text" name="descryption" class="input-field" /><br /></td>
			</tr>			<tr>
				<td><label for="date-input">Due Date(e.g. <i>yyyy-mm-dd</i> or <i>yyyy-mm-dd hh:mm:ss</i> using army time)</label></td>
				<td><input type="text" name="due_date" class="input-field" /><br /></td>
			</tr>
		</table>
		
		<p>
			<input type="submit" name="submit" value="Submit" class="submit-button" />
			<input type="reset" />
		</p>
	</fieldset>
</form>



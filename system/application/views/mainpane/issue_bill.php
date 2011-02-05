<h2>Issue Bill</h2>


<form method="post" action="/bills/issue_new_bill" id="issue-bill">
	<fieldset id="issue-bill-fieldset">
		<legend> Issue bill to <?php echo $results['first_name'].' '.$results['last_name'];?> </legend>
		<input type="hidden" name="patient_id" value="<?php echo $results['patient_id'];?>" />
		<table>
			<tr>
				<td><label for="amount-input">Amount</label></td>
				<td><input type="text" name="amount" class="input-field" /><br /></td>
			</tr>			<tr>
				<td><label for="description-input">Description</label></td>
				<td><input type="text" name="descryption" class="input-field" /><br /></td>
			</tr>			<tr>
				<td><label for="date-input">Date</label></td>
				<td><input type="text" name="due_date" class="input-field" /><br /></td>
			</tr>
		</table>
		
		<p>
			<input type="submit" name="submit" value="Submit" class="submit-button" />
			<input type="reset" />
		</p>
	</fieldset>
</form>



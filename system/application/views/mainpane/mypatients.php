<h2>My Patients</h2>

<?php
// Id of the table
$table['table-name'] = 'mypatients-table';

// Names of the headers in the table
$table['th'] = array('Account Id', 'First Name', 'Last Name', 'Actions');

// Classes for columns (order matters)
$table['th_class'] = array('id_keeper', '', '', '');
$table['td_class'] = array('id_keeper', '', '', '');

// All the results from the database
$table['tuples'] = $pat_list;

// Attributes to display
$table['attr'] = array('account_id', 'first_name', 'last_name', '*actions');

// Special columns to dislpay
for ($i = 0; $i < count($table['tuples']); $i++) {
	$table['tuples'][$i]['*actions'] = '
	<ul>
		<li><a href="/profile/user/'.$table['tuples'][$i]['account_id'].'" class="ajaxlink">See Profile</a></li>
		<li><a href="">Send Email</a></li>
		<li><a href="">Delete Connection</a></li>
		<li><a href="">Upload Medical Record</a></li>
		<li><a href="">Issue Bill</a></li>
	</ul>';
}

require_once('table_result.php');
?>

<!--table class="tables-1" id="mypatients-table" cellpadding="0" cellspacing="0">
	<tr>
		<th class="id_keeper">Account Id</th>
		<th>First Name</th>
		<th>Last Name</th>
		<th>Actions</th>
	</tr>
	< ?php foreach ($pat_list as $hcp) : ?>
	<tr>
		<td class="id_keeper">< ?php echo $hcp['account_id']; ?></td>
		<td>< ?php echo $hcp['first_name']; ?></td>
		<td>< ?php echo $hcp['last_name']; ?></td>
		<td>
			<ul>
				<li><a href="">Send Email</a></li>
				<li><a href="">Delete Connection</a></li>
				<li><a href="">Upload Medical Record</a></li>
				<li><a href="">Issue Bill</a></li>
			</ul>
		</td>
	</tr>
	< ? endforeach ?>
</table-->

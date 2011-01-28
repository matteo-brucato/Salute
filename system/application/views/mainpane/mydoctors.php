<h1>My 
<?php
if ($this->auth->get_type() == 'patient')
	echo 'Doctors';
else
	echo 'Collegues';
?>
</h1>

<?php
// Id of the table
$table['table-name'] = 'mydoctors-table';

// Names of the headers in the table
$table['th'] = array('Account Id', 'First Name', 'Last Name', 'Specialty', 'Actions');

// Classes for columns (order matters)
$table['th_class'] = array('id_keeper', '', '', '', '');
$table['td_class'] = array('id_keeper', '', '', '', '');

// All the results from the database
$table['tuples'] = $hcp_list;

// Attributes to display
$table['attr'] = array('account_id', 'first_name', 'last_name', 'specialization', '*actions');

// Special columns to dislpay
if ($this->auth->get_type() == 'patient') {
	for ($i = 0; $i < count($table['tuples']); $i++) {
		$table['tuples'][$i]['*actions'] = '
		<ul>
			<li><a href="">Send Email</a></li>
			<li><a href="">Delete Connection</a></li>
			<li><a href="">Request Appointment</a></li>
		</ul>';
	}
} else {
	for ($i = 0; $i < count($table['tuples']); $i++) {
		$table['tuples'][$i]['*actions'] = '
		<ul>
			<li><a href="">Send Email</a></li>
			<li><a href="">Delete Connection</a></li>
		</ul>';
	}
}

require_once('table_result.php');
?>

<!-- table class="tables-1" id="mydoctors-table" cellpadding="0" cellspacing="0">
	<tr>
		<th class="id_keeper">Account Id</th>
		<th>First Name</th>
		<th>Last Name</th>
		<th>Specialty</th>
		<th>Actions</th>
	</tr>
	< ?php foreach ($hcp_list as $hcp) : ?>
	<tr>
		<td class="id_keeper">< ?php echo $hcp['account_id']; ?></td>
		<td>< ?php echo $hcp['first_name']; ?></td>
		<td>< ?php echo $hcp['last_name']; ?></td>
		<td>< ?php echo $hcp['specialization']; ?></td>
		<td>
			<ul>
				<li><a href="">Send Email</a></li>
				<li><a href="">Delete Connection</a></li>
				< ?php if ($this->session->userdata('type') === 'patient') : ?>
				<li><a href="">Request Appointment</a></li>
				< ?php endif ?>
			</ul>
		</td>
	</tr>
	< ? endforeach ?>
</table-->

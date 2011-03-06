<h2>Salute Doctors</h2>

<?php
$this->load->helper('actions');
$this->load->helper('table_result');

// Id of the table
$table['table-name'] = 'mydoctors-table';

// Names of the headers in the table
$table['th'] = array('Account Id', 'First Name', 'Last Name', 'Specialty', 'Actions');

// Classes for columns (order matters)
$table['th_class'] = array('id_keeper', '', '', '', '');
$table['td_class'] = array('id_keeper', '', '', '', '');

// All the results from the database
$table['tuples'] = $doc_list;

// Attributes to display
$table['attr'] = array('account_id', 'first_name', 'last_name', 'specialization', '*actions');

// Special columns to dislpay
if ($this->auth->get_type() == 'patient') {
	for ($i = 0; $i < count($table['tuples']); $i++) {
		if ($table['tuples'][$i]['connected']) {
			$table['tuples'][$i]['*actions'] = '
			<ul>
				<li><a href="/profile/user/'.$table['tuples'][$i]['account_id'].'" class="ajax">See Profile</a></li>
				<li><a href="/messages/compose/'.$table['tuples'][$i]['account_id'].'">Send Email</a></li>
				<li><a href="/connections/destroy/'.$table['tuples'][$i]['account_id'].'" class="confirm">Delete Connection</a></li>
				<li><a href="/appointments/request/'.$table['tuples'][$i]['account_id'].'">Request Appointment</a></li>
			</ul>';
		} else {
			$table['tuples'][$i]['*actions'] = '
			<ul>
				<li><a href="/profile/user/'.$table['tuples'][$i]['account_id'].'">See Profile</a></li>
				<li><a href="/connections/request/'.$table['tuples'][$i]['account_id'].'" class="confirm">Request Connection</a></li>
			</ul>';
		}
	}
} else {
	for ($i = 0; $i < count($table['tuples']); $i++) {
		if ($table['tuples'][$i]['connected']) {
			$table['tuples'][$i]['*actions'] = '
			<ul>
				<li><a href="/profile/user/'.$table['tuples'][$i]['account_id'].'">See Profile</a></li>
				<li><a href="/messages/compose/'.$table['tuples'][$i]['account_id'].'">Send Email</a></li>
				<li><a href="/connections/destroy/'.$table['tuples'][$i]['account_id'].'" class="confirm">Delete Connection</a></li>
			</ul>';
		} else {
			$table['tuples'][$i]['*actions'] = '
			<ul>
				<li><a href="/profile/user/'.$table['tuples'][$i]['account_id'].'">See Profile</a></li>
				<li><a href="/connections/request/'.$table['tuples'][$i]['account_id'].'" class="confirm">Request Connection</a></li>
			</ul>';
		}
	}
}

view_table($table);
?>

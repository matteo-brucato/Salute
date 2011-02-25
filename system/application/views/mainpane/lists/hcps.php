<?php
$this->load->helper('actions');
$this->load->helper('table_result');

/**
 * @param
 *   $list_name		The name of the list
 *   $list			The result array from the database
 *   $status		Must be one of:
 * 					"connected", "pending_in", "pending_out" or 
 * 					"not_connected". If not, it assumes "not_connected"
 * 
 * @note For now we are assuming that a controller asks a list where
 * ALL the tuples are of the same status.
 * */

echo '<h2>'.$list_name.'</h2>';

// Id of the table
$table['table-name'] = 'mydoctors-table';

// Names of the headers in the table
$table['th'] = array('Account Id', 'First Name', 'Last Name', 'Specialty', 'Actions');

// Classes for columns (order matters)
$table['th_class'] = array('id_keeper', '', '', '', '');
$table['td_class'] = array('id_keeper', '', '', '', '');

// All the results from the database
$table['tuples'] = $list;

// Attributes to display
$table['attr'] = array('account_id', 'first_name', 'last_name', 'specialization', 'actions');

// Special columns to dislpay
if ($status === 'connected') {
	if ($this->auth->get_type() === 'patient') {
		$actions = array('profile', 'send-email', 'delete-conn', 'request-app');
	} else {
		$actions = array('profile', 'send-email', 'delete-conn');
	}
}
else if ($status === 'pending_in') {
	$actions = array('profile', 'accept-conn-req', 'reject-conn-req');
}
else if ($status === 'pending_out') {
	$actions = array('profile', 'cancel-conn-req');
}
else { // not connected
	$actions = array('profile', 'request-conn');
}

// Everybody has the same action, in this implementation
for ($i = 0; $i < count($table['tuples']); $i++) {
	
	$table['tuples'][$i]['actions'] = '<ul>';
	$table['tuples'][$i]['actions'] .= get_action_strings($actions, $list[$i]);
	$table['tuples'][$i]['actions'] .= '<ul>';
	
}

view_table($table);
		
?>

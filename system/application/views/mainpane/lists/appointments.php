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
$table['table-name'] = 'myappointments-table';

// Names of the headers in the table
$table['th'] = array( 'First Name', 'Last Name', 'Description', 'Date & Time', 'Approved', 'Actions');

// Classes for columns (order matters)
$table['th_class'] = array( '', '', '', '', '', '', '');
$table['td_class'] = array( '', '', '', '', '', '', '');

// All the results from the database
$table['tuples'] = $list;

// Attributes to display
$table['attr'] = array( 'first_name', 'last_name','descryption', 'date_time', 'approved', 'actions');


for ($i = 0; $i < count($table['tuples']); $i++) {
	
	if( $table['tuples'][$i]['approved'] === 't' ){
		$table['tuples'][$i]['approved'] = 'approved';
	}
	else{
		$table['tuples'][$i]['approved'] = 'pending';
	}

	if ($this->auth->get_type() === 'patient') {
		$actions = array('reschedule-app', 'cancel-app');
	} 
	else {
		if ($table['tuples'][$i]['approved'] === 'approved')
			$actions = array('cancel-app');
		else
			$actions = array('accept-app', 'cancel-app');
	}
	
	$apt_date_time = strtotime($table['tuples'][$i]['date_time']);
	if ($apt_date_time < time())
		$actions = array();
	
	$table['tuples'][$i]['actions'] = '<ul>';
	$table['tuples'][$i]['actions'] .= get_action_strings($actions, $list[$i]);
	$table['tuples'][$i]['actions'] .= '<ul>';
}
view_table($table);
?>

<?php
$this->load->helper('actions');
$this->load->helper('table_result');

/**
 * @param
 *   $list_name		The name of the list
 *   $list			The result array from the database
 *   $status		Must be one of:
 * 					
 * 					
 * 
 * @note For now we are assuming that a controller asks a list where
 * ALL the tuples are of the same status.
 * */

echo '<h2>'.$list_name.'</h2>';

// Id of the table
$table['table-name'] = 'myreferals-table';

// Names of the headers in the table
if ($this->auth->get_type() === 'patient')
	$table['th'] = array( 'Doc First Name', 'Doc Last Name', 'Doc First Name', 'Doc Last Name', 'Specialization', 'Date & Time', 'Status', 'Actions');
else
	$table['th'] = array( 'Pat First Name', 'Pat Last Name', 'Doc First Name', 'Doc Last Name', 'Specialization', 'Date & Time', 'Status', 'Actions');

	
// Classes for columns (order matters)
$table['th_class'] = array('', '', '', '', '', '', '', '');
$table['td_class'] = array('', '', '', '', '', '', '', '');

// All the results from the database
$table['tuples'] = $list;

// Attributes to display
if ($this->auth->get_type() === 'patient')
	$table['attr'] = array( 'ref_fn', 'ref_ln','is_ref_fn', 'is_ref_ln', 'specialization', 'date_time', 'status', 'actions');
else
	$table['attr'] = array( 'pat_fn', 'pat_ln','is_ref_fn', 'is_ref_ln', 'specialization', 'date_time', 'status', 'actions');

for ($i = 0; $i < count($table['tuples']); $i++) {
	
	if( $table['tuples'][$i]['status'] === 'f' ){
		$table['tuples'][$i]['status'] = 'pending';
	}
	else{
		$table['tuples'][$i]['status'] = 'Request Sent';
	}

	if ($this->auth->get_type() === 'patient') {
		if ($table['tuples'][$i]['status'] === 'pending')
			$actions = array('accept-ref', 'delete-ref');
		else
			$actions = array('delete-ref');
	} 
	else {
		$actions = array('delete-ref');
		/*if ($table['tuples'][$i]['status'] === 'Request Sent')
			$actions = array();
		else
			$actions = array('delete-ref');	*/
		  	
	}
	
	$table['tuples'][$i]['actions'] = '<ul>';
	$table['tuples'][$i]['actions'] .= get_action_strings($actions, $list[$i]);
	$table['tuples'][$i]['actions'] .= '<ul>';
}
view_table($table);
?>

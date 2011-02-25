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
$table['table-name'] = 'mypatients-table';

// Names of the headers in the table
$table['th'] = array('Account Id', 'First Name', 'Last Name', 'Select');

// Classes for columns (order matters)
$table['th_class'] = array('id_keeper', '', '', '');
$table['td_class'] = array('id_keeper', '', '', '');

// All the results from the database
$table['tuples'] = $list;

// Attributes to display
$table['attr'] = array('account_id', 'first_name', 'last_name', 'actions');

// The only actions available it to select a patient using a radio button
for ($i = 0; $i < count($table['tuples']); $i++) {
	
	$table['tuples'][$i]['actions'] = '<center><input type="radio" name="patient_id" value=" '.$table['tuples'][$i]['account_id'].'</center>">';
}

//action="/groups/create_do"
echo '<form method="post" action="refers/get_patient_id" id="pick_patient">';
view_table($table);

echo 	'<br />';
echo 	'<p>';
echo		'<input type="submit" name="submit" value="Submit" class="submit-button" />';
echo		'<input type="reset" />';
echo 	'</p>';
echo '</form>';
?>

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
$table['table-name'] = 'medical_records-table';

// Names of the headers in the table
$table['th'] = array('Medical Record Id', 'Patient', 'Created By', 'Issue', 'Info', 'Actions');

// Classes for columns (order matters)
$table['th_class'] = array('id_keeper', '', '', '', '', '');
$table['td_class'] = array('id_keeper', '', '', '', '', '');

// All the results from the database
$table['tuples'] = $list;

// Attributes to display
$table['attr'] = array('medical_rec_id', 'pat_first_name', 'first_name', 'issue', 'suplementary_info', 'actions');

// Special values for file downloading
if ($this->auth->get_type() === 'patient') {
	for ($i = 0; $i < count($table['tuples']); $i++) {
		$table['tuples'][$i]['actions'] = '<ul>';
		$table['tuples'][$i]['actions'] .= get_action_strings(
			array('add-perm', 'delete-perm', 'view-all-perm', 'download-med-rec', 'delete-med-rec'),
			$table['tuples'][$i]);
		$table['tuples'][$i]['actions'] .= '</ul>';
	}
} else {
	for ($i = 0; $i < count($table['tuples']); $i++) {
		$table['tuples'][$i]['actions'] = '<ul>';
		$table['tuples'][$i]['actions'] .= get_action_strings(
			array('see-med-rec', 'download-med-rec'), $table['tuples'][$i]);
		$table['tuples'][$i]['actions'] .= '</ul>';
	}
}

/*for ($i = 0; $i < count($table['tuples']); $i++) {
	$table['tuples'][$i]['file'] = '<ul>';
	$table['tuples'][$i]['file'] .= '<li><a href="/download/medical_record/'
		.$table['tuples'][$i]['patient_id'].'/'
		.$table['tuples'][$i]['medical_rec_id'].'">Download</a></li>';
	$table['tuples'][$i]['file'] .= '<li><a href="/medical_records/delete/'
		.$table['tuples'][$i]['medical_rec_id'].'">Delete</a></li>';
	$table['tuples'][$i]['file'] .= '</ul>';
}*/

view_table($table);



?>

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
$table['th'] = array('Medical Record Id', 'Patient', 'Created By', 'Issue', 'Info', 'File');

// Classes for columns (order matters)
$table['th_class'] = array('id_keeper', '', '', '', '', '');
$table['td_class'] = array('id_keeper', '', '', '', '', '');

// All the results from the database
$table['tuples'] = $list;

// Attributes to display
$table['attr'] = array('medical_rec_id', 'patient_id', 'account_id', 'issue', 'suplementary_info', 'file');

// Special values for file downloading
for ($i = 0; $i < count($table['tuples']); $i++) {
	$table['tuples'][$i]['file'] = '<a href="/download/'.
	$table['tuples'][$i]['file_path'].'">Download File</a>';
}


view_table($table);
?>

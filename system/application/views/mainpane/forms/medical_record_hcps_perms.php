<?php
$this->load->helper('actions');
$this->load->helper('table_result');

/**
 * @param
 *   $list_name		The name of the list
 *   $list			The result array from the database
 *   $status		Must be one of:
 * 
 * @note For now we are assuming that a controller asks a list where
 * ALL the tuples are of the same status.
 * */

echo '<h2 class="table-header">'.$list_name.'</h2>';

// Id of the table
$table['table-name'] = 'medical-record-hcps-perms-table';

// Names of the headers in the table
$table['th'] = array('Account Id', 'First Name', 'Last Name', 'Specialty', 'Select');

// Classes for columns (order matters)
$table['th_class'] = array('id_keeper', '', '', '', 'checkbox');
$table['td_class'] = array('id_keeper', '', '', '', 'checkbox');

// All the results from the database
$table['tuples'] = $list;

// Attributes to display
$table['attr'] = array('account_id', 'first_name', 'last_name', 'specialization', 'actions');

echo '<form method="post" action="'.$form_action.'" id="med_rec_hcps_perm_form">';

// Checkbox, already checked if the account has permission
for ($i = 0; $i < count($table['tuples']); $i++) {
	$checked = '';
	for ($j = 0; $j < count($list2); $j++) {
		if ($table['tuples'][$i]['account_id'] === $list2[$j]['account_id']) {
			$checked = 'checked="checked"';
			break;
		}
	}
	
	$table['tuples'][$i]['actions'] =
		'<input type="checkbox" '.$checked.' name="hcps[]" value="'.$table['tuples'][$i]['account_id'].'" />';
}

view_table($table);

echo 	'<br />';
echo 	'<p>';
echo		'<input type="hidden" name="change" value="hcps" />';
echo		'<input type="submit" name="submit" value="Submit" class="submit-button" />';
echo		'<input type="reset" />';
echo 	'</p>';
echo '</form>';

?>

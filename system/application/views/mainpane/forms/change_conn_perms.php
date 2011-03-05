<?php
$this->load->helper('actions');
$this->load->helper('table_result');
//$box=$POST['box'];
/**
 * @param
 *   $list_name		The name of the list
 *   $list			The result array from the database
 *   $status		Must be one of:
 * 
 * @note For now we are assuming that a controller asks a list where
 * ALL the tuples are of the same status.
 * */
 
echo '<h2>'.$list_name.'</h2>';
//echo($hcp_id);

// Id of the table
$table['table-name'] = 'medical_records-table';

// Names of the headers in the table
$table['th'] = array('Medical Record Id', 'Patient', 'Created By', 'Issue', 'Info', 'Actions'/*, 'Type'*/);

// Classes for columns (order matters)
$table['th_class'] = array('id_keeper', '', '', '', '', 'checkbox'/*, ''*/);
$table['td_class'] = array('id_keeper', '', '', '', '', 'checkbox'/*, ''*/);

// All the results from the database
$table['tuples'] = $list;
$table['hcptuples'] = $list2;

// Attributes to display
$table['attr'] = array('medical_rec_id', 'pat_first_name', 'first_name', 'issue', 'suplementary_info', 'actions'/*, 'type'*/);


for ($i = 0; $i < count($table['tuples']); $i++) {
	$isit = FALSE;
	//$type1 = 'checked="checked"';
	//$type2 = '';
	for($j = 0; $j < count($table['hcptuples']); $j++ ){
		if ($table['tuples'][$i]['medical_rec_id'] === $table['hcptuples'][$j]['medical_rec_id']) {
			$isit = true;
			//if ($table['hcptuples'][$j]['type'] != '0') {
			//	$type1 = '';
			//	$type2 = 'checked="checked"';
			//}
			break;
		}
	}
	if( $isit === TRUE ){
		$table['tuples'][$i]['actions'] = '<input type="checkbox" 
													name="box[]" 
													value=" '.$table['tuples'][$i]['medical_rec_id'].'" 
													checked = "checked" />';
	}
	else{
		$table['tuples'][$i]['actions'] = '<input type="checkbox" 
													name="box[]" 
													value=" '.$table['tuples'][$i]['medical_rec_id'].'"/>';
	}
	//$table['tuples'][$i]['type'] =
	//	'<input type="radio" name="type'.$table['tuples'][$i]['medical_rec_id'].'" '.$type1.' /> download only
	//	<br /><input type="radio" name="type'.$table['tuples'][$i]['medical_rec_id'].'" '.$type2.' /> delete also';
}

echo '<form method="post" action="/connections/permissions_do/'.$aid.'" id="change_perm">';
view_table($table);

echo	'<div class="checkAll"><input type="checkbox" name="checkAll" /> Check/Uncheck All</div>';
echo 	'<br />';
echo 	'<p>';
//echo		'<input type="hidden" name="hcp_id" value = "'.$hcp_id.'" />';
echo		'<input type="submit" name="submit" value="Submit" class="submit-button" />';
echo		'<input type="reset" />';
echo 	'</p>';
echo '</form>';
?>

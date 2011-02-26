<?php
$this->load->helper('actions');
$this->load->helper('table_result');

/**
 * @param
 *		$list_name		The name of the list
 *		$list			The result array from the database
 * 		$perm	 		Boolean
 * */

//echo '<h2>'.$list_name.'</h2>';

// Id of the table
$table['table-name'] = 'my-groups-table';

// Names of the headers in the table
$table['th'] = array('Group Id', 'Group Name', 'Description', 'Type', 'Actions');

// Classes for columns (order matters)
$table['th_class'] = array('id_keeper', '', '', '', '');
$table['td_class'] = array('id_keeper', '', '', '', '');

// All the results from the database
$table['tuples'] = $group_list;

// Attributes to display
$table['attr'] = array('group_id','name', 'description', 'group_type', 'actions');

// Everybody has the same action, in this implementation
for ($i = 0; $i < count($table['tuples']); $i++) {
	if($perm[$i]['can_delete'] === TRUE)
		$actions = array('leave-group','delete-group');  
	else 
		$actions = array('leave-group');
	if($table['tuples'][$i]['group_type'] == 0) $table['tuples'][$i]['group_type'] = 'patients only';
	else if($table['tuples'][$i]['group_type'] == 1) $table['tuples'][$i]['group_type'] = 'healthcare providers only';
	else $table['tuples'][$i]['group_type'] = 'patients and healthcare providers';
	$table['tuples'][$i]['actions'] = '<ul>';
	$table['tuples'][$i]['actions'] .= get_action_strings($actions, $group_list[$i]);
	$table['tuples'][$i]['actions'] .= '<ul>';
}

view_table($table);
?>

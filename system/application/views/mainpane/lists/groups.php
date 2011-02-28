<?php
$this->load->helper('actions');
$this->load->helper('table_result');

/**
 * @param
 *		$list_name		The name of the list
 *		$list			The result array from the database
 * 		$member 		Boolean
 * */

//echo '<h2>'.$list_name.'</h2>';

// Id of the table
$table['table-name'] = 'groups-table';

// Names of the headers in the table
$table['th'] = array('Group Id', 'Group Name', 'Description', 'Type', 'Actions');

// Classes for columns (order matters)
$table['th_class'] = array('id_keeper', '', '', '', '');
$table['td_class'] = array('id_keeper', '', '', '', '');

// All the results from the database
$table['tuples'] = $group_list;

// Attributes to display
$table['attr'] = array('group_id','name', 'description', 'group_type', 'actions');

for ($i = 0; $i < count($table['tuples']); $i++) {
	
	if($member[$i]['is'] === TRUE){
		if($member[$i]['perm'] === '0')
			$actions = array('leave-group');
		else if ($member[$i]['perm'] === '3')
			$actions = array('invite-to-group','leave-group', 'edit-group','delete-group');
		else if($member[$i]['perm'] !== '0' && $member[$i]['perm'] !== NULL)
			$actions = array('invite-to-group','leave-group'); // later: add link that loads a whole form to change all member perms
		else
			$actions = array(''); 
	}else 
		$actions = array('join-group');
	
		
	if($table['tuples'][$i]['group_type'] == 0) $table['tuples'][$i]['group_type'] = 'patients only';
	else if($table['tuples'][$i]['group_type'] == 1) $table['tuples'][$i]['group_type'] = 'healthcare providers only';
	else $table['tuples'][$i]['group_type'] = 'patients and healthcare providers';
	$table['tuples'][$i]['actions'] = '<ul>';
	$table['tuples'][$i]['actions'] .= get_action_strings($actions, $group_list[$i]);
	$table['tuples'][$i]['actions'] .= '<ul>';
}

view_table($table);
?>

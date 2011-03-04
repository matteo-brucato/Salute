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
$table['th'] = array('Account Id', 'Group Id','First Name', 'Last Name', 'Actions');

// Classes for columns (order matters)
$table['th_class'] = array('id_keeper', 'id_keeper', '', '', '');
$table['td_class'] = array('id_keeper', 'id_keeper', '', '', '');


// All the results from the database
$table['tuples'] = $mem_list;

// Attributes to display
$table['attr'] = array('account_id', 'group_id','first_name','last_name','actions');

if($perm['permissions'] == '0')
	$actions = array('request-conn');  
else if ($perm['permissions'] == '1')
	$actions = array('request-conn');  
else if ($perm['permissions'] == '2')
	$actions = array('request-conn', 'delete-member','edit-mem');  
else if ($perm['permissions'] == '3')
	$actions = array('request-conn', 'delete-member','edit-mem');  

for ($i = 0; $i < count($table['tuples']); $i++) {
	
	$table['tuples'][$i]['first_name'] = $info[$i]['first_name'];
	$table['tuples'][$i]['last_name'] = $info[$i]['last_name'];
	$table['tuples'][$i]['actions'] = '<ul>';
	$table['tuples'][$i]['actions'] .= get_action_strings($actions, $mem_list[$i]);
	$table['tuples'][$i]['actions'] .= '<ul>';
}

view_table($table);
?>


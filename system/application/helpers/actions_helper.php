<?php
function get_action_strings($actions, $table, $i) {
	$str = '';
	foreach ($actions as $action) {
		switch ($action) {
			case 'profile':
				$str .= '<li><a href="/profile/user/'.$table['tuples'][$i]['account_id'].'" class="ajaxlink">See Profile</a></li>';
				break;
			case 'send-email':
				$str .= '<li><a href="">Send Email</a></li>';
				break;
			case 'delete-conn':
				$str .= '<li><a href="">Delete Connection</a></li>';
				break;
			case 'request-app':
				$str .= '<li><a href="">Request Appointment</a></li>';
				break;
			case 'accept-conn-req':
				$str .= '<li><a href="">Accept Connection</a></li>';
				break;
			case 'reject-conn-req':
				$str .= '<li><a href="">Reject Connection</a></li>';
				break;
			case 'cancel-conn-req':
				$str .= '<li><a href="">Cancel Connection Request</a></li>';
				break;
			case 'request-conn':
				$str .= '<li><a href="">Request Connection</a></li>';
				break;
			case 'upload-med-rec':
				$str .= '<li><a href="">Upload Medical Record</a></li>';
				break;
			case 'issue-bill':
				$str .= '<li><a href="">Issue Bill</a></li>';
				break;
		}
	}
	return $str;
}
?>

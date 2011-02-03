<?php
function get_action_strings($actions, $table, $i) {
	$CI = get_instance();
	$str = '';
	foreach ($actions as $action) {
		switch ($action) {
			case 'profile':
				$str .= '<li><a href="/profile/user/'.$table['tuples'][$i]['account_id'].'" class="ajaxlink">See Profile</a></li>';
				break;
			case 'send-email':
				$str .= '<li><a href="/messages/compose">Send Email</a></li>';
				break;
			case 'delete-conn':
				$str .= '<li><a href="/connections/destroy/'.$table['tuples'][$i]['account_id'].'">Delete Connection</a></li>';
				break;
			case 'request-app':
				$str .= '<li><a href="/appointments/request">Request Appointment</a></li>';
				break;
			case 'accept-conn-req':
				$str .= '<li><a href="/connections/accept/'.$table['tuples'][$i]['account_id'].'">Accept Connection</a></li>';
				break;
			case 'reject-conn-req':
				$str .= '<li><a href="/connections/reject/'.$table['tuples'][$i]['account_id'].'">Reject Connection</a></li>';
				break;
			case 'cancel-conn-req':
				$str .= '<li><a href="/connections/cancel/'.$table['tuples'][$i]['account_id'].'">Cancel Connection Request</a></li>';
				break;
			case 'request-conn':
				$str .= '<li><a href="/connections/request/'.$table['tuples'][$i]['account_id'].'">Request Connection</a></li>';
				break;
			case 'upload-med-rec':
				$str .= '<li><a href="/medical_records/upload/">Upload Medical Record</a></li>';
				break;
			case 'issue-bill':
				$str .= '<li><a href="/bills/issue">Issue Bill</a></li>';
				break;
		}
	}
	return $str;
}
?>

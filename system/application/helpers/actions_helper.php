<?php
function get_action_strings($actions, $tuple) {
	$CI = get_instance();
	$str = '';
	foreach ($actions as $action) {
		switch ($action) {
			case 'profile':
				$str .= '<li><a href="/profile/user/'.$tuple['account_id'].'" class="ajaxlink">See Profile</a></li>';
				break;
			case 'send-email':
				$str .= '<li><a href="/messages/compose/'.$tuple['account_id'].'">Send Email</a></li>';
				break;
			case 'delete-conn':
				$str .= '<li><a href="/connections/destroy/'.$tuple['account_id'].'">Delete Connection</a></li>';
				break;
			case 'request-app':
				$str .= '<li><a href="/appointments/request/'.$tuple['account_id'].'">Request Appointment</a></li>';
				break;
			case 'accept-conn-req':
				$str .= '<li><a href="/connections/accept/'.$tuple['account_id'].'">Accept Connection</a></li>';
				break;
			case 'reject-conn-req':
				$str .= '<li><a href="/connections/reject/'.$tuple['account_id'].'">Reject Connection</a></li>';
				break;
			case 'cancel-conn-req':
				$str .= '<li><a href="/connections/cancel/'.$tuple['account_id'].'">Cancel Connection Request</a></li>';
				break;
			case 'request-conn':
				$str .= '<li><a href="/connections/request/'.$tuple['account_id'].'">Request Connection</a></li>';
				break;
			case 'upload-med-rec':
				$str .= '<li><a href="/medical_records/upload/'.$tuple['account_id'].'">Upload Medical Record</a></li>';
				break;
			case 'issue-bill':
				$str .= '<li><a href="/bills/issue/'.$tuple['account_id'].'">Issue Bill</a></li>';
				break;
		}
	}
	return $str;
}
?>

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
				
			case 'request-app':
				$str .= '<li><a href="/appointments/request/'.$tuple['account_id'].'">Request Appointment</a></li>';
				break;	
			case 'cancel-app':
				$str .= '<li><a href="/appointments/cancel/'.$tuple['appointment_id'].'">Cancel</a></li>';
				break;
			case 'reschedule-app':
				$str .= '<li><a href="/appointments/reschedule/'.$tuple['appointment_id'].'">Reschedule</a></li>';
				break;	
			case 'accept-app':
				$str .= '<li><a href="/appointments/accept_appointment/'.$tuple['appointment_id'].'">Accept</a></li>';
				break;	

			case 'delete-conn':
				$str .= '<li><a href="/connections/destroy/'.$tuple['account_id'].'">Delete Connection</a></li>';
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
			
			case 'issue-bill':
				$str .= '<li><a href="/bills/issue/'.$tuple['account_id'].'">Issue Bill</a></li>';
				break;
			case 'delete-bill':
				$str .= '<li><a href="/bills/delete/'.$tuple['bill_id'].'">Delete Bill</a></li>';
				break;
			case 'pay-bill':
				$str .= '<li><a href="/bills/pay/'.$tuple['bill_id'].'">Pay Bill</a></li>';
				break;
			
			case 'list-med-recs':
				$str .= '<li><a href="/medical_records/patient/'.$tuple['account_id'].'">Medical Records</a></li>';
				break;
			/*case 'see-med-rec':
				$str .= '<li><a href="/medical_records/see/'.$tuple['medical_rec_id'].'">See</a></li>';
				break;*/
			case 'upload-med-rec':
				$str .= '<li><a href="/medical_records/upload/'.$tuple['account_id'].'">Upload Medical Record</a></li>';
				break;
			case 'download-med-rec':
				$str .= '<li><a href="/download/medical_record/'.$tuple['patient_id'].'/'.$tuple['medical_rec_id'].'">Download</a></li>';
				break;
			case 'delete-med-rec':
				$str .= '<li><a href="/medical_records/delete/'.$tuple['medical_rec_id'].'">Delete</a></li>';
				break;
			
			case 'add-perm':
				$str .= '<li><a href="/medical_records/add_permission/'.$tuple['medical_rec_id'].'">Add a permission</a></li>';
				break;
			case 'delete-perm':
				$str .= '<li><a href="/medical_records/remove_permission/'.$tuple['medical_rec_id'].'">Remove permission</a></li>';
				break;
			case 'view-all-perm':
				$str .= '<li><a href="/medical_records/see_permissions/'.$tuple['medical_rec_id'].'">View all permissions</a></li>';
				break;
		}
	}
	return $str;
}
?>

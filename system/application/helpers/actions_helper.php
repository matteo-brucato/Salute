<?php
/**
 * @file actions_helper.php
 * @brief Contains functions to help handling action links in the interface
 * 
 * 
 * @defgroup hlp Helpers
 * @ingroup hlp
 * */
function get_action_strings($actions, $tuple) {
	$CI = get_instance();
	$str = '';
	foreach ($actions as $action) {
		switch ($action) {
			case 'profile':
				$str .= '<li><a href="/profile/user/'.$tuple['account_id'].'" class="ajax">See Profile</a></li>';
				break;
			
			case 'send-email':
				$str .= '<li><a href="/messages/compose/'.$tuple['account_id'].'">Send Email</a></li>';
				break;
				
			case 'request-app':
				$str .= '<li><a href="/appointments/request/'.$tuple['account_id'].'">Request Appointment</a></li>';
				break;	
			case 'cancel-app':
				$str .= '<li><a href="/appointments/cancel/'.$tuple['appointment_id'].'" class="confirm">Cancel</a></li>';
				break;
			case 'reschedule-app':
				$str .= '<li><a href="/appointments/reschedule/'.$tuple['appointment_id'].'">Reschedule</a></li>';
				break;	
			case 'accept-app':
				$str .= '<li><a href="/appointments/accept_appointment/'.$tuple['appointment_id'].'">Accept</a></li>';
				break;	

			case 'delete-conn':
				$str .= '<li><a href="/connections/destroy/'.$tuple['account_id'].'" class="confirm">Delete Connection</a></li>';
				break;
			case 'accept-conn-req':
				$str .= '<li><a href="/connections/accept/'.$tuple['account_id'].'" class="confirm">Accept Connection</a></li>';
				break;
			case 'reject-conn-req':
				$str .= '<li><a href="/connections/reject/'.$tuple['account_id'].'" class="confirm">Reject Connection</a></li>';
				break;
			case 'cancel-conn-req':
				$str .= '<li><a href="/connections/cancel/'.$tuple['account_id'].'" class="confirm">Cancel Connection Request</a></li>';
				break;
			case 'request-conn':
				$str .= '<li><a href="/connections/request/'.$tuple['account_id'].'" class=\"confirm\">Request Connection</a></li>';
				break;
			case 'change-level':
				$str .= '<li><a href="/connections/change_level/'.$tuple['account_id'].'" class=\"\">Change Connection Level</a></li>';
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
				$str .= '<li><a href="/medical_records/delete/'.$tuple['medical_rec_id'].'" class="confirm">Delete</a></li>';
				break;
			
			case 'add-perm':
				$str .= '<li><a href="/medical_records/add_permission/'.$tuple['medical_rec_id'].'">Add a permission</a></li>';
				break;
			case 'delete-perm':
				$str .= '<li><a href="/medical_records/remove_permission/'.$tuple['medical_rec_id'].'/'.$tuple['account_id'].'" class="confirm">Remove permission</a></li>';
				break;
			case 'change-perm':
				$str .= '<li><a href="/medical_records/change_permissions/'.$tuple['account_id'].'">Change shared medical records</a></li>';
				break;
			case 'view-all-perm':
				$str .= '<li><a href="/medical_records/see_permissions/'.$tuple['medical_rec_id'].'">View all permissions</a></li>';
				break;
				
			case 'join-group':
				$str .= '<li><a href="/groups/members/join/'.$tuple['group_id'].'">Join</a></li>';
				break;
				
			case 'leave-group':
				$str .= '<li><a href="/groups/members/leave/'.$tuple['group_id'].'/'.$tuple['account_id'].'" class="confirm">Leave</a></li>';
				break;
			
			case 'delete-group':
				$str .= '<li><a href="/groups/delete/'.$tuple['group_id'].'">Delete Group</a></li>';
				break;
					
			case 'delete-member':
				$str .= '<li><a href="/groups/members/delete/'.$tuple['group_id'].'/'.$tuple['account_id'].'" class="confirm">Delete Member</a></li>';
				break;
				
			case 'invite-to-group':
				$str .= '<li><a href="/groups/members/invite/'.$tuple['group_id'].'">Invite a Member</a></li>';
				break;
				
			case 'edit-mem':
				$str .= '<li><a href="/groups/members/edit/'.$tuple['group_id'].'/'.$tuple['account_id'].'">Change Permissions</a></li>';
				break;
			
			case 'edit-group':
				$str .= '<li><a href="/groups/edit/'.$tuple['group_id'].'">Edit Group</a></li>';
				break;
						
						
			case 'accept-ref':
				$str .= '<li><a href="/refers/accept_referal/'.$tuple['referal_id'].'">Request Connection</a></li>';
				break;
			case 'delete-ref':
				$str .= '<li><a href="/refers/delete_referal/'.$tuple['referal_id'].'" class="confirm">Delete</a></li>';
				break;

		}
	}
	return $str;
}
/**@}*/
?>

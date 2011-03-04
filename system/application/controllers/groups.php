<?php
/**
 * @file groups.php
 * @brief Controller to handle groups
 *
 * @defgroup ctr Controllers
 * @ingroup ctr
 * @{
 */

/**
 * Class Controller Groups
 * @todo: 	
 * 			add more action links to Group's List / My Groups list
 *			should group names be unique? 
 * @other bugs:
 * 		request connection seems to be broken?
 * */
class Groups extends Controller {

	function __construct() {
		parent::Controller();
		$this->load->library('ui');
		$this->load->library('auth');
		$this->load->model('groups_model');
		$this->load->model('connections_model');
		$this->load->model('account_model');
		$this->load->model('patient_model');
		$this->load->model('hcp_model');
	}
	
	/**
	 * Default method:
	 * 		List all groups
	 * tested.
	 * */
	function index() {
		$this->lists();
	}
	
	/**
	 * tested.
	 * */
	function lists($direction = 'all'){
		if ( $direction == 'all' )
			$this->_lists_all();
		else if ( $direction == 'mine' )
			$this->_lists_mine();
		else
			$this->ui->set_error('Input not valid: <b>'.$direction.'</b>');
	}
	
	function members($direction = 'list', $group_id = NULL, $account_id = NULL){
		if ( $direction == 'list' )
			$this->_members_list($group_id);
		else if ( $direction == 'edit' )
			$this->_members_edit($group_id,$account_id);
		else if ( $direction == 'edit_do' )
			$this->_members_edit_do($group_id,$account_id);
		else if ( $direction == 'join' )
			$this->_members_join($group_id);
		else if ( $direction == 'leave' )
			$this->_members_leave($group_id);
		else if ( $direction == 'delete' )
			$this->_members_delete($group_id,$account_id);
		else if ( $direction == 'invite' )
			$this->_members_invite($group_id);
		else
			$this->ui->set_error('Input not valid: <b>'.$direction.'</b>');	
	}
	
	/**
	 * Loads Create New Group Form
	 * @bug, keeps reloading form after completion...
	 * */
	function create(){
		if ($this->auth->check(array(auth::CurrLOG)) !== TRUE) return;
		
		//$type = $this->auth->get_type();
		
		//if ($type === 'hcp')
		//	$sideview = $this->load->view('sidepane/personal_hcp_profile', '', TRUE);
		//else if ($type === 'patient')
		//	$sideview = $this->load->view('sidepane/personal_patient_profile', '', TRUE);
		
		$this->ui->set(array($this->load->view('mainpane/forms/create_group', '', TRUE)));
	}


	/**
	 * Create a New Group
	 * tested.
	 * */
	function create_do(){

		$name = $this->input->post('name');
		$description = $this->input->post('description');
		$privacy = $this->input->post('public_private');
		$group_type = $this->input->post('group_type');
		
		$type = $this->auth->get_type();
		
		// Form Checking will replace this
		if($name == NULL || $description == NULL || $privacy == NULL || $group_type == NULL )	{
			$this->ui->set_error('All Fields are Mandatory.','Missing Arguments'); 
			return;
		}
		
		if ($group_type === '0' && $type === 'hcp'){
			$this->ui->set_error('You are a healthcare provider. You cannot create a patient only group.', 'Permission Denied');
			return;
		} else if ($group_type === '1' && $type === 'patient'){
			$this->ui->set_error('You are a patient. You cannot create a healthcare provider only group.', 'Permission Denied');
			return;
		}
		
		$this->db->trans_start();
		
		$group_id = $this->groups_model->create(array(
													'account_id' => $this->auth->get_account_id(),
													'name' => $name, 
													'description' => $description, 
													'public_private' => $privacy,
													'group_type' => $group_type,
												)); 
		
		if($group_id === -1){
			$this->ui->set_query_error(); 
			return;
		} else if ( $group_id === -2 ){
			$this->ui->set_error('Group does not exist'); 
			return;
		} 
				
		$admin = $this->groups_model->join($this->auth->get_account_id(),$group_id);
		if($admin === -1){
			$this->ui->set_query_error(); 
			return;
		}
		$permissions = '3';
		$admin = $this->groups_model->edit_member($this->auth->get_account_id(),$group_id,$permissions);
		if($admin === -1){
			$this->ui->set_query_error(); 
			return;
		}
		$this->db->trans_complete();
		
		$this->ui->set_message("You have successfully created the group: $name", 'Confirmation');
		$this->lists('mine');
	}

	
	/**
	 * Delete an Existing Group
	 * 
	 * tested.
	 * */
	function delete($group_id = NULL){
		
		if ($this->auth->check(array(auth::CurrLOG, auth::GRP, $group_id)) !== TRUE) {
			return;
		}
		
		// check that they have permission
		$mem = $this->groups_model->get_member($this->auth->get_account_id(),$group_id); 
		if ($mem === -1){
			$this->ui->set_query_error();
			return;	
		} else if ($mem['permissions'] !== '3') {
			$this->ui->set_error('You do not have permission to delete this group.','Permission Denied');
			return;	
		}
		
		$this->db->trans_start();
				
		$result = $this->groups_model->delete(array($group_id));
		
		if ($result === -1){
			$this->ui->set_query_error();
			return;
		}
		$this->db->trans_complete();

		$this->ui->set_message('The group has been deleted.','Confirmation');
		$this->ui->set($this->lists('mine'));
	}
	
	/**
	 * Join an Existing Group
	 * functionality tested.
	 * @bug does not check if you have permission to join ( e.g. pats only, docs only )
	 * */
	function _members_join($group_id = NULL){
		if ($this->auth->check(array(auth::CurrLOG,auth::GRP,$group_id )) !== TRUE) return;
		
		$this->db->trans_start();
		
		$mem = $this->groups_model->is_member($this->auth->get_account_id(),$group_id);
		$group = $this->groups_model->get_group($group_id);
		if($mem === -1 || $group === -1){
			$this->ui->set_query_error();
			return;
		} else if ($mem){ 
			$this->ui->set_error('You are already a member of this group.','notice');
			return;
		} else if ($mem === FALSE){
			// Check if its a patient
			$mem = $this->patient_model->is_patient($this->auth->get_account_id());
			//if not, check if its a doctor
			if($mem === -1 || $mem === FALSE){
				$mem = $this->hcp_model->is_hcp($this->auth->get_account_id());
				// if not, server error
				if($mem === -1){
					$this->ui->set_error('Internal server error1','server');
					return;
				} else if ($mem === FALSE){
					$this->ui->set_error('Internal server error1','server');
					return;
				} else{
					if ( $group['group_type'] === '0' ){
						$this->ui->set_error('This is a patient only group.','Permission Denied');
						return;
					}
				}
			} else{ //else it is a patient.
				if ( $group['group_type'] === '1' ){
					$this->ui->set_error('This is a healthcare provider only group.','Permission Denied');
					return;
				}
			}
		} else {
			$this->ui->set_error('Internal server error2','server');
			return;
		}
			
		$check = $this->groups_model->join($this->auth->get_account_id(),$group_id);
		if ($check === -1){
			$this->ui->set_query_error();
			return;	
		}
		
		$this->db->trans_complete();
		
		// check again that they're in 'is_in'
		if ($this->auth->check(array(auth::CurrGRPMEM,$group_id)) === TRUE){
			$this->ui->set_message('You have successfully joined the group.','Confirmation');
		} else {
			$this->ui->set_error('Internal Server Error4','server');
			return;
		}
		$this->ui->set($this->lists('mine'));
	}
	
	/**
	 * Group Member Leave from the Existing Group
	 * tested.
	 * */
	function _members_leave($group_id = NULL){

		if ($this->auth->check(array(
										auth::CurrLOG, 
										auth::GRP, $group_id,
										auth::CurrGRPMEM, $group_id
									)) !== TRUE) {
			return;
		}
		
		$this->db->trans_start();
			
		$mem = $this->groups_model->is_member($this->auth->get_account_id(),$group_id);
		if($mem === -1){
			$this->ui->set_query_error();
			return;
		} else if ($mem){
			$check = $this->groups_model->remove($this->auth->get_account_id(),$group_id);
			if ($check === -1){
				$this->ui->set_query_error();
				return;	
			}
		} else {
			$this->ui->set_error('Internal Server Error','server');
			return;
		}

		$this->db->trans_complete();
						
		$this->ui->set_message('You have successfully left the group.','Confirmation');
		$this->ui->set($this->lists('mine'));
	}
	
	// a member is being removed by the group admin
	function _members_delete($group_id = NULL, $account_id = NULL){
	
		if ($this->auth->check(array(auth::CurrLOG,auth::GRP,$group_id,auth::CurrGRPMEM,$group_id)) !== TRUE) return;
		
		$this->db->trans_start();
		
		// check that current member has permission to do this
		$mem = $this->groups_model->get_member($this->auth->get_account_id(),$group_id); 
		if ($mem === -1){
			$this->ui->set_query_error();
			return;	
		} else if ($mem['permissions'] !== '3' && $mem['permissions'] !== '2') {
			$this->ui->set_error('You do not have permission to delete members from this group.','Permission Denied');
			return;	
		}
			
		$mem = $this->groups_model->is_member($account_id,$group_id);
		if($mem === -1){
			$this->ui->set_query_error();
			return;
		} else if ($mem){
			$check = $this->groups_model->remove($account_id,$group_id);
			if ($check === -1){
				$this->ui->set_query_error();
				return;	
			}
		} else {
			$this->ui->set_error('Internal Server Error','server');
			return;
		}
		$this->db->trans_complete();		
		
		$this->ui->set_message(' Member successfully deleted from group.','Confirmation');
		$this->ui->set($this->members('list',$group_id));
	}
	
	/**
	 * Allow Invitations to a Group
	 * @attention invite must be sent to a Salute Member
	 * @attention invite may only be sent by: permission #s 1,2,3 (all except 0)
	 * */
	function _members_invite($gid = NULL) {
		if ($this->auth->check(array(
			auth::CurrLOG,
			auth::GRP, $gid,		// gid must be a group id
			auth::CurrGRPMEM, $gid		// current must be member of group gid
			)) !== TRUE) return;
		
		$results = $this->connections_model->list_patients_connected_with($this->auth->get_account_id()); 
		if ($results === -1) {$this->ui->set_query_error(); return;}
		
		// Get the group tuple
		$group = $this->groups_model->get_group($gid);
		if ($group === -1) {$this->ui->set_query_error(); return;}
		if ($group === NULL) {$this->ui->set_error('Group not found'); return;}
		
		$mainview = '';
		if ($group['group_type'] !== '1'){		
			$mainview .= $this->load->view('mainpane/forms/pick_multiple_patients',
				array(
					'list_name' => 'Select patients to invite to this group',
					'list' => $results,
					'form_action' => '/groups/members_invite_do/'.$gid), TRUE);
		}
		$results = $this->connections_model->list_hcps_connected_with($this->auth->get_account_id()); 
		if ($results === -1) {$this->ui->set_query_error(); return;}
		
		if ($group['group_type'] !== '0'){		
			$mainview .= $this->load->view('mainpane/forms/pick_multiple_hcps',
				array(
					'list_name' => 'Select HCPs to invite to this group',
					'list' => $results,
					'form_action' => '/groups/members_invite_do/'.$gid), TRUE);
		}
		$this->ui->set(array($mainview));
	}
	
	function members_invite_do($gid = NULL) {
		if ($this->auth->check(array(
			auth::CurrLOG,
			auth::GRP, $gid,				// gid must be a group id
			auth::CurrGRPMEM, $gid		// current must be member of group gid
		)) !== TRUE) return;
		
		
		// Get the group tuple
		$group = $this->groups_model->get_group($gid);
		if ($group === -1) {$this->ui->set_query_error(); return;}
		if ($group === NULL) {$this->ui->set_error('Group not found'); return;}
		
		// Get POST variables (an array of ids to invite to gid group)
		$invite_ids = $this->input->post('ids');
		
		// Form input check
		if ($invite_ids == NULL) {
			$this->ui->set_error('No input', 'Form input missing');
			return;
		}
		
		$alert = '';
		foreach ($invite_ids as $iid) {
			
			// Current must be connected with it
			if ($this->auth->check(array(
				auth::CurrCONN, $iid
			)) !== TRUE) {
				$alert .= 'Ignoring id '.$iid.'<br />';
				continue;
			}
			
			$is_inv = $this->groups_model->is_invited($iid,$gid);
			if($is_inv === -1){
				$this->ui->set_query_error();
				return;
			}else if ($is_inv){
				$this->ui->set_message('This user has already been invited to the group.');
				$this->ui->set($this->list('mine'));
			}
			
			
			$alert .= 'Inviting id '.$iid.'... ';

			$invitation = $this->groups_model->invite($this->auth->get_account_id(),$iid,$gid);
			if ($invitation === -1){
				$this->ui->set_query_error();
				return;
			}

			// Try to send the email
			$email = $this->account_model->get_account_email(array($iid));
			if($email === -1) {
				$alert .= 'error, could not send invitation email<br />';
				continue;
			}
			$message_body = 'Hello Salute member,<br /><br />'.
				$this->auth->get_first_name().' '.$this->auth->get_last_name().
				' invited you to join group '.$group['name'].'!<br />'.
				'Click <a href="/grups/members/join/'.$gid.'">here</a> to join the group.';
			$this->load->library('email');
			$config['mailtype'] = 'html';
			$this->email->initialize($config);
			$this->email->from($this->auth->get_email());
			$this->email->to($email[0]['email']);
			$this->email->subject('Salute - Group Invitation');
			$this->email->message($message_body);
			$this->email->send();
			
			$alert .= 'email invitation sent!<br />';
		}
		$this->ui->set_message($alert);
		$this->ui->set($this->lists('mine'));
	}
	
	/*function members_invite_hcps_do($gid = NULL) {
		$hcp_ids = $this->input->post('hcp_ids');
		
		// Form input check
		if ($hcp_ids == NULL) {
			$this->ui->set_error('No input', 'Form input missing');
			return;
		}
		
		foreach ($hcp_ids as $hid) {
			echo $hid.' ';
		}
	}*/
	
	/**
	 * List Existing Groups
	 * tested.
	 * */
	function _lists_all(){

		if ($this->auth->check(array(auth::CurrLOG)) !== TRUE) {
			return;
		}
		
		$this->db->trans_start();
		
		$list = $this->groups_model->list_all_groups();
		
		if ($list === -1){
			$this->auth->set_query_error();
			return;
		}

		$member = '';
		for ($i = 0; $i < count($list); $i++) {
			$is_mem = $this->groups_model->is_member($this->auth->get_account_id(),$list[$i]['group_id']); 
			$mem = $this->groups_model->get_member($this->auth->get_account_id(),$list[$i]['group_id']); 
			if ($is_mem === -1 || $mem === -1 ){
				$this->auth->set_query_error();
				return;	
			} else if ($is_mem) {
				$member[$i]['is'] = TRUE; 
				$member[$i]['perm'] = $mem['permissions'];
			} else {
				$member[$i]['is'] = FALSE; 
				$member[$i]['perm'] = NULL;
			}
		}

		$this->ui->set(array($this->load->view('mainpane/lists/groups', array('group_list' => $list, 'member' => $member), TRUE)));
		
		$this->db->trans_complete();
	}
	
	/**
	 * List My Groups
	 * tested.
	 * */
	function _lists_mine(){
		if ($this->auth->check(array(auth::CurrLOG)) !== TRUE) {
			return;
		}
		
		$this->db->trans_start();
		
		$list = $this->groups_model->list_my_groups($this->auth->get_account_id());
		if ($list === -1){
			$this->auth->set_query_error();
			return;
		}
		$perm='';
		for ($i = 0; $i < count($list); $i++) {
			$mem = $this->groups_model->get_member($this->auth->get_account_id(),$list[$i]['group_id']); 
			if ($mem === -1 ){
				$this->auth->set_query_error();
				return;	
			} 
			$perm[$i]['perm'] = $mem['permissions'];
		}
		$this->ui->set(array($this->load->view('mainpane/lists/mygroups', array('group_list' => $list, 'perm' => $perm), TRUE)));
		
		$this->db->trans_complete();
	}
	
	/**
	 * Edit an Existing Group
	 * */
	function edit($group_id = NULL){
		
		if ($this->auth->check(array(auth::CurrLOG,auth::GRP,$group_id,auth::CurrGRPMEM,$group_id)) !== TRUE) {
			return;
		}

		$this->db->trans_start();

		$mem = $this->groups_model->get_member($this->auth->get_account_id(),$group_id);
		if ($mem === -1){
			$this->ui->set_query_error();
			return;
		} else if ($mem === NULL) {
			$this->ui->set_error('You are no longer a member of this group.');
			return;
		} else if ($mem['permissions'] !== '3'){
			$this->ui->set_error('You do not have permission to edit this group.');
			return;
		}
		
		$curr_info = $this->groups_model->get_group($group_id);
		if ($curr_info === -1){
			$this->ui->set_query_error();
			return;
		} else if (count($curr_info) <= 0 || $curr_info === NULL){
			$this->ui->set_error('Internal Server Error','server');
			return;
		}
		$this->ui->set(array($this->load->view('mainpane/forms/edit_group', array('curr_info' => $curr_info, 'group_id' => $group_id), TRUE)));
		
		$this->db->trans_complete();
	}
	
	
	// @bug does not change database
	function edit_do($group_id = NULL){
		if ($this->auth->check(array(auth::CurrLOG,auth::GRP,$group_id,auth::CurrGRPMEM,$group_id)) !== TRUE) {
			return;
		}
		
		$name = $this->input->post('name');
		$description = $this->input->post('description');
		$public_private = $this->input->post('public_private');
		$group_type = $this->input->post('group_type');
		
		// Form Checking will replace this
		if($name == NULL || $description == NULL || $public_private == NULL || $group_type == NULL )	{
			$this->ui->set_error('All Fields are Mandatory.','Missing Arguments'); 
			return;
		}
		
		$this->db->trans_start();
		$result = $this->groups_model->edit_group(array(
													$name, 
													$description, 
													$public_private,
													$group_type,
													$group_id
												)); 
 		$this->db->trans_complete();		
		if($result === -1){
			$this->ui->set_query_error(); 
			return;
		}
		
 		$this->ui->set(array("You have successfully edited the group: $name"));
	}
	
	/**
	 * List members of a group
	 * @bug, you can request connection to yourself...
	 * @bug, actions do not change. e.g. if you request the connection, it still shows you the option of requesting connection. 
	 * 		 similarly for deleting member. 
	 * 
	 * */
	function _members_list($group_id = NULL){

		if ($this->auth->check(array(auth::CurrLOG, auth::GRP,$group_id,auth::CurrGRPMEM,$group_id)) !== TRUE) {
			return;
		}

		$this->db->trans_start();
		
		$list = $this->groups_model->list_members($group_id);
		if ($list === -1){
			$this->ui->set_query_error();
			return;
		}
		
		for ($i = 0; $i < count($list); $i++) {
			$mem = $this->groups_model->get_member($list[$i]['account_id'],$list[$i]['group_id']);
			
			if($mem === -1){
				$this->ui->set_query_error();
				return;
			} else if($this->patient_model->is_patient($mem['account_id'])){
				$pat = $this->patient_model->get_patient($mem['account_id']);
				if ($pat === -1){
					$this->ui->set_query_error();
					return;	
				}
				$info[$i]['first_name'] = $pat[0]['first_name'];
				$info[$i]['last_name'] = $pat[0]['last_name'];
			} else if ($this->hcp_model->is_hcp($mem['account_id'])){
				$hcp = $this->hcp_model->get_hcp($mem['account_id']);
				if ($hcp === -1){
					$this->ui->set_query_error();
					return;	
				}
				$info[$i]['first_name'] = $hcp[0]['first_name'];
				$info[$i]['last_name'] = $hcp[0]['last_name'];	
			} else{
				$this->ui->set_error('Internal server error','server');
				return;
			}
			
		}
		$perm = $this->groups_model->get_member($this->auth->get_account_id(),$group_id);
		$this->db->trans_complete();
		if ($perm === -1){
			$this->ui->set_query_error();
			return;	
		}
		$perm = $perm['permissions'];
		$this->ui->set(array($this->load->view('mainpane/lists/group_members', 
										array('mem_list' => $list, 'perm' => $perm, 'info' => $info),
										TRUE)));
	}
	
	function _members_edit($group_id = NULL, $account_id = NULL){
		if ($this->auth->check(array(auth::CurrLOG,auth::GRP,$group_id,auth::CurrGRPMEM,$group_id)) !== TRUE) {
			return;
		}
		$this->db->trans_start();
		
		$curr_info = $this->groups_model->get_member($this->auth->get_account_id(),$group_id);
		if ($curr_info === -1){
			$this->ui->set_query_error();
			return;
		} else if ($curr_info === NULL) {
			$this->ui->set_error('You are no longer a member of this group.');
			return;
		} else if ($curr_info['permissions'] != '2' && $curr_info['permissions'] != '3' ){
			$this->ui->set_error('You do not have permission to edit this member.');
			return;
		}

		$this->ui->set(array($this->load->view('mainpane/forms/edit_member',array(
																			'curr_info' => $curr_info, 
																			'group_id' => $group_id, 
																			'account_id' => $account_id),
																			 TRUE)));

		$this->db->trans_complete();
	}
	
	function _members_edit_do($group_id = NULL,$account_id = NULL){
		
		if ($this->auth->check(array(auth::CurrLOG,auth::GRP,$group_id,auth::CurrGRPMEM,$group_id)) !== TRUE) {
			return;
		}
		$perm = $this->input->post('permissions');
		
		// Form Checking will replace this
		if($perm == NULL)	{
			$this->ui->set_error('All Fields are Mandatory.','Missing Arguments'); 
			return;
		}

		$this->db->trans_start();
		
		$result = $this->groups_model->edit_member($account_id, $group_id, $perm); 
		
		$this->db->trans_complete();
		if($result === -1){
			$this->ui->set_query_error(); 
			return;
		}

		$this->ui->set_message('You have successfully edited the member','Confirmation');
		$this->members('list', $group_id);
	}
}
/** @} */
?>

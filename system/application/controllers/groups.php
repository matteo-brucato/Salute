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
 * */
class Groups extends Controller {

	function __construct() {
		parent::Controller();
		$this->load->library('ui');
		$this->load->library('auth');
		$this->load->model('groups_model');
	}
	
	/**
	 * Default method:
	 * 		List all groups
	 * */
	function index(){
		$this->groups('all');
	}
	
	function groups($direction = 'all' , $subdir = 'all', $group_id = NULL, $account_id = NULL){
		if ( $direction = 'all' )
			$this->_groups_all();
		else if ( $direction = 'mine' )
			$this->_groups_mine();
		else if ( $direction = 'create' )
			$this->_groups_create();
		else if ( $direction = 'create_do' )
			$this->_groups_create_do();
		else if ( $direction = 'delete' )
			$this->_groups_delete($group_id);
		else if ( $direction = 'edit' )		
			$this->_groups_edit($group_id);
		else if ( $direction = 'edit_do' )		
			$this->_groups_edit_do($group_id);
		else if ( $direction = 'members' )
			$this->_groups_members($subdir,$group_id, $account_id);
		else
			$this->ui->set_error('Input not valid: <b>'.$param.'</b>');
	}
	
	function _groups_members($subdir = 'all', $group_id = NULL, $account_id = NULL){
		if ( $subdir = 'all' )
			$this->_members_all($group_id);
		else if ( $subdir = 'edit' )
			$this->_members_edit($group_id,$account_id);
		else if ( $subdir = 'edit_do' )
			$this->_members_edit($group_id,$account_id);
		else if ( $subdir = 'join' )
			$this->_members_join($group_id);
		else if ( $subdir = 'leave' )
			$this->_members_leave($group_id);
		else if ( $subdir = 'delete' )		
			$this->_members_delete($group_id,$account_id);
		else if ( $subdir = 'invite' )		
			$this->_members_invite($group_id,$account_id);
		else
			$this->ui->set_error('Input not valid: <b>'.$subdir.'</b>');	
	}
	
	
	/**
	 * Loads Create New Group Form
	 * */
	function _groups_create(){

		if ($this->auth->check(array(auth::CurrLOG)) !== TRUE) {
			return;
		}
		$this->ui->set(array($this->load->view('mainpane/forms/create_group', '', TRUE), ''));
	}


	/**
	 * Create a New Group
	 * */
	function _groups_create_do(){

		$name = $this->input->post('name');
		$description = $this->input->post('description');
		$privacy = $this->input->post('public_private');
		$group_type = $this->input->post('group_type');
		
		// Form Checking will replace this
		if($name == NULL || $description == NULL || $privacy == NULL || $group_type == NULL )	{
			$this->ui->set_error('All Fields are Mandatory.','Missing Arguments'); 
			return;
		}
		
		$this->db->trans_start();
		
		$result = $this->groups_model->create(array(
													'account_id' => $this->auth->get_account_id(),
													'name' => $name, 
													'description' => $description, 
													'public_private' => $privacy,
													'group_type' => $group_type,
												)); 
		
		if($result === -1){
			$this->ui->set_query_error(); 
			return;
		}
		
		$this->ui->set_message("You have successfully created the group: $name",'Confirmation');
		
		$this->db->trans_complete();
	}

	
	/**
	 * Delete an Existing Group
	 * */
	function _groups_delete($group_id = NULL){
		
		if ($this->auth->check(array(auth::CurrLOG, auth::GRP, $group_id)) !== TRUE) {
			return;
		}
		
		// check that they have permission
		
		$this->db->trans_start();
				
		$result = $this->groups_model->delete(array($group_id));
		
		if ($result === -1){
				$this->auth->set_query_error();
				return;
		}
	
		//@todo later...make it fancy.
		$this->ui->set_message('The group has been deleted.','Confirmation');
		
		$this->db->trans_complete();
	}
	
	/**
	 * Join an Existing Group
	 * */
	function _members_join($group_id = NULL){
		if ($this->auth->check(array(auth::CurrLOG,auth::GRP,$group_id)) !== TRUE) return;
		
		$this->db->trans_start();
		
		$mem = $this->groups_model->is_member($this->auth->get_account_id(),$group_id);
		if($mem === -1){
			$this->ui->set_query_error();
			return;
		} else if ($mem){ 
			$this->ui->set_error('You are already a member of this group.','notice');
			return;
		} else if ($mem === FALSE){
			$check = $this->groups_model->join($this->auth->get_account_id(),$group_id);
			if ($check === -1){
				$this->ui->set_query_error();
				return;	
			}
		} else {
			$this->ui->set_error('Internal Server Error','server');
			return;
		}
		$this->db->trans_complete();
		
		// check again that they're in 'is_in'
		if ($this->auth->check(array(auth::CurrGRPMEM,$group_id)) === TRUE){
			$this->ui->set_message('You have successfully joined the group.','Confirmation');
			$this->groups('mine');
		} else {
			$this->ui->set_error('Internal Server Error','server');
			return;
		}
	}
	
	/**
	 * Group Member Leave from the Existing Group
	 * */
	function _members_leave($group_id = NULL){

		if ($this->auth->check(array(
										auth::CurrLOG, 
										auth::CurrGRPMEM, $this->auth->get_account_id()
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
		$this->groups('mine');
	}
	
	// a member is being removed by the group admin
	function _members_delete($group_id = NULL, $account_id = NULL){
	
		if ($this->auth->check(array(auth::CurrLOG)) !== TRUE) return;
		
		$this->db->trans_start();
		
		// check that current member has permission to do this
			
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
		
		if ($this->auth->check(array(auth::CurrGRPMEM,$group_id)) !== TRUE){
			$this->ui->set_message('You have successfully left the group.','Confirmation');
			$this->groups('members');
		} else {
			$this->ui->set_error('Internal Server Error','server');
			return;
		}
	}
	
	/**
	 * Allow Invitations to a Group
	 * @attention invite must be sent to a Salute Member
	 * @attention invite may only be sent by: permission #s 1,2,3 (all except 0)
	 * */
	function invite($aid = NULL) {
		if ($this->auth->check(array(auth::CurrLOG)) !== TRUE) {
			return;
		}
		
		if ($aid === NULL) {
			$this->ui->set(array(
				$this->load->view('mainpane/forms/pick_patient',
					array('list_name' => 'a', 'list' => array(), 'hcp_id' => 12), TRUE)
			));
		}
	}
	
	/**
	 * List Existing Groups
	 * */
	function _groups_all(){

		if ($this->auth->check(array(auth::CurrLOG)) !== TRUE) {
			return;
		}
		
		$this->db->trans_start();
		
		$list = $this->groups_model->list_all_groups();
		
		if ($list === -1){
			$this->auth->set_query_error();
			return;
		}

		for ($i = 0; $i < count($list); $i++) {
			
			$mem = $this->groups_model->is_member($this->auth->get_account_id(),$list[$i]['group_id']); 
			if ($mem === -1){
				$this->auth->set_query_error();
				return;	
			} else if ($mem) {
				$member[$i]['is'] = TRUE; 
			} else 
				$member[$i]['is'] = FALSE; 
		}

		$this->ui->set(array($this->load->view('mainpane/lists/groups', array('group_list' => $list, 'member' => $member), TRUE)));
		
		$this->db->trans_complete();
	}
	
	/**
	 * List My Groups
	 * */
	function _groups_mine(){
		if ($this->auth->check(array(auth::CurrLOG)) !== TRUE) {
			return;
		}
		
		// Start a transaction now
		$this->db->trans_start();
		//$this->db->trans_begin();
		
		$list = $this->groups_model->list_my_groups($this->auth->get_account_id());
		if ($list === -1){
			$this->auth->set_query_error();
			return;
		}
		$perm='';
		for ($i = 0; $i < count($list); $i++) {
			if ($this->groups_model->can_delete(
												$this->auth->get_account_id(),
												$list[$i]['group_id'] 
									)) 
			{
				$perm[$i]['can_delete'] = TRUE; 
			} else
				$perm[$i]['can_delete'] = FALSE; 
		}
		$this->ui->set(array($this->load->view('mainpane/lists/mygroups', array('group_list' => $list, 'perm' => $perm), TRUE)));
		
		$this->db->trans_complete();
	}
	
	/**
	 * Edit an Existing Group
	 * */
	function _groups_edit($group_id = NULL){

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
		} else if ($mem[0]['permissions'] !== '3'){
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
		$this->ui->set(array($this->load->view('mainpane/forms/edit_group', array('curr_info' => $curr_info), TRUE)));
		
		$this->db->trans_complete();
	}
	
	function _groups_edit_do($group_id = NULL){

		$name = $this->input->post('name');
		$description = $this->input->post('description');
		$privacy = $this->input->post('public_private');
		$group_type = $this->input->post('group_type');
		
		// Form Checking will replace this
		if($name == NULL || $description == NULL || $privacy == NULL || $group_type == NULL )	{
			$this->ui->set_error('All Fields are Mandatory.','Missing Arguments'); 
			return;
		}
		
		$this->db->trans_start();
		
		$result = $this->groups_model->edit_group(array(
													'name' => $name, 
													'description' => $description, 
													'public_private' => $privacy,
													'group_type' => $group_type,
													'group_id' => $group_id
												)); 
		
		if($result === -1){
			$this->ui->set_query_error(); 
			return;
		}
		
		$this->ui->set_message("You have successfully edited the group: $name",'Confirmation');
		
 		$this->db->trans_complete();
	}
	
	/**
	 * List members of a group
	 * */
	function _members_all($group_id = NULL){

		if ($this->auth->check(array(auth::CurrLOG,auth::CurrGRPMEM,$group_id)) !== TRUE) {
			return;
		}

		$this->db->trans_start();
		
		$list = $this->groups_model->list_members($group_id);
		if ($list === -1){
			$this->ui->set_query_error();
			return;
		}
		
		for ($i = 0; $i < count($list); $i++) {
			$perm = $this->groups_model->get_member($this->auth->get_account_id(),$list[$i]['group_id']);

			if($perm === -1){
				$this->ui->set_query_error();
				return;
			}
		}
		
		$this->ui->set(array($this->load->view('mainpane/lists/group_members', array('mem_list' => $list, 'perm' => $perm), TRUE)));
		
		$this->db->trans_complete();
	}
	
	function _members_edit($group_id = NULL, $account_id = NULL){

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
		} else if ($mem[0]['permissions'] !== '2' || $mem[0]['permissions'] !== '3' ){
			$this->ui->set_error('You do not have permission to edit this member.');
			return;
		}

		/* @todo learn how to load curr info into a radio button's value */
		$this->ui->set(array($this->load->view('mainpane/forms/edit_member','', TRUE)));

		$this->db->trans_complete();
	}
	
	function _members_edit_do($group_id = NULL,$account_id = NULL){

		$perm = $this->input->post('permissions');
		
		// Form Checking will replace this
		if($perm == NULL)	{
			$this->ui->set_error('All Fields are Mandatory.','Missing Arguments'); 
			return;
		}
		
		$this->db->trans_start();
		
		$result = $this->groups_model->edit_member(array(
													'account_id' => $account_id, 
													'group_id' => $group_id, 
													'permissions' => $perm, 
												)); 
		
		if($result === -1){
			$this->ui->set_query_error(); 
			return;
		}

		/*todo: link back to members list*/
		$this->ui->set_message('You have successfully edited the member','Confirmation');
		
 		$this->db->trans_complete();
	}
}
/** @} */
?>

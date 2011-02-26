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
 * Class Controller Connections
 * 
 * @test The whole class has been succesfully tested.
 * @bug No known bugs reported
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
		$this->list_groups();
	}
	
	/**
	 * Loads Create New Group Form
	 * */
	function create(){

		if ($this->auth->check(array(auth::CurrLOG)) !== TRUE) {
			return;
		}
		$this->ui->set(array($this->load->view('mainpane/forms/create_group', '', TRUE), ''));
	}


	/**
	 * Create a New Group
	 * */
	function create_do(){

		if ($this->auth->check(array(auth::CurrLOG)) !== TRUE) {
			return;
		}

		$name = $this->input->post('name');
		$description = $this->input->post('description');
		$privacy = $this->input->post('public_private');
		$group_type = $this->input->post('group_type');
		
		// Form Checking will replace this
		if($name == NULL || $description == NULL || $privacy == NULL || $group_type == NULL )	{
			$this->ui->set_error('All Fields are Mandatory.','Missing Arguments'); 
			return;
		}
		
		// Start a transaction now
		$this->db->trans_start();
		//$this->db->trans_begin();
		
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
		
		// End transaction
		$this->db->trans_complete();
		//$this->db->trans_rollback();
	}

	
	/**
	 * Delete an Existing Group
	 * */
	function delete($group_id = NULL){
		
		if ($this->auth->check(array(auth::CurrLOG)) !== TRUE) {
			return;
		}
		
		// check that group_id is right type and not null
		// check that they have permission
		
		// Start a transaction now
		$this->db->trans_start();
		//$this->db->trans_begin();
				
		$result = $this->groups_model->delete(array($group_id));
		
		if ($result === -1){
				$this->auth->set_query_error();
				return;
		}
	
		//@todo later...make it fancy.
		$this->ui->set_message('The group has been deleted.','Confirmation');
		
		// End transaction
		$this->db->trans_complete();
		//$this->db->trans_rollback();
	}
	
	/**
	 * Join an Existing Group
	 * */
	function join(){

		if ($this->auth->check(array(auth::CurrLOG)) !== TRUE) {
			return;
		}
	}
	
	/**
	 * Group Member Leave from the Existing Group
	 * */
	function leave($group_id){

		if ($this->auth->check(array(auth::CurrLOG)) !== TRUE) {
			return;
		}
		
		// Start a transaction now
		$this->db->trans_start();
		//$this->db->trans_begin();
		
		$mem = $this->groups_model->is_member($this->auth->get_account_id(),$group_id);
		if($mem === -1){
			$this->ui->set_query_error();
			return;
		} else if ($mem){
			$check = $this->groups_model->leave($this->auth->get_account_id(),$group_id);
			if ($check === -1){
				$this->ui->set_query_error();
				return;	
			}
		} else {
			$this->ui->set_error('Internal Server Error','server');
			return;
		}
		
		$this->ui->set_message('You have successfully left the group.','Confirmation');
		$this->list_my_groups();
		// Start a transaction now
		$this->db->trans_start();
		//$this->db->trans_begin();
	}
	
	/**
	 * Allow Invitations to a Group
	 * @attention invite must be sent to a Salute Member
	 * @attention invite may only be sent by: permission #s 1,2,3 (all except 0)
	 * */
	function invite(){

		if ($this->auth->check(array(auth::CurrLOG)) !== TRUE) {
			return;
		}
	}
	
	/**
	 * List Existing Groups
	 * */
	function list_groups(){

		if ($this->auth->check(array(auth::CurrLOG)) !== TRUE) {
			return;
		}
		
		// Start a transaction now
		$this->db->trans_start();
		//$this->db->trans_begin();
		
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
		
		// End transaction
		$this->db->trans_complete();
		//$this->db->trans_rollback();
	}
	
	/**
	 * List My Groups
	 * */
	function list_my_groups(){

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
		
		// End transaction
		$this->db->trans_complete();
		//$this->db->trans_rollback();
	}
	
	/**
	 * Edit an Existing Group
	 * */
	function edit($group_id){

		if ($this->auth->check(array(auth::CurrLOG)) !== TRUE) {
			return;
		}
		// check if group exists
		// if they're a member with permission 3 (make is_admin fn?)
		// load form : edit group name/description/type		
		
		// Start a transaction now
		$this->db->trans_start();
		//$this->db->trans_begin();
		
		$curr_info = $this->groups_model->get_group($group_id);
		if ($curr_info === -1){
			$this->ui->set_query_error();
			return;
		} else if (count($curr_info) <= 0 || $curr_info === NULL){
			$this->ui->set_error('Internal Server Error','server');
			return;
		}
		$this->ui->set(array($this->load->view('mainpane/forms/edit_group', array('curr_info' => $curr_info), TRUE)));
		
		// End transaction
		$this->db->trans_complete();
		//$this->db->trans_rollback();
	}
	
	function edit_do($group_id){

		if ($this->auth->check(array(auth::CurrLOG)) !== TRUE) {
			return;
		}
		
		// Check Form values
		// call edit fn
		// confirmation message
		
		$name = $this->input->post('name');
		$description = $this->input->post('description');
		$privacy = $this->input->post('public_private');
		$group_type = $this->input->post('group_type');
		
		// Form Checking will replace this
		if($name == NULL || $description == NULL || $privacy == NULL || $group_type == NULL )	{
			$this->ui->set_error('All Fields are Mandatory.','Missing Arguments'); 
			return;
		}
		
		// Start a transaction now
		$this->db->trans_start();
		//$this->db->trans_begin();
		
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
		
		// End transaction
 		$this->db->trans_complete();
		//$this->db->trans_rollback();
	}
	
	/**
	 * List members of a group
	 * */
	function list_members($group_id){

		if ($this->auth->check(array(auth::CurrLOG)) !== TRUE) {
			return;
		}

		// Start a transaction now
		$this->db->trans_start();
		//$this->db->trans_begin();
		
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
		
		// End transaction
		$this->db->trans_complete();
		//$this->db->trans_rollback();
	}
	
	function change_member_permissions($account_id,$group_id){

		if ($this->auth->check(array(auth::CurrLOG)) !== TRUE) {
			return;
		}
		
		// if member and if member is perm 2-3
		// load form
	
	}
	
	function change_member_permissions_do(){

		if ($this->auth->check(array(auth::CurrLOG)) !== TRUE) {
			return;
		}
		
		// form check
		// submit changes
		// Success Msg, link back to member list
	}
	function delete_member($group_id,$account_id){}
	
}
/** @} */
?>

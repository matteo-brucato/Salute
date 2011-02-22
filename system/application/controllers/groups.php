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

		if ($this->auth->check(array(auth::CurrLOG)) !== TRUE) {
			return;
		}
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
		
		// End transaction
		$this->db->trans_complete();
		//$this->db->trans_rollback();
	}

	
	/**
	 * Delete an Existing Group
	 * */
	function delete($group_id){
		
		if ($this->auth->check(array(auth::CurrLOG)) !== TRUE) {
			return;
		}
		
		// check that group_id is right type and not null

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
	function leave(){

		if ($this->auth->check(array(auth::CurrLOG)) !== TRUE) {
			return;
		}
	}
	
	/**
	 * Requests to Join an Existing Group
	 * @attention only can request if the group is public
	 * */
	function request(){
		
		if ($this->auth->check(array(auth::CurrLOG)) !== TRUE) {
			return;
		}
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
			if ($this->groups_model->is_member(
												$this->auth->get_account_id(),
												$list[$i]['group_id'] 
									)) 
			{
				$member[$i]['is'] = TRUE; //$list[$i]['member'] = TRUE;
			} else 
				$member[$i]['is'] = FALSE; //$list[$i]['member'] = FALSE;
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
	}
	
	/**
	 * Edit an Existing Group
	 * */
	function edit(){

		if ($this->auth->check(array(auth::CurrLOG)) !== TRUE) {
			return;
		}
	}
	
	/**
	 * List members of a group
	 * */
	function list_members(){

		if ($this->auth->check(array(auth::CurrLOG)) !== TRUE) {
			return;
		}
	}
	
	function change_member_permissions(){

		if ($this->auth->check(array(auth::CurrLOG)) !== TRUE) {
			return;
		}
	
	}
	
	
}
/** @} */
?>

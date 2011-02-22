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
	 * Create a New Group
	 * */
	function create(){

		if ($this->auth->check(array(auth::CurrLOG)) !== TRUE) {
			return;
		}

		$this->ui->set(array($this->load->view('mainpane/forms/create_group', '', TRUE), ''));
	}
	
	/**
	 * Delete an Existing Group
	 * */
	function delete(){
		
		if ($this->auth->check(array(auth::CurrLOG)) !== TRUE) {
			return;
		}
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

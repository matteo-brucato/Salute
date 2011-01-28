<?php
class Bills extends Controller {

	function __construct(){
		parent::Controller();
		$this->load->library('ajax');	
		$this->load->library('auth');
	}

	/* Default: call all function */
	function index(){
		$this->auth->check_logged_in();
	}
  	
	/* List all bills  */      	   
	function all()	{
		$this->auth->check_logged_in();
		// if Doctor --> Additional Option: list all bills of a specific patient id
		// 	e.g. Doctor wants to see the all bills a specific patient has with him/her
	}

	/* List Current Bills */
	function current(){
		$this->auth->check_logged_in();
		// if Doctor --> Additional Option: list all current bills of a specific patient id
		// 	e.g. Doctor wants to see the current bills a specific patient owes him/her
	}

	/* Lists past bills */	
	function past() {
		$this->auth->check_logged_in();
		// if Doctor --> Additional Option: list all past bills of a specific patient id
		// 	e.g. Doctor wants to see all the bills a specific patient paid him/her
	}

	// load form , charge patient an amount for an procedure/appointment/test, upload itemized receipt	
	// update database
	// Only available for doctors
	function issue_new_bill() {
		$this->auth->check_logged_in();
	}

	// only available to patient: pay a bill
	function pay() {
		$this->auth->check_logged_in();	
	}

}
?>

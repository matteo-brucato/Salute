<?php
class Appointments extends Controller {

	function __construct(){
		parent::Controller();
		$this->load->library('ajax');	
		$this->load->library('auth');
	}

	// Default: load upcoming function
	function index()
	{}  	

	/* request an appointment */      	   
	function request(){
		$this->auth->check_logged_in();
	}

	/* List all Appointments */
	function all() {
		$this->auth->check_logged_in();
	}

	/* List Upcoming Appointments */
	function upcoming(){
		$this->auth->check_logged_in();
	}

	/* List Past Appointments */
	function past(){
		$this->auth->check_logged_in();
	}

	/*Cancel an existing appointment */	
	function cancel() {
		$this->auth->check_logged_in();
	}
	
	/*Reschedule an existing appointment (date/time) */
	function reschedule(){
		$this->auth->check_logged_in();
	}
}
?>

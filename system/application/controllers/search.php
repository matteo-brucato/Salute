<?php
class Search extends Controller {

//	private $type;

	function __construct(){
		parent::Controller();
		$this->load->library('ajax');	
		$this->load->library('auth');
		// $this->type = $this->session->userdata('type');
	}

	// Default to the advanced search
	function index() {
		$this->auth->check_logged_in();
	}

	// Search for doctor
	function hcp(){
		$this->auth->check_logged_in();
	}

	// Search for patients 
	// Note: Only for doctors, should only show patients they are connected with
	function patient(){ 
		$this->auth->check_logged_in();
	}

	// Search in my messages
	function message() {
		$this->auth->check_logged_in();
	}

	// Search in medical records
	// Note: if patient --> only search in my records
	//	 if doctor --> only search in records i have access to
	function medical_record() {
		$this->auth->check_logged_in();
	}

}
?>

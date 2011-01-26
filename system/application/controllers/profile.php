<?php
class Profile extends Controller {

	function __constructor(){
		parent::Controller();
		$this->load->library('ajax');	
		//check if you're logged in	
	}

	// Default
	function index() {
		// if patient_login -> load view of patient main panel + side panel
		
		// if doctor_login -> load view of doctor main panel + side panel
	}

	// My info... Name, Weight, Date of Birth, Height...	
	function myinfo()
	{}

	
	// loads form that allows me to edit my info
	function edit()
	{}

	// submits edits to database
	function make_edits()
	{}

}
?>

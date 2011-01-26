<?php
class Profile extends Controller {

	function __constructor(){
		parent::Controller();
		$this->load->library('ajax');	
		//check if you're logged in	
	}

	// Default
	function index() {
		// if patient_login -> load view of patient main panel + side panel + navbar
		$this->ajax->view(array(
			$this->load->view('mainpane/patient-profile', '', TRUE),
			$this->load->view('sidepane/patient-profile', '', TRUE)
		));
		// if doctor_login -> load view of doctor main panel + side panel + navbar
		$this->ajax->view(array(
			$this->load->view('mainpane/doctor-profile', '', TRUE),
			$this->load->view('sidepane/doctor-profile', '', TRUE)
		));
	}

	// My info... Name, Weight, Date of Birth, Height...	
	function myinfo()
	{
		// if patient_login -> load view of patient main panel + side panel + navbar
		$this->ajax->view(array(
			$this->load->view('mainpane/patient-info', '', TRUE),
			$this->load->view('sidepane/patient-profile', '', TRUE)
		));
		// if doctor_login -> load view of doctor main panel + side panel + navbar
		$this->ajax->view(array(
			$this->load->view('mainpane/doctor-info', '', TRUE),
			$this->load->view('sidepane/doctor-profile', '', TRUE)
		));		
	}

	
	// loads form that allows me to edit my info
	function edit()
	{}

	// submits edits to database
	function make_edits()
	{}

}
?>

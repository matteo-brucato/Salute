<?php
class Profile extends Controller {

	private
		$type;

	function __constructor(){
		parent::Controller();
		$this->load->library('ajax');	
		$this->type = $this->session->userdata('type');
	}

	function index() {
		if ($type === 'patient') {
			$this->ajax->view(array(
				$this->load->view('mainpane/patient-profile', '', TRUE),
				$this->load->view('sidepane/patient-profile', '', TRUE)
			));
		}
		else if ($type === 'doctor') {
			$this->ajax->view(array(
				$this->load->view('mainpane/doctor-profile', '', TRUE),
				$this->load->view('sidepane/doctor-profile', '', TRUE)
			));
		}
	}

	function myinfo()
	{
		if ($type === 'patient') {
			$this->ajax->view(array(
				$this->load->view('mainpane/patient-info', '', TRUE),
				$this->load->view('sidepane/patient-profile', '', TRUE)
			));
		}
		else if ($type === 'doctor') {		
			$this->ajax->view(array(
				$this->load->view('mainpane/doctor-info', '', TRUE),
				$this->load->view('sidepane/doctor-profile', '', TRUE)
			));		
		}	
	}

	
	// loads form that allows me to edit my info
	function edit()
	{}

	// submits edits to database
	function make_edits()
	{}

}
?>

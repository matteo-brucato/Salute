<?php
class Profile extends Controller {

	private $type;

	function __construct(){
		parent::Controller();
		$this->load->library('ajax');	
		$this->type = $this->session->userdata('type');
	}

	function index() {
		if ($this->type === 'patient') {
			$this->ajax->view(array(
				$this->load->view('mainpane/patient-profile', '', TRUE),
				$this->load->view('sidepane/patient-profile', '', TRUE)
			));
		echo "I am a patient!";
		}

		else if ($this->type === 'doctor') {
			$this->ajax->view(array(
				$this->load->view('mainpane/doctor-profile', '', TRUE),
				$this->load->view('sidepane/doctor-profile', '', TRUE)
			));
		echo "I am a doctor!";
		}

		else {
			echo "Do some error"; 
		}

		// Fancy Features: pass notifications from model to view via the 2nd parameter in the load->view call. 
	}

	function myinfo()
	{
		if ($this->type === 'patient') {
			$this->ajax->view(array(
				$this->load->view('mainpane/patient-info', '', TRUE),
				$this->load->view('sidepane/patient-profile', '', TRUE)
			));
		}
		else if ($this->type === 'doctor') {		
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

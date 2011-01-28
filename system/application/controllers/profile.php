<?php
class Profile extends Controller {

	function __construct(){
		parent::Controller();
		$this->load->library('ajax');	
		$this->load->library('auth');
	}

	function index() {

		$this->auth->check_logged_in();

		if ($this->auth->get_type() === 'patient') {
			$this->ajax->view(array(
				$this->load->view('mainpane/patient-profile', '', TRUE),
				$this->load->view('sidepane/patient-profile', '', TRUE)
			));
		}

		else if ($this->auth->get_type() === 'doctor') {
			$this->ajax->view(array(
				$this->load->view('mainpane/doctor-profile', '', TRUE),
				$this->load->view('sidepane/doctor-profile', '', TRUE)
			));
		}

		else {
			show_error('Access to this page not allowed', 500);
			return;
		}

		// Fancy Features: pass notifications from model to view via the 2nd parameter in the load->view call. 
	}

	function myinfo()
	{

		$this->auth->check_logged_in();

		if ($this->auth->get_type() === 'patient') {
			$this->ajax->view(array(
				$this->load->view('mainpane/patient-info', '', TRUE),
				$this->load->view('sidepane/patient-profile', '', TRUE)
			));
		}
		else if ($this->auth->get_type() === 'doctor') {		
			$this->ajax->view(array(
				$this->load->view('mainpane/doctor-info', '', TRUE),
				$this->load->view('sidepane/doctor-profile', '', TRUE)
			));		
		}	
		else{
			show_error('Unknown Error.', 500);
			return;
		}
	}

	function user($id){
		$this->auth->check_logged_in();

		if ($this->auth->get_type() === 'doctor' ){

			/* only should work if they are connected!
			NEED A MODEL AND IS CONNECTED FUNCTION
			$this->load->model('_______');  
			$check = $this->____->is_connected(array('id' => $id)); 
			*/
			$this->ajax->view(array(
				$this->load->view('mainpane/see_patient', '', TRUE),  /* pass in account id! */
				$this->load->view('sidepane/doctor-profile', '', TRUE)
			));

		}

		else if ($this->auth->get_type() === 'patient' ){
			$this->ajax->view(array(
				$this->load->view('mainpane/see_doctor', '', TRUE), 
				$this->load->view('sidepane/patient-profile', '', TRUE)
			));
		}

		else{
			show_error('Unknown Error.', 500);
			return;
		}
	}

	// loads form that allows me to edit my info
	function edit() {
		$this->auth->check_logged_in();
	}

	// submits edits to database
	function make_edits() {
		$this->auth->check_logged_in();
	}

}
?>

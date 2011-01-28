<?php
class Appointments extends Controller {

	function __construct(){
		parent::Controller();
		$this->load->library('ajax');	
		$this->load->library('auth');
		$this->load->model('appointments_model');
	}

	// Default: load upcoming function
	function index(){
		$this->auth->check_logged_in();
		$this->ajax->redirect('/appointments/upcoming');
	}  	

	/* List all Appointments */
	function all() {
		$this->auth->check_logged_in();

		$results = $this->appointments_model->view_all(array(
									'account_id' => $this->auth->get_account_id(),
									'type' => $this->auth->get_type()
								)); 
		$this->ajax->view(array(
					'',
					$this->load->view('mainpane/list_appointments', $results , TRUE)
				));
	}


	/* List Upcoming Appointments */
	function upcoming(){
		$this->auth->check_logged_in();

		$results = $this->appointments_model->view_upcoming(array(
									'account_id' => $this->auth->get_account_id(),
									'type' => $this->auth->get_type()
								)); 
		$this->ajax->view(array(
					'',
					$this->load->view('mainpane/list_appointments', $results , TRUE)
				));
	}

	/* List Past Appointments */
	function past(){
		$this->auth->check_logged_in();

		$results = $this->appointments_model->view_past(array(
									'account_id' => $this->auth->get_account_id(),
									'type' => $this->auth->get_type()
								)); 
		$this->ajax->view(array(
					'',
					$this->load->view('mainpane/list_appointments', $results , TRUE)
				));

	}

	/*Cancel an existing appointment */	
	function cancel($apt_id) {
		$this->auth->check_logged_in();
		$results = $this->appointments_model->cancel(array('appointment_id' => $apt_id)); 
		// TODO: Refresh page
	}
	
	/*Reschedule an existing appointment (date/time) */
	// TODO: need reschedule appt form / view
	function reschedule($apt_id){
		$this->auth->check_logged_in();
		$this->ajax->view(array(
					'',
					$this->load->view('mainpane/reschedule_appointments', $results , TRUE)
				));
		$new_time = $this->input->post('appointment_time');
		$results = $this->appointments_model->reschedule(array('appointment_id' => $apt_id, 'new_time' => $new_time )); 
	}

	/* request an appointment */      	   
	//TODO: view for appt request ; 
	// expect an array with doctorid, reason for appointment, time
	function request(){
		$this->auth->check_logged_in();

		$this->ajax->view(array(
					'',
					$this->load->view('mainpane/request_appointments', '' , TRUE)
				));
		$hcp_id = $this->input->post('hcp_id');
		$desc = $this->input->post('description');
		$time = $this->input->post('time');
		$results = $this->appointments_model->reschedule(array( 'patient_id' => $this->auth->get_account_id(), 
									'hcp_id' => $hcp_id , 
									'desc' => $desc ,
									'time' => $time 
								)); 
		// Fancy: send confirmation 
	}
}
?>

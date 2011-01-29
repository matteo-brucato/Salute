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

	/* List all Appointments 
		@todo: view to list appointments: Date Time Descrip Doctor Name Actions(Reschedule,Cancel)	
	*/
	function all() {
		$this->auth->check_logged_in();

		$results = $this->appointments_model->view_all(array(
									'account_id' => $this->auth->get_account_id(),
									'type' => $this->auth->get_type()
								)); 
		$this->ajax->view(array(
					$this->load->view('mainpane/____', $results , TRUE),
					''
				));
	}


	/* List Upcoming Appointments
		@todo: view to list appointments: Date Time Descrip Doctor Name Actions(Reschedule,Cancel)	
	*/
	function upcoming(){
		$this->auth->check_logged_in();

		$results = $this->appointments_model->view_upcoming(array(
									'account_id' => $this->auth->get_account_id(),
									'type' => $this->auth->get_type()
								)); 
		$this->ajax->view(array($this->load->view('mainpane/_____', $results , TRUE),''));
	}

	/* List Past Appointments */
	function past(){
		$this->auth->check_logged_in();

		$results = $this->appointments_model->view_past(array(
									'account_id' => $this->auth->get_account_id(),
									'type' => $this->auth->get_type()
								)); 
		$this->ajax->view(array($this->load->view('mainpane/table_result', $results , TRUE),''));
	}

	/*Cancel an existing appointment */	
	function cancel($apt_id) {
		$this->auth->check_logged_in();
		$this->load->model('appointments_model');
		if($this->appointments_model->is_myappointment(array($this->auth->get_account_id(),$apt_id))){
			/* @to do: pop up-- are you sure you want to cancel appointment?*/
			$results = $this->appointments_model->cancel(array($apt_id)); 
			$this->ajax->redirect('/appointments/upcoming');
		}
		else{
			show_error('This is not your appointment. Permission Denied.', 500);
			return;
		}
		
	}
	
	/*Reschedule an existing appointment (date/time) */
	// @todo: need reschedule appt form / view
	// Later: we should have a request reschedule and accept reschedule 
	function reschedule($apt_id){
		$this->auth->check_logged_in();
		$this->load->model('appointments_model');
		if($this->appointments_model->is_myappointment(array($this->auth->get_account_id(),$apt_id))){
			$result = $this->appointments_model->get_appointment(array($apt_id));
			$this->ajax->view(array($this->load->view('mainpane/________',$result, TRUE),''));
			$new_time = $this->input->post('appointment_time');
			$results = $this->appointments_model->reschedule(array('appointment_id' => $apt_id, 'date_time' => $new_time )); 
		}
		else{
			show_error('This is not your appointment. Permission Denied.', 500);
			return;
		}
	}

	/* request an appointment */      	   
	//TODO: view for appt request ; 
	// expect an array with doctorid, reason for appointment, time
	/* Fancy: send confirmation */
	// do the same thing as a connection request...wont implement this till that one is figured out 
	function request(){
		$this->auth->check_logged_in();

		$this->ajax->view(array($this->load->view('mainpane/__________', '' , TRUE), ''));
		$hcp_id = $this->input->post('hcp_id');
		$desc = $this->input->post('description');
		$time = $this->input->post('time');
		$results = $this->appointments_model->reschedule(array( 'patient_id' => $this->auth->get_account_id(), 
									'hcp_id' => $hcp_id , 
									'desc' => $desc ,
									'time' => $time 
								)); 
	}
}
?>

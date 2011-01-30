<?php
/**
 * @file appointments.php
 * @brief Controller to handle appointments
 *
 * @defgroup ctr Controllers
 * @ingroup ctr
 * @{
 */

class Appointments extends Controller {

	function __construct(){
		parent::Controller();
		$this->load->library('ajax');	
		$this->load->library('auth');
		$this->load->model('appointments_model');
	}

	/*
	 * fn index -- default
	 * checks if user is logged in. 
	 * redirects to page to list upcoming appointments
	 * */
	function index(){
		$this->auth->check_logged_in();
		$this->ajax->redirect('/appointments/upcoming');
	}  	

	/*
	 * fn all 
	 * lists all appointments of the logged in user
	 * @todo: need a view to list appointments: Date Time Descrip Doctor Name Actions(Reschedule,Cancel)	
	 * */
	function all() {
		$this->auth->check_logged_in();

		$results = $this->appointments_model->view_all(array(
									'account_id' => $this->auth->get_account_id(),
									'type' => $this->auth->get_type()
								)); 
		if($results == NULL){
			$this->ajax->view(array('No appointments. ',''));
		}
		$this->ajax->view(array($this->load->view('mainpane/____', $results , TRUE),''));
	}

	/*
	 * fn upcoming 
	 * lists all upcoming appointments of the logged in user
	 * @todo: need a view to list appointments: Date Time Descrip Doctor Name Actions(Reschedule,Cancel)	
	 * */
	function upcoming(){
		$this->auth->check_logged_in();

		$results = $this->appointments_model->view_upcoming(array(
									'account_id' => $this->auth->get_account_id(),
									'type' => $this->auth->get_type()
								)); 
		if($results == NULL){
			$this->ajax->view(array('You have no upcoming appointments. ',''));
		}
		$this->ajax->view(array($this->load->view('mainpane/_____', $results , TRUE),''));
	}


	/*
	 * fn past 
	 * lists all past appointments of the logged in user
	 * @todo: need a view to list appointments: Date Time Descrip Doctor_Name 	
	 * */
	function past(){
		$this->auth->check_logged_in();

		$results = $this->appointments_model->view_past(array(
									'account_id' => $this->auth->get_account_id(),
									'type' => $this->auth->get_type()
								)); 
		if($results == NULL){
			$this->ajax->view(array('You have no past appointments. ',''));
		}
		$this->ajax->view(array($this->load->view('mainpane/table_result', $results , TRUE),''));
	}

	/*
	 * fn cancel 
	 * cancel an existing appointment
	 * @param apt_id, the appointment id number to delete from database
	 * @return redirect to list of upcoming appointments || error(not their appointment)
	 * @todo: pop up-- are you sure you want to cancel appointment?
	 * */
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
	
	/*
	 * fn reschedule 
	 * reschedule an existing appointment (date/time)
	 * @param apt_id, the appointment id number to modifty in database
	 * @input -- new appointment date/time
	 * @return redirect to list of upcoming appointments || error(not their appointment)
	 * @todo: need reschedule appt form / view
 	 * @todo(later): Later: have a request reschedule and accept reschedule via email 
	 * */
	function reschedule($apt_id){
		$this->auth->check_logged_in();
		$this->load->model('appointments_model');
		if($this->appointments_model->is_myappointment(array($this->auth->get_account_id(),$apt_id))){
			$result = $this->appointments_model->get_appointment(array($apt_id));
			$this->ajax->view(array($this->load->view('mainpane/________',$result, TRUE),''));
			$new_time = $this->input->post('appointment_time');
			$results = $this->appointments_model->reschedule(array('appointment_id' => $apt_id, 'date_time' => $new_time )); 
			$this->ajax->redirect('/appointments/upcoming');
		}
		else{
			show_error('This is not your appointment. Permission Denied.', 500);
			return;
		}
	}

	/*
	 * fn request 
	 * request an apptointment with a hcp
	 * @input -- appointment date/time, description
	 * @return -- confirmation statement
	 * @todo: need reschedule appt form / view
	 * @todo:fix this -- view should pass this to me based on the tuple they click..
 	 * @todo(later): Later: have a request reschedule and accept reschedule via email , reminder emails... 
	 * */
	function request(){
		$this->auth->check_logged_in();

		$this->ajax->view(array($this->load->view('mainpane/__________', '' , TRUE), ''));
		$hcp_id = $this->input->post('hcp_id'); // @todo:fix this -- view should pass this to me based on the tuple they click..
		$desc = $this->input->post('description');
		$time = $this->input->post('time');
		$results = $this->appointments_model->request(array( 'patient_id' => $this->auth->get_account_id(), 
									'hcp_id' => $hcp_id , 
									'desc' => $desc ,
									'time' => $time 
								)); 
		/*if($results == NULL){
			show_error('We apologize for the inconvenience. Your appointment could not be requested.', 500);
			return;	
		} */
	}
}
/** @} */
?>

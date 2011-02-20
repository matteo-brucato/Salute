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

	function __construct() {
		parent::Controller();
		$this->load->library('ui');
		$this->load->library('auth');
		$this->load->model('patient_model');
			$this->load->model('hcp_model');
		$this->load->model('appointments_model');
		$this->load->model('connections_model');
	}

	/**
	 * fn index -- default
	 * checks if user is logged in. 
	 * redirects to page to list upcoming appointments
	 * */
	function index(){
		//$this->auth->check_logged_in();
		//$this->ui->redirect('/appointments/upcoming');
		$this->upcoming();
	}

	/**
	 * Lists all appointments of the logged in user
	 * */
	function all() {
		$check = $this->auth->check(array(auth::CurrLOG));
		
		if ($check !== TRUE) return;

		if ($this->auth->get_type() === 'patient'){
			$results = $this->appointments_model->view_all(array('account_id' => $this->auth->get_account_id(),
																 'type' => $this->auth->get_type()));
		} else if ($this->auth->get_type() === 'hcp'){
			$results = $this->appointments_model->view_all(array('account_id' => $this->auth->get_account_id(),
																 'type' => $this->auth->get_type()));
		} else {
			$this->ui->set_error('Internal server logic error.', 'server');
			return;
		}
		
		if ($results === -1){
			$this->ui->set_query_error();
			return;
		}
		$mainview = $this->load->view('mainpane/lists/appointments',
					array('list_name' => 'My Appointments', 'list' => $results) , TRUE);
				
		// Give results to the client
		$this->ui->set(array($mainview));
	}

	/**
	 * fn upcoming 
	 * lists all upcoming appointments of the logged in user
	 * @todo: need a view to list appointments: Date Time Descrip Doctor Name Actions(Reschedule,Cancel)
	 * @MATEO:
	 * 	I ASSUME THE VIEW NAME WILL BE list_appointments	
	 * */
	function upcoming(){
		
		$this->auth->check(array(auth::CurrLOG));

		if ($this->auth->get_type() === 'patient'){
			$results = $this->appointments_model->view_upcoming(array('account_id' => $this->auth->get_account_id(),
																			 'type' => $this->auth->get_type()
																));
		} else if ($this->auth->get_type() === 'hcp'){
			$results = $this->appointments_model->view_upcoming(array('account_id' => $this->auth->get_account_id(),
																 'type' => $this->auth->get_type()
																));
		} else {
			$this->ui->set_error('Internal server logic error.', 'server');
			return;
		} 
			
		if ($results === -1) {
			$this->ui->set_query_error();
			return;
		}
		
		$mainview = $this->load->view('mainpane/lists/appointments',
					array('list_name' => 'My Upcoming Appointments', 'list' => $results) , TRUE);
				
		// Give results to the client
		$this->ui->set(array($mainview));
	}


	/**
	 * fn past 
	 * lists all past appointments of the logged in user
	 * @todo: need a view to list appointments: Date Time Descrip Doctor_Name 
	 * @MATEO:
	 * 	I ASSUME THE VIEW NAME WILL BE list_appointments	
	 * */
	function past(){
		
		$this->auth->check(array(auth::CurrLOG));
		
		if ($this->auth->get_type() === 'patient'){
			$results = $this->appointments_model->view_past(array('account_id' => $this->auth->get_account_id(),
																 'type' => $this->auth->get_type()));
		} else if ($this->auth->get_type() === 'hcp'){
			$results = $this->appointments_model->view_past(array('account_id' => $this->auth->get_account_id(),
																 'type' => $this->auth->get_type()));
		} else {
			$this->ui->set_error('Internal server logic error.', 'server');
			return;
		}
		
		if($results === -1) {
			$this->ui->set_query_error();
			return;
		}
		
		$mainview = $this->load->view('mainpane/lists/appointments',
					array('list_name' => 'My Past Appointments', 'list' => $results) , TRUE);
				
		// Give results to the client
		$this->ui->set(array($mainview));
	}

	/**
	 * fn cancel 
	 * cancel an existing appointment
	 * @param apt_id, the appointment id number to delete from database
	 * @return redirect to list of upcoming appointments || error(not their appointment)
	 * @todo: pop up-- are you sure you want to cancel appointment?
	 * */
	function cancel($apt_id = NULL) {
		
		$check = $this->auth->check(array(
			auth::CurrLOG,
			auth::APPT_MINE, $apt_id));
			
		if ($check !== TRUE)
			return;
			
		$results = $this->appointments_model->cancel(array($apt_id));
								
			switch ($results) {
				case -1:
					$this->ui->set_query_error();
					return;
				case -5:
					$this->ui->set_error('Appointment does not exist!');
					return;
				default:
					$this->ui->set_message('The appointment was successfully canceled.','Confirmation');
					return;
			}
	}
	
	/**
	 * Shows a form to change an appointment date and time
	 * */
	function reschedule($apt_id = NULL) {
		
		$check = $this->auth->check(array(
			auth::CurrLOG,
			auth::CurrPAT,
			auth::APPT_MINE, $apt_id));
			
		if ($check !== TRUE)
			return;
		
		// Get appointment tuple from the model
		$app = $this->appointments_model->get_appointment(array($apt_id));
		if ($app === -1) {
			$this->ui->set_query_error();
			return;
		}
		else if (count($app) <= 0) {
			$this->ui->set_error('This appointment does not exist');
			return;
		}
		
		$this->ui->set(array(
			$this->load->view('mainpane/forms/change_appointment', array('app' => $app[0]), TRUE)
		));
	}
	
	/**
	 * Reschedule an existing appointment (date/time)
	 * 
	 * @param apt_id, the appointment id number to modifty in database
	 * @input -- new appointment date/time
	 * @return redirect to list of upcoming appointments || error(not their appointment)
	 * @todo: need reschedule appt form / view
 	 * @todo(later): Later: have a request reschedule and accept reschedule via email
 	 * @MATEO:
	 * 	I ASSUME THE VIEW NAME WILL BE reschedule	 
	 * */
	function reschedule_do($apt_id = NULL) {
	
		$check = $this->auth->check(array(
			auth::CurrLOG,
			auth::CurrPAT,
			auth::APPT_MINE, $apt_id));
			
		if ($check !== TRUE)
			return;
		
		$new_time = $this->input->post('time');
		
		if ($new_time !== FALSE && $new_time !== ''){
			$results = $this->appointments_model->reschedule(array('appointment_id' => $apt_id, 'date_time' => $new_time )); 
			switch ($results) {
				case -1:
					$this->ui->set_query_error();
					return;
				case -5:
					$error = 'Appointment does not exist.';
					$this->ui->set_error($error);
					return;
				default:
					$this->ui->set_message('Appointment was successfully rescheduled');
					return;
			}
		} else {
			$error = 'Please fill out the Time and Date field';
			$type = 'Missing Arguments';
			$this->ui->set_error($error, $type);
		}

	}

	
	/**
	 * Dislpays a form for a new appointment request
	 * 
	 * @param $aid
	 * 		account id of person you want appointment with
	 * 
	 * @attention Right now only patient can ask appointments
	 * */
	function request($aid = NULL)
	{
		$check = $this->auth->check(array(
			auth::CurrLOG,
			auth::CurrPAT,
			auth::CurrCONN, $aid,
			auth::HCP, $aid));
		if ($check !== TRUE)return;
		
		// Get doctor tuple from the model
		$hcp = $this->hcp_model->get_hcp(array($aid));
		if ($hcp === -1) {
			$this->ui->set_query_error();
			return;
		}
		
		$this->ui->set(array(
			$this->load->view('mainpane/forms/request_appointment', array('hcp' => $hcp[0]), TRUE)
		));
	}
	/** 
	 * Request an apptointment with a hcp
	 * 
	 * @input -- account_id of the hcp to which the request is going to be made
	 * @return -- confirmation statement
	 * @todo: need reschedule appt form / view
	 * @todo:fix this -- view should pass this to me based on the tuple they click..
 	 * @todo(later): Later: have a request reschedule and accept reschedule via email , reminder emails...
 	 * @MATEO:
	 * 	I ASSUME THE VIEW NAME WILL BE request
	 * */
	function request_do($account_id = NULL) {
		$check = $this->auth->check(array(
			auth::CurrLOG,
			auth::CurrPAT,
			auth::CurrCONN, $account_id,
			auth::HCP, $account_id));
		
		if ($check !== TRUE)
			return;
	
		$desc = $this->input->post('description');
		$time = $this->input->post('time');
		
		//test to see if the time and description are TRUE and not NULL
		if( $desc !== FALSE && $desc !== '' && $time !== FALSE && $time !== '') {
			$results = $this->appointments_model->request(array(
				$this->auth->get_account_id(), 
				$account_id, 
				$desc,
				$time
			));
			if($results === -1){
				$this->ui->set_query_error();
				return;
			}
			
			$this->ui->set_message('Your request has been submitted','Confirmation');
			return;
		} else{
			$this->ui->set_error('Please fill out the Time and Date and Description', 'Missing Arguments');
			return;
		}  
	}
	
	/**
	 * Hcp accepts an apptointment with a patient
	 * @param $apt_id appointment id
	 * @return confirmation statement
	 * @todo fix this -- view should pass this to me based on the tuple they click..
	 * */
	 function accept_appointment($apt_id = NULL){
		 
		 $check = $this->auth->check(array(
			auth::CurrLOG,
			auth::CurrHCP,
			auth::APPT_MINE, $apt_id));
		if ($check !== TRUE) return;

		$results = $this->appointments_model->approve( array('appointment_id' => $apt_id));
		if($results === -1) {
			$this->ui->set_query_error();
			return;
		}
		elseif( $results === -5){
			$this->ui->set_error('Appointment ID does not exist');
			return;
		}
		
		$this->ui->set_message('The appointment has been successfully approved.','Confirmation');
	}
}
/** @} */
?>

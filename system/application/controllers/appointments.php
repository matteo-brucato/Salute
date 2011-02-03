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
	 * @MATEO:
	 * 	I ASSUME THE VIEW NAME WILL BE all_appointments	
	 * */
	function all() {
		$this->auth->check_logged_in();
		
		$this->load->model('appointments_model');

		if ($this->auth->get_type() === 'patient'){
			
			$results = $this->appointments_model->view_all(array('account_id' => $this->auth->get_account_id(),
																 'type' => $this->auth->get_type()));
			$sidepane = 'sidepane/patient-profile';
		}
		else if ($this->auth->get_type() === 'doctor'){
			$results = $this->appointments_model->view_all(array('account_id' => $this->auth->get_account_id(),
																 'type' => $this->auth->get_type()));
			$sidepane = 'sidepane/doctor-profile';
		}
		else {
			show_error('Internal server logic error.', 500);
			return;
		}
		
		switch ($results) {
			case -1:
				$mainview = 'Query error!';
				$sideview = '';
				break;
			default:
				$mainview = $this->load->view('mainpane/all_appointments',
					array('list_name' => 'My Appointments', 'list' => $results) , TRUE);
				$sideview = $this->load->view($sidepane, '', TRUE);
				break;
		}
		
		// Give results to the client
		$this->ajax->view(array($mainview,$sideview));
		
		//if($results == NULL){
		//	$this->ajax->view(array('No appointments. ',''));
		//}
		//$this->ajax->view(array($this->load->view('mainpane/____', $results , TRUE),''));
	}

	/*
	 * fn upcoming 
	 * lists all upcoming appointments of the logged in user
	 * @todo: need a view to list appointments: Date Time Descrip Doctor Name Actions(Reschedule,Cancel)
	 * @MATEO:
	 * 	I ASSUME THE VIEW NAME WILL BE upcoming_appointments	
	 * */
	function upcoming(){
		$this->auth->check_logged_in();
		
		$this->load->model('appointments_model');

		if ($this->auth->get_type() === 'patient'){
			
			$results = $this->appointments_model->view_upcoming(array('account_id' => $this->auth->get_account_id(),
																 'type' => $this->auth->get_type()));
			$sidepane = 'sidepane/patient-profile';
		}
		else if ($this->auth->get_type() === 'doctor'){
			$results = $this->appointments_model->view_upcoming(array('account_id' => $this->auth->get_account_id(),
																 'type' => $this->auth->get_type()));
			$sidepane = 'sidepane/doctor-profile';
		}
		else {
			show_error('Internal server logic error.', 500);
			return;
		}
		
		switch ($results) {
			case -1:
				$mainview = 'Query error!';
				$sideview = '';
				break;
			default:
				$mainview = $this->load->view('mainpane/upcoming_appointments',
					array('list_name' => 'My Upcoming Appointments', 'list' => $results) , TRUE);
				$sideview = $this->load->view($sidepane, '', TRUE);
				break;
		}
		
		// Give results to the client
		$this->ajax->view(array($mainview,$sideview));
								
		//if($results == NULL){
		//	$this->ajax->view(array('You have no upcoming appointments. ',''));
		//}
		//$this->ajax->view(array($this->load->view('mainpane/_____', $results , TRUE),''));
	}


	/*
	 * fn past 
	 * lists all past appointments of the logged in user
	 * @todo: need a view to list appointments: Date Time Descrip Doctor_Name 
	 * @MATEO:
	 * 	I ASSUME THE VIEW NAME WILL BE past_appointments	
	 * */
	function past(){
		$this->auth->check_logged_in();
		
		$this->load->model('appointments_model');

		if ($this->auth->get_type() === 'patient'){
			
			$results = $this->appointments_model->view_past(array('account_id' => $this->auth->get_account_id(),
																 'type' => $this->auth->get_type()));
			$sidepane = 'sidepane/patient-profile';
		}
		else if ($this->auth->get_type() === 'doctor'){
			$results = $this->appointments_model->view_past(array('account_id' => $this->auth->get_account_id(),
																 'type' => $this->auth->get_type()));
			$sidepane = 'sidepane/doctor-profile';
		}
		else {
			show_error('Internal server logic error.', 500);
			return;
		}
		
		switch ($results) {
			case -1:
				$mainview = 'Query error!';
				$sideview = '';
				break;
			default:
				$mainview = $this->load->view('mainpane/past_appointments',
					array('list_name' => 'My Past Appointments', 'list' => $results) , TRUE);
				$sideview = $this->load->view($sidepane, '', TRUE);
				break;
		}
		
		// Give results to the client
		$this->ajax->view(array($mainview,$sideview));
		 
		//if($results == NULL){
		//	$this->ajax->view(array('You have no past appointments. ',''));
		//}
		//$this->ajax->view(array($this->load->view('mainpane/table_result', $results , TRUE),''));
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
			
			if ($this->auth->get_type() === 'patient')
				$sidepane = 'sidepane/patient-profile';
			$sidepane = 'sidepane/doctor-profile';
				
			
			switch ($results) {
			case -1:
				$mainview = 'Query error!';
				$sideview = '';
				break;
			case -5:
				$mainview = 'Appointment does not exist';
				$sideview = '';
			default:
				//$mainview = $this->ajax->redirect('/appointments/upcoming');   //????on the name
				$mainview = $this->ajax->redirect('/appointments/upcoming_appointments');
				$sideview = $this->load->view($sidepane, '', TRUE);
				break;
			}
		}
		else{
			show_error('This is not your appointment. Permission Denied.', 500);
			return;
		}
		
		// Give results to the client
		$this->ajax->view(array($mainview,$sideview));
	}
	
	/*
	 * fn reschedule 
	 * reschedule an existing appointment (date/time)
	 * @param apt_id, the appointment id number to modifty in database
	 * @input -- new appointment date/time
	 * @return redirect to list of upcoming appointments || error(not their appointment)
	 * @todo: need reschedule appt form / view
 	 * @todo(later): Later: have a request reschedule and accept reschedule via email
 	 * @MATEO:
	 * 	I ASSUME THE VIEW NAME WILL BE reschedule	 
	 * */
	function reschedule($apt_id){
		$this->auth->check_logged_in();
		$this->load->model('appointments_model');
		if($this->appointments_model->is_myappointment(array($this->auth->get_account_id(),$apt_id))){
			$result = $this->appointments_model->get_appointment(array($apt_id));
			
			$this->ajax->view(array($this->load->view('mainpane/reschedule',$result, TRUE),''));
			$new_time = $this->input->post('appointment_time');
			$results = $this->appointments_model->reschedule(array('appointment_id' => $apt_id, 'date_time' => $new_time )); 
			
			switch ($results) {
			case -1:
				$mainview = 'Query error!';
				$sideview = '';
				break;
			case -5:
				$mainview = 'Appointment does not exist';
				$sideview = '';
			default:
				//$mainview = $this->ajax->redirect('/appointments/upcoming');   //????on the name
				$mainview = $this->ajax->redirect('/appointments/upcoming_appointments');
				$sideview = $this->load->view('sidepane/patient-profile', '', TRUE);
				break;
			}
		}
		else{
			show_error('This is not your appointment. Permission Denied.', 500);
			return;
		}
		
		// Give results to the client
		$this->ajax->view(array($mainview,$sideview));
	}

	/*
	 * fn request 
	 * request an apptointment with a hcp
	 * @input -- appointment date/time, description
	 * @return -- confirmation statement
	 * @todo: need reschedule appt form / view
	 * @todo:fix this -- view should pass this to me based on the tuple they click..
 	 * @todo(later): Later: have a request reschedule and accept reschedule via email , reminder emails...
 	 * @MATEO:
	 * 	I ASSUME THE VIEW NAME WILL BE request
 	 * 
	 * */
	function request(){
		$this->auth->check_logged_in();

		$this->ajax->view(array($this->load->view('mainpane/request', '' , TRUE), ''));
		$hcp_id = $this->input->post('hcp_id'); // @todo:fix this -- view should pass this to me based on the tuple they click..
		$desc = $this->input->post('description');
		$time = $this->input->post('time');
		$results = $this->appointments_model->request(array( 'patient_id' => $this->auth->get_account_id(), 
									'hcp_id' => $hcp_id , 
									'desc' => $desc ,
									'time' => $time ));
									 
		switch ($results) {
			case -1:
				$mainview = 'Query error!';
				$sideview = '';
			default:
				$mainview = $this->ajax->redirect('/appointments/upcoming_appointments');
				$sideview = $this->load->view('sidepane/patient-profile', '', TRUE);
				break;
			}
			
		// Give results to the client
		$this->ajax->view(array($mainview,$sideview));					
		
		/*if($results == NULL){
			show_error('We apologize for the inconvenience. Your appointment could not be requested.', 500);
			return;	
		} */
	}
}
/** @} */
?>

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

	/**
	 * fn index -- default
	 * checks if user is logged in. 
	 * redirects to page to list upcoming appointments
	 * */
	function index(){
		//$this->auth->check_logged_in();
		//$this->ajax->redirect('/appointments/upcoming');
		$this->upcoming();
	}

	/**
	 * fn all 
	 * lists all appointments of the logged in user
	 * @todo: need a view to list appointments: Date Time Descrip Doctor Name Actions(Reschedule,Cancel)
	 * @MATEO:
	 * 	I ASSUME THE VIEW NAME WILL BE list_appointments	
	 * */
	function all() {
		$this->auth->check_logged_in();
		
		$this->load->model('appointments_model');

		if ($this->auth->get_type() === 'patient'){
			
			$results = $this->appointments_model->view_all(array('account_id' => $this->auth->get_account_id(),
																 'type' => $this->auth->get_type()));
			$sidepane = 'sidepane/patient-profile';
		}
		else if ($this->auth->get_type() === 'hcp'){
			$results = $this->appointments_model->view_all(array('account_id' => $this->auth->get_account_id(),
																 'type' => $this->auth->get_type()));
			$sidepane = 'sidepane/hcp-profile';
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
				$mainview = $this->load->view('mainpane/list_appointments',
					array('list_name' => 'My Appointments', 'list' => $results) , TRUE);
				$sideview = $this->load->view($sidepane, '', TRUE);
				break;
		}
		
		// Give results to the client
		$this->ajax->view(array($mainview,$sideview));
	}

	/**
	 * fn upcoming 
	 * lists all upcoming appointments of the logged in user
	 * @todo: need a view to list appointments: Date Time Descrip Doctor Name Actions(Reschedule,Cancel)
	 * @MATEO:
	 * 	I ASSUME THE VIEW NAME WILL BE list_appointments	
	 * */
	function upcoming(){
		$this->auth->check_logged_in();
		
		$this->load->model('appointments_model');

		if ($this->auth->get_type() === 'patient'){
			
			$results = $this->appointments_model->view_upcoming(array('account_id' => $this->auth->get_account_id(),
																 'type' => $this->auth->get_type()));
			$sidepane = 'sidepane/patient-profile';
		}
		else if ($this->auth->get_type() === 'hcp'){
			$results = $this->appointments_model->view_upcoming(array('account_id' => $this->auth->get_account_id(),
																 'type' => $this->auth->get_type()));
			$sidepane = 'sidepane/hcp-profile';
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
				$mainview = $this->load->view('mainpane/list_appointments',
					array('list_name' => 'My Upcoming Appointments', 'list' => $results) , TRUE);
				$sideview = $this->load->view($sidepane, '', TRUE);
				break;
		}
		
		// Give results to the client
		$this->ajax->view(array($mainview,$sideview));
	}


	/**
	 * fn past 
	 * lists all past appointments of the logged in user
	 * @todo: need a view to list appointments: Date Time Descrip Doctor_Name 
	 * @MATEO:
	 * 	I ASSUME THE VIEW NAME WILL BE list_appointments	
	 * */
	function past(){
		$this->auth->check_logged_in();
		
		$this->load->model('appointments_model');

		if ($this->auth->get_type() === 'patient'){
			
			$results = $this->appointments_model->view_past(array('account_id' => $this->auth->get_account_id(),
																 'type' => $this->auth->get_type()));
			$sidepane = 'sidepane/patient-profile';
		}
		else if ($this->auth->get_type() === 'hcp'){
			$results = $this->appointments_model->view_past(array('account_id' => $this->auth->get_account_id(),
																 'type' => $this->auth->get_type()));
			$sidepane = 'sidepane/hcp-profile';
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
				$mainview = $this->load->view('mainpane/list_appointments',
					array('list_name' => 'My Past Appointments', 'list' => $results) , TRUE);
				$sideview = $this->load->view($sidepane, '', TRUE);
				break;
		}
		
		// Give results to the client
		$this->ajax->view(array($mainview,$sideview));
	}

	/**
	 * fn cancel 
	 * cancel an existing appointment
	 * @param apt_id, the appointment id number to delete from database
	 * @return redirect to list of upcoming appointments || error(not their appointment)
	 * @todo: pop up-- are you sure you want to cancel appointment?
	 * */
	function cancel($apt_id) {
		
		$this->auth->check_logged_in();
		
		$this->load->model('appointments_model');
		
		$result = $this->appointments_model->is_myappointment(array($this->auth->get_account_id(),$apt_id));
		if ( $result === -1){
				$mainview = 'Query error';
				$sideview = '';
		}
		elseif ( $relust === -5){
				$mainview = 'Appointment ID does not exist!';
				$sideview = ''
		}
		elseif ( $result ==== TRUE){
				/* @to do: pop up-- are you sure you want to cancel appointment?*/
				$results = $this->appointments_model->cancel(array($apt_id));
			
				if ($this->auth->get_type() === 'patient'){
					$sidepane = 'sidepane/patient-profile';
				}
				else {
				$sidepane = 'sidepane/hcp-profile';
				}
				
				switch ($results) {
				case -1:
					$mainview = 'Query error!';
					$sideview = '';
					break;
				case -5:
					$mainview = 'Appointment does not exist!';
					$sideview = '';
				default:
					$mainview = 'The appointment was successfully canceled.';
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
	
	/**
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
		
		if ($this->auth->get_type() === 'hcp'){
			show_error('Doctors are not allowed to reschedule appointments', 500);
			return;
		}
	
		if($this->appointments_model->is_myappointment(array($this->auth->get_account_id(),$apt_id))){
			$result = $this->appointments_model->get_appointment(array($apt_id));
			
			$this->ajax->view(array($this->load->view('mainpane/reschedule',$result, TRUE),''));
			$new_time = $this->input->post('appointment_time');
			$results = $this->appointments_model->reschedule(array('appointment_id' => $apt_id, 'date_time' => $new_time )); 
			
			switch ($results) {
			case -1:
				$mainview = 'Query Error!';
				$sideview = '';
				break;
			case -5:
				$mainview = 'Appointment does not exist';
				$sideview = '';
				break;
			default:
				$mainview = $this->ajax->redirect('/appointments/upcoming');
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

	//input account id of person you want appointment with
	function request($account_id)
	{
		$this->auth->check_logged_in();
		$this->load->model('appointments_model');
		
	}
	/**
	 * fn request 
	 * request an apptointment with a hcp
	 * @input -- appointment date/time, description
	 * @return -- confirmation statement
	 * @todo: need reschedule appt form / view
	 * @todo:fix this -- view should pass this to me based on the tuple they click..
 	 * @todo(later): Later: have a request reschedule and accept reschedule via email , reminder emails...
 	 * @MATEO:
	 * 	I ASSUME THE VIEW NAME WILL BE request
	 * */
	function request_do($account_id){
		$this->auth->check_logged_in();
		$this->load->model('appointments_model');
		$this->load->model('patient_model');
		$this->load->model('connections_model');
		
		if ($this->auth->get_type() === 'hcp')
		{
			show_error('Doctors are not allowed to request appointments', 500);
			return;
		}
		
		//test to see if the accound_id belongs to a hcp
		$is_hcp = $this->hcp_model->is_hcp(array($account_id));
		if ( $is_hcp === -1 )
		{
			$mainview = 'Querry Error';
			$sideview = '';
		}
		elseif ($is_hcp === TRUE)
		{
			
			//test to see if the person loged in is connected with the hcp
			$is_connected = $this->connections_model->is_connected_with(array($account_id, $this->auth->get_acccount_id()));
			
			if($is_connected === -1)
			{
				$mainview = 'Querry Error';
				$sideview = '';
			}
			elseif ($is_connected === TRUE)
			{
				
				$this->ajax->view(array($this->load->view('mainpane/request', '' , TRUE), ''));
				$hcp_id = $this->input->post('hcp_id'); // @todo:fix this -- view should pass this to me based on the tuple they click..
				$desc = $this->input->post('description');
				$time = $this->input->post('time');
				
				//test to see if the time and description are TRUE and not NULL
				if( $desc !== FALSE && $desc !== '' && $time !== FALSE && $time !== '')
				{
					$results = $this->appointments_model->request(array( 'patient_id' => $this->auth->get_account_id(), 
											'hcp_id' => $account_id, 
											'desc' => $desc ,
											'time' => $time ));					 
					switch ($results)
					{
						case -1:
						$mainview = 'Query error!';
						default:
						$mainview = 'Your request has been submitted.';
						break;
					}
				}
				else
				{
					show_error('Please fill out the Time and Date and Description', 500);
					return;
				}
			}
			else
			{
				show_error('This account is not connected with the healthcare provider specified to request an appointment', 500);
				return;
			}
		}
		else
		{
			show_error('The healthcare provider ID does not exist.', 500);
			return;
		}
			
		// Give results to the client
		$this->ajax->view(array($mainview,''));					
	}
	
	/**
	 * fn accept appointment 
	 * hcp accepts an apptointment with a patient
	 * @input -- appointment id
	 * @return -- confirmation statement
	 * @todo:fix this -- view should pass this to me based on the tuple they click..
 	 * @MATEO:
	 * 	I ASSUME THE VIEW NAME WILL BE request
	 * */
	 function accept_appointment($apt_id){
		 $this->auth->check_logged_in();
		 $this->load->model('appointments_model');
		 
		 if ($this->auth->get_type() === 'patient'){
			show_error('Patients are not allowed to accept appointments!', 500);
			return;
		}
		
		//$results = $this->appointments_model->approve( array('appointment_id' => $apt_id));
		$results = $this->appointments_model->approve( array('appointment_id' => $apt_id));
		switch ($results) {
			case -1:
				$mainview = 'Query error!';
				$sideview = '';
			default:
				$mainview = 'The appointment has been successfully approved.';
				$sideview = $this->load->view('sidepane/patient-profile', '', TRUE);
				break;
			}
			
		// Give results to the client
		$this->ajax->view(array($mainview,$sideview));	
		 
	 }
}
/** @} */
?>

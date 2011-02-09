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
		$this->ajax->view(array($mainview, ''));
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
		$this->ajax->view(array($mainview, ''));
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
		$this->ajax->view(array($mainview, ''));
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
		elseif ( $result === -5){
				$mainview = 'Appointment ID does not exist!';
				$sideview = '';
		}
		elseif ( $result === TRUE){
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
		$this->ajax->view(array($mainview, ''));
	}
	
	/**
	 * Shows a form to change an appointment date and time
	 * */
	function reschedule($apt_id) {
		$this->auth->check_logged_in();
		
		// Only patient can request
		if ($this->auth->get_type() != 'patient') {
			show_error('Doctors are not allowed to request appointments');
			return;
		}
		
		// Get appointment tuple from the model
		$app = $this->appointments_model->get_appointment(array($apt_id));
		if ($app === -1) {
			$this->ajax->view(array('Query error',''));
			return;
		}
		else if (count($app) <= 0) {
			$this->ajax->view(array('This appointment does not exist',''));
			return;
		}
		
		$this->ajax->view(array(
			$this->load->view('mainpane/forms/change_appointment',
				array('app' => $app[0]), TRUE),
				''
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
	function reschedule_do($apt_id) {                          // test it belongs to me
		$this->auth->check_logged_in();
		
		if ($this->auth->get_type() === 'hcp')
		{
			show_error('Doctors are not allowed to reschedule appointments', 500);
			return;
		}
		
		//test to see if id is numeric
		if (is_numeric($apt_id))
		{

			//get the appointment if it exits
			$is_mine = $this->appointments_model->get_appointment($apt_id);
			
			if($is_mine === -1)
			{
				$mainview = 'Query Error!';
				$sideview = '';
			} 
			elseif ($is_mine === -5)
			{
				show_error('Appointment ID does not exist.', 500);
				return;
			}
			elseif (sizeof($is_mine) <= 0)
			{
				show_error('Appointment tupple does not exist in the database.', 500);
				return;
			}
			else
			{
				//test to see if the appointment is mine
				if ($this->auth->get_account_id() === $is_mine[0]['patient_id'])
				{
					$new_time = $this->input->post('time');
					
					if ($new_time !== FALSE && $new_time !== '')
					{
						$results = $this->appointments_model->reschedule(array('appointment_id' => $apt_id, 'date_time' => $new_time )); 
			
						switch ($results) 
						{
							case -1:
								$mainview = 'Query Error!';
								$sideview = '';
								break;
							case -5:
								$mainview = 'Appointment does not exist';
								$sideview = '';
								break;
							default:
								$mainview = 'Appointment was successfully rescheduled';
								$sideview = $this->load->view('sidepane/patient-profile', '', TRUE);
								break;
						}
					}
					else
					{
						show_error('Please fill out the Time and Date field', 500);
						return;
					}
				}
				else
				{
					show_error('Cannot reschedule an appointment that doesnt belong to me.', 500);
					return;
				}

			}
		}
		else
		{
			show_error('Appointment ID is not numeric.', 500);
			return;
		}
		// Give results to the client
		$this->ajax->view(array($mainview, ''));
	}

	
	/**
	 * Dislpays a form for a new appointment request
	 * 
	 * @param $aid
	 * 		account id of person you want appointment with
	 * 
	 * @attention Right now only patient can ask appointments
	 * */
	function request($aid)
	{
		$this->auth->check_logged_in();
		$this->load->model('hcp_model');
		
		// Only patient can request
		if ($this->auth->get_type() != 'patient') {
			show_error('Doctors are not allowed to request appointments');
			return;
		}
		
		// Get doctor tuple from the model
		$hcp = $this->hcp_model->get_hcp(array($aid));
		if ($hcp === -1) {
			$this->ajax->view(array('Query error',''));
			return;
		}
		else if (count($hcp) <= 0) {
			$this->ajax->view(array('Sorry, you can request appointments only to HCPs',''));
			return;
		}
		
		$this->ajax->view(array(
			$this->load->view('mainpane/forms/request_appointment',
				array('hcp' => $hcp[0]), TRUE),
				''
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
	function request_do($account_id){
		$this->auth->check_logged_in();
		$this->load->model('patient_model');
		$this->load->model('hcp_model');
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
			$is_connected = $this->connections_model->is_connected_with($account_id, $this->auth->get_account_id());
			
			if($is_connected === -1)
			{
				$mainview = 'Querry Error';
				$sideview = '';
			}
			elseif ($is_connected === TRUE)
			{
				
				$desc = $this->input->post('description');
				$time = $this->input->post('time');
				
				//test to see if the time and description are TRUE and not NULL
				if( $desc !== FALSE && $desc !== '' && $time !== FALSE && $time !== '')
				{
					$results = $this->appointments_model->request(array($this->auth->get_account_id(), 
											$account_id, 
											$desc ,
											$time ));					 
					switch ($results)
					{
						case -1:
							$mainview = 'Query error!';
							break;
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
		$this->ajax->view(array($mainview, ''));
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
		$this->ajax->view(array($mainview, ''));
		 
	 }
}
/** @} */
?>

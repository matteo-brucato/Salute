<?php
/**
 * @file connections.php
 * @brief Controller to handle connections
 *
 * @defgroup ctr Controllers
 * @ingroup ctr
 * @{
 */

class Connections extends Controller {

	function __construct(){
		parent::Controller();
		$this->load->library('ajax');
		$this->load->library('auth');
	}

	/**
	 * fn index -- default.
	 * 
	 * @return error
	 *
	 * */
	function index() {
		show_error('Direct access to this resource is forbidden', 500);
		return;
	}

	/**
	 * fn list_doctors: list all doctors that user is connected with.
	 * 
	 * only available for patients
	 * 
	 * @return: list view of doctors
	 * */
	function list_doctors()	{
		$this->auth->check_logged_in();		

		$this->load->model('connections_model');
		
		if ($this->auth->get_type() === 'patient') {
			$results = $this->connections_model->list_my_doctors($this->auth->get_account_id()); 
			$sidepane='sidepane/patient-profile'
		} 

		else if ($this->auth->get_type() === 'doctor'){
			$results = $this->connections_model->list_my_colleagues($this->auth->get_account_id()); 
			$sidepane='sidepane/doctor-profile'
		}
		else{
			show_error('Internal server logic error.', 500);
			return;
		}
		switch($results){
			case -1:
				$view = 'Query error!';
				break;
			default:
				$view = $this->ajax->view(array(
						$this->load->view('mainpane/mydoctors', array('hcp_list' => $results) , TRUE),
						$this->load->view($sidepane, '', TRUE)
						));			
				break;
		}
		
		$this->ajax->view(array(
			$view,
			''
		));
	}

	/**
	 * fn list_patients: list all patients that hcp is connected with.
	 * 
	 * only available for doctors
	 * 
	 * @return: list view of patients
	 * */
	function list_patients()
	{
		$this->auth->check_logged_in();
		
		if ($this->auth->get_type() !== 'doctor'){
			show_error($this->load->view('errors/not_hcp', '', TRUE));
			return;
		}

		$this->load->model('connections_model');
		$res = $this->connections_model->list_my_patients($this->auth->get_account_id()); 

		// Switch the response from the model, to select the correct view
		switch ($res) {
			case -1:
				$view = 'Query error!';
				break;
			default:
				$view = $this->ajax->view(array(
							$this->load->view('mainpane/mypatients', array('pat_list' => $res) , TRUE),
							$this->load->view('sidepane/doctor-profile', '', TRUE)
						));
				break;
		}
		
		// Create final view for the user
		$this->ajax->view(array(
			$view,
			''
		));

	}

	/* 
	 * Lists the pending connections that this user has initiated(outgoing)
	 * */
	function list_pending_out() 
	{
		$this->auth->check_logged_in();
		$this->load->model('connections_model');
		
		if($this->auth->get_type() === 'doctor'){
			$res = $this->connections_model->pending_todoctors_fromdoctor(array($this->auth->get_account_id())); 
			$sidepane = 'sidepane/doctor-profile';
		}
		else if($this->auth->get_type() === 'patient'){
			$res = $this->connections_model->pending_todoctors_frompatient(array($this->auth->get_account_id())); 
			$sidepane = 'sidepane/patient-profile';
		}
		else {
			show_error('Internal server logic error.', 500);
			return;
		}

		// Switch the response from the model, to select the correct view
		switch ($res) {
			case -1:
				$view = 'Query error!';
				break;
			default:
				$view = $this->ajax->view(array(
							$this->load->view('mainpane/mypatients', array('pending_list' => $res) , TRUE),
							$this->load->view($sidepane, '', TRUE)
						));
				break;
		}
		
		// Create final view for the user
		$this->ajax->view(array(
			$view,
			''
		));
	}
	
	/* 
	 * Lists the pending connections that this user has received(incoming)
	 * Only doctors have this
	 * */
	function list_pending_in() 
	{
		$this->auth->check_logged_in();
		$this->load->model('connections_model');
		
		if($this->auth->get_type() === 'doctor'){
			$res = $this->connections_model->pending_todoctor_fromdoctors(array($this->auth->get_account_id())); 
			$res2 = $this->connections_model->pending_todoctor_frompatients(array($this->auth->get_account_id())); 
			$sidepane = 'sidepane/doctor-profile';
		}
		else if($this->auth->get_type() === 'patient'){
			show_error('Patients cannot be requested. Internal server logic error.', 500);
			return;
		}
		else {
			show_error('Internal server logic error.', 500);
			return;
		}

		// Switch the response from the model, to select the correct view
		switch ($res) {
			case -1:
				$view = 'Query error!';
				break;
			default:
				$view = $this->ajax->view(array(
							$this->load->view('mainpane/mypatients', array('pending_hcp' => $res,'pending_pat' => $res2) , TRUE),
							$this->load->view($sidepane, '', TRUE)
						));
				break;
		}
		
		// Create final view for the user
		$this->ajax->view(array(
			$view,
			''
		));
	}	
	/**
	 * Request a new connection to a doctor.
	 * 
	 * @param
	 *   $id is the id of a doctor you want to connect to
	 * 
	 * @attention
	 *   Can be called by both patients and doctors, but a 
	 * doctor can only request for another doctor and a patient can 
	 * only request for a doctor.
	 * */
	function request($id = NULL)
	{
		$this->auth->check_logged_in();
		$this->load->model('hcp_model');
		$this->load->model('connections_model');
		
		// Check if an account_id has been specified
		if ($id == NULL) {
			show_error('No doctor_id specified.');
			return;
		}
		
		// Check if the account_id specified refers to a doctor
		if (!$this->hcp_model->is_doctor(array($id))) {
			show_error('The id specified does not refer to an HCP.');
			return;
		}
		
		// Get all the doctor's info
		$results = $this->hcp_model->get_doctor(array($id));
		
		// If current user is a doctor
		if ($this->auth->get_type() === 'doctor') {
			$res = $this->connections_model->add_doctor_doctor(array(
										$this->auth->get_account_id(),
										$id
										));
		}
		// If current user is a patient
		else if ($this->auth->get_type() === 'patient') {
			$res = $this->connections_model->add_patient_doctor(array(
										$this->auth->get_account_id(),
										$id
										));
		}
		else {
			show_error('Internal server logic error.', 500);
			return;
		}
		
		// Switch the response from the model, to select the correct view
		switch ($res) {
			case -1:
				$view = 'Query error!';
				break;
			case -3:
				$view = 'This connection has been already requested.';
				break;
			default:
				$view = 'Your request has been submitted.';
				$this->load->library('email');
				$config['mailtype'] = 'html';
				$this->email->initialize($config);
				//$this->email->from($this->auth->get_email());
				$this->email->from('salute-noreply@salute.com');
				//$this->email->to($results['email']);
				$this->email->to('mattfeel@gmail.com');
				$this->email->subject('New Connection Request');
				
				$this->email->message(
					'You have a new connection request from '.
					$this->auth->get_first_name().' '.$this->auth->get_last_name().
					'. Click <a href="https://'.$_SERVER['SERVER_NAME'].'/connections/accept/'.$this->auth->get_account_id().'/'.$id.'">here</a> to accept.');
				
				$this->email->send();
				break;
		}
		
		$this->ajax->view(array(
			$view,
			''
		));
	}

	/** 
	 * Accept an existing connection request
	 * 
	 * @attention Only doctors can do this
	 * */
	function accept($requester_id = NULL, $my_id = NULL) 
	{
		$this->auth->check_logged_in();
		$this->load->model('connections_model');
		$this->load->model('hcp_model');
		$this->load->model('patient_model');
		
		// Check if parameters are specified
		if ($requester_id == NULL || $my_id == NULL) {
			show_error('ids not specified.', 500);
			return;
		}
		
		// Check if the current user is the receiver
		if ($this->auth->get_account_id() != $my_id) {
			show_error('You are not the receiver for this request');
			return;
		}
		
		// Check if you are a doctor (only doctor can call this function)
		if ($this->auth->get_type() != 'doctor') {
			show_error('Sorry, only HCP can accept connection requests');
			return;
		}
		
		if ($this->patient_model->is_patient(array($requester_id))) {
			$res = $this->connections_model->accept_patient_doctor(array($requester_id,$my_id));
		}
		else if ($this->hcp_model->is_doctor(array($requester_id))) {
			$res = $this->connections_model->accept_doctor_doctor(array($requester_id, $my_id));
		}
		else {
			show_error('The requester id does not match any id in the database', 500);
			return;
		}
		
		// Switch the response from the model, to select the correct view
		switch ($res) {
			case -1:
				$view = 'Query error!';
				break;
			case -2:
				$view = 'Connection does not exists.';
				break;
			case -3:
				$view = 'This connection has already been accepted.';
				break;
			default:
				$view = 'You have accepted the connection.';
				break;
		}
		
		// Create final view for the user
		$this->ajax->view(array(
			$view,
			''
		));
	}

	/*
	 * deletes connection (un-friend someone)
	 * @param
	 * 		to_delete_id t
	 * 		my_id
	 * @return 
	 * 		error 
	 * 			query fails
	 * 			connection doesnt exist
	 */
	function destroy($id = NULL)
	{
		$this->auth->check_logged_in();
		
		if ( $id == NULL ){
			show_error('id not specified.', 500);
			return;
		}
		
		$this->load->model('connections_model');
		$this->load->model('hcp_model');
		$this->load->model('patient_model');

		if ( $this->patient_model->is_patient(array($id)) ){
			$res = $this->connections_model->remove_pd_connection(array('patient_id' => $this->auth->get_account_id() , 'hcp_id' => $id )); 
		}
		else if ($this->hcp_model->is_doctor(array($id))) {
			$res = $this->connections_model->remove_dd_connection(array('this_hcp_id' => $this->auth->get_account_id() , 'hcp_id' => $id )); 
		}
		else {
			show_error('Internal Logic Error.', 500);
			return;
		}
		
		// Switch the response from the model, to select the correct view
		switch ($res) {
			case -1:
				$view = 'Query error!';
				break;
			case -2:
				$view = 'Connection does not exists.';
				break;
			default:
				$view = 'You have been disconnected from that health care provider.';
				break;
		}
		
		// Create final view for the user
		$this->ajax->view(array(
			$view,
			''
		));
	}
}

/** @} */
?>

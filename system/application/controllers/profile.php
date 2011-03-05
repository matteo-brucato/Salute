<?php
/**
 * @file profile.php
 * @brief Controller to view and edit profile info
 *
 * @defgroup ctr Controllers
 * @ingroup ctr
 * @{
 */

class Profile extends Controller {

	function __construct(){
		parent::Controller();
		$this->load->library('ui');
		$this->load->library('auth');
		$this->load->model('hcp_model');
		$this->load->model('patient_model');
		$this->load->model('appointments_model');
		$this->load->model('medical_records_model');
		$this->load->model('bills_model');
		$this->load->model('connections_model');
		//$this->load->view('other_patient_profile');
		//$this->load->view('');
		//$this->load->view('');

	}

	/**
	 * Default profile function
	 * @attention user must be logged in
	 * loads the main welcome screen for when a patient or hcp is logged in. 
	 * if patient, load respective views
	 * else if hcp, load respective views
 	 * else error
	 * */
	function index() {
		//$this->auth->check_logged_in();
		if ($this->auth->check(array(auth::CurrLOG)) !== TRUE) {
			return;
		}
		
		if ($this->auth->get_type() === 'patient') {
			/*			$this->ui->set(array(
				$this->load->view('mainpane/personal_patient_profile', '', TRUE)
			));*/
			
			$patient_info = $this->patient_model->get_patient(array($this->auth->get_account_id()));
			if( $patient_info === -1 ){
				$this->auth->set_query_error();
				return;
			}
			if( count($patient_info) <= 0 ){
				$this->auth->set_error('Internal Logic Server Error.','server');
				return;
			}
			//$mainview = 'mainpane/forms/edit_patient_info';
			//$this->ui->set(array($this->load->view($mainview, array('curr_info' => $res[0]), TRUE)));
			$mainview = '';
			$mainview .= '<img src="/resources/images/default_patient.jpg"/>';
			//$mainview.= 
			//$mainview .= $this->load->view('mainpaine/forms/edit_patient_info', 
										//array('curr_info' => $res[0]), TRUE);
										
							
			$mainview .= $this->load->view('mainpane/personal_patient_profile',
										array('info' => $patient_info[0]), TRUE);
			$mainview .= '<a href="/profile/edit">Edit</a>';

			$appointments = $this->appointments_model->view_recent_five(array('account_id' => $this->auth->get_account_id(),
																 'type' => $this->auth->get_type()));
			if( $appointments === -1 ){
				$this->auth->set_query_error();
				return;
			}
			$mainview .= $this->load->view('mainpane/lists/appointments',
										array('list' => $appointments, 'list_name' => 'My Appointments'), TRUE);
			$mainview .= '<a href="/appointments/all">View all appointments</a>';
			$medical_recs= $this->medical_records_model->list_recent_five(array($this->auth->get_account_id()));
			if( $medical_recs === -1 ){
				$this->auth->set_query_error();
				return;
			}
			$mainview .= $this->load->view('mainpane/lists/medical_records',
										array('list' => $medical_recs, 'list_name' => 'My Medical Records'), TRUE);
			$mainview .= '<a href="/medical_records/myrecs">View all medical records</a>';
			
			$bills= $this->bills_model->view_top_five(array( $this->auth->get_account_id(), $this->auth->get_type()));
			if( $bills === -1 ){
				$this->auth->set_query_error();
				return;
			}
			$mainview .= $this->load->view('mainpane/lists/bills',
										array('list' => $bills, 'list_name' => 'My Bills'), TRUE);
			$mainview .= '<a href="/bills/all">View all bills</a>';
			
			$patients= $this->connections_model->list_patients_top_five($this->auth->get_account_id());
			if( $patients === -1 ){
				$this->auth->set_query_error();
				return;
			}		
			$mainview .= $this->load->view('mainpane/lists/patients',
										array('list' => $patients, 'list_name' => 'My Patients', 'status' => 'connected'),  TRUE);			
			$mainview .= '<a href="/connections/mypatients">View all patients</a>';
			$this->ui->set(array($mainview));
			
			$hcps= $this->connections_model->list_hcps_top_five($this->auth->get_account_id());
			if( $hcps === -1 ){
				$this->auth->set_query_error();
				return;
			}		
			$mainview .= $this->load->view('mainpane/lists/patients',
										array('list' => $hcps, 'list_name' => 'My HCPs', 'status' => 'connected'),  TRUE);			
			$mainview .= '<a href="/connections/mypatients">View all patients</a>';
			$this->ui->set(array($mainview));
			
		/*
			//load patient account information
			$patient_info = $this->patient_model->get_patient( array($this->auth->get_account_id()) );
			if( $patient_info === -1 ){
				$this->auth->set_query_error();
				return;
			}
			if( count($patient_info) <= 0 ){
				$this->auth->set_error('Internal Logic Server Error.','server');
				return;
			}
			//$picsrc = $patient_info[0]['picture_name']
			$mainview = '<img src="/resources/images/default_patient.jpg"/>';
		//	$mainview = $this->load->view('mainpaine/forms/edit_patient_info', array('curr_info' => $res[0]), TRUE);
			

			//$this->ui->set(array($mainview));
			$this->ui->set(array($mainview));
			return;*/
		}

		else if ($this->auth->get_type() === 'hcp') {
			$this->ui->set(array(
				$this->load->view('mainpane/personal_hcp_profile', '', TRUE),
				$this->load->view('sidepane/personal_hcp_profile', '', TRUE)
			));
		}

		else {
			$this->ui->set_error('Access to this page not allowed', 'forbidden');
			return;
		}

		// Fancy Features: pass notifications from model to view via the 2nd parameter in the load->view call. 
	}

	/**
	 * Loads logged-in user's information
	 * 
	 * @attention user must be logged in
	 * loads the user's information in the main panel
	 * loads the user's menu bar in the side panel  
	 * if patient, load respective views
	 * else if hcp, load respective views
 	 * else error
 	 * 
 	 * @todo There's no link to this function in the GUI...
	 * 
	function myinfo()
	{
		//$this->auth->check_logged_in();
		if ($this->auth->check(array(auth::CurrLOG)) !== TRUE) {
			return;
		}

		if ($this->auth->get_type() === 'patient') {
			$this->ui->set(array(
				$this->load->view('mainpane/personal_patient_info', '', TRUE),
				$this->load->view('sidepane/personal_patient_profile', '', TRUE)
			));
		}
		else if ($this->auth->get_type() === 'hcp') {		
			$this->ui->set(array(
				$this->load->view('mainpane/personal_hcp_info', '', TRUE),
				$this->load->view('sidepane/personal_hcp_profile', '', TRUE)
			));
		}	
		else{
			$this->ui->set_error('Unknown Error.', 'server');
			return;
		}
	}
	* */
	
	/**
	 * Prints another user's profile under the condition that they are connected
	 * @param 	id is used to check type(hcp or patient) of the user who's profile is to be viewed
	 * 			checks if they are connected
	 * @return 	loads the friend's profile in the main panel || error page
	 * @tests 	all successful and complete.
	 * 				invalid id input
	 * 				non-existent id #
	 *	 			pat trying to view patient: 'Sorry! Patients cannot be connected with other patients'
	 * 				pat trying to view unconnected hcp: 'You are not connected. Permission Denied.' 
	 * 				pat trying to view connected hcp: shows hcp profile + actions
	 * 				pat trying to view pending hcp: 'You are not connected. Permission Denied. '		
	 * 
	 * 	 			hcp trying to view pending patient : Sorry! An HCP can only view profiles of connected patients
	 *  			hcp trying to view unconnected patient: 'Sorry! An HCP can only view profiles of connected patients'
	 * 				hcp trying to view pending hcp: denies permission
	 * 				hcp trying to view connected hcp: Profile + actions
	 *  			hcp trying to view unconnected hcp: 'You are not connected. Permission Denied.'
	 * 				hcp trying to view connected patient: Sorry! An HCP can only view profiles of connected patients
	 * 
	 * @todo Add more checks for values from the model (error checking)
	 * */
	function user($id = NULL) {
		//$this->auth->check_logged_in();
		if ($this->auth->check(array(auth::CurrLOG,auth::ACCOUNT,$id,auth::CurrCONN,$id)) !== TRUE) {
			return;
		}
		/**if ($id == NULL) {
			$this->ui->set_redirect('/profile');
			//$this->ui->show_app_error();
			return;
		}

		check that id is an intenger		
		if (!is_numeric($id)) {
			$this->ui->set_error('Invalid id type.');
			return;
		}
		**/
		
		//$this->load->model('hcp_model');
		//$this->load->model('patient_model');
		//$this->load->model('connections_model');

		$info = $this->patient_model->get_patient(array($id));
		if ($info === -1 || count($info) <= 0){
			$info = $this->hcp_model->get_hcp(array($id));
			if($info === -1){
				$this->auth->set_query_error();
				return;
			}
			$id_type = 'hcp';
		}
		else if (count($info) <= 0){
			$this->auth->set_error('Internal Logic Server Error.','server');
			return;
		}
		else{
			$id_type = 'patient';	
		}
			
		/*		
		// Checks the user_id, if passes, get their info 
		if ($this->hcp_model->is_hcp(array($id))) {
			$info = $this->hcp_model->get_hcp(array($id));
			$id_type = 'hcp';
		} else if ($this->patient_model->is_patient(array($id))) {
			$info = $this->patient_model->get_patient(array($id));
			$id_type = 'patient';
		} else {
			$this->ui->set_error('The specified <i>id</i> does not correspond
			neither to an HCP nor a patient');
			return;
		}
		if( $info === -1 ){
			$this->ui->set_query_error();
			return;
		}
	
		// check that logged in user is a hcp. 
		if ($this->auth->get_type() == 'hcp' && $id_type != 'patient') {
			$this->ui->error('Sorry! An HCP can only view profiles of connected patients');
			return;
		}
		
		* @attention milestone1-- we can have p-p connections
		if ($this->auth->get_type() == 'patient' && $id_type == 'patient') {
			$this->ui->set_error('Sorry! Patients cannot be connected with other patients','Permission Denied');
			return;
		} 
	
		// check that the id is friends with logged in user
		$is_my_friend = $this->connections_model->is_connected_with($this->auth->get_account_id(), $id);
		
		if ($is_my_friend === -1){
			$this->ui->set_query_error();
			return;		
		}else if (!$is_my_friend && $id_type === 'patient' ){
			$this->ui->set_error('You are not connected.','Permission Denied');
			return;		
		}

		// Show the side panel based on logged in type.
		if ($this->auth->get_type() == 'hcp') {
			$sideview = $this->load->view('sidepane/personal_hcp_profile', '' , TRUE);
		} else if ($this->auth->get_type() == 'patient') {
			$sideview = $this->load->view('sidepane/personal_patient_profile', '' , TRUE);
		} else {
				$this->ui->set_error('Internal Logic Error.','server');
				return;
		}
		* */

		if ($id_type == 'hcp') {
			$this->ui->set(array(
				$this->load->view('mainpane/other_hcp_profile',
					array('info' => $info[0], 'is_my_friend' => TRUE), TRUE)
			));
		} else if($id_type == 'patient') { // looking for a patient profile
			$this->ui->set(array(
				$this->load->view('mainpane/other_patient_profile',
					array('info' => $info[0], 'is_my_friend' => TRUE), TRUE)
			));
		}
		
		/* Load up the right view
		if ($id_type == 'hcp') {
			$this->ui->set(array(
				$this->load->view('mainpane/other_hcp_profile',
					array('info' => $info[0], 'is_my_friend' => $is_my_friend), TRUE)
			));
		} else if($id_type == 'patient') { // looking for a patient profile
			$this->ui->set(array(
				$this->load->view('mainpane/other_patient_profile',
					array('info' => $info[0], 'is_my_friend' => $is_my_friend), TRUE)
			));
		}*/ else {
			$this->ui->set_error('Internal Logic Error.','server');
			return;			
		}
	}

	/*
	 * Loads a form that shows their current information, with ability to change and submit.
	 * */
	function edit() {
		//$this->auth->check_logged_in();
		if ($this->auth->check(array(auth::CurrLOG)) !== TRUE) {
			return;
		}
		
		// Get my current info
		if ($this->auth->get_type() === 'patient') {
			$res = $this->patient_model->get_patient(array($this->auth->get_account_id()));
			$mainview = 'mainpane/forms/edit_patient_info';
		}
		else if ($this->auth->get_type() === 'hcp') {
			$res = $this->hcp_model->get_hcp(array($this->auth->get_account_id()));
			$mainview = 'mainpane/forms/edit_hcp_info';
		} else {
			$this->ui->set_error('Server error','server');
			return;
		}
		
		if ($res === -1) {
			$this->ui->set_query_error();
			return;
		}
		else if (count($res) <= 0) {
			$this->ui->set_error('Server error','server');
			return;
		}
		
		$this->ui->set(array(
			$this->load->view($mainview, array('curr_info' => $res[0]), TRUE)
		));
	}

	/* *	
	 * Updates database with user's editted personal information
	 * @return confirmation statement + email || error
	 * 
	 * @todo Add input checking
	 * */
	function edit_do() {
		//$this->auth->check_logged_in();
		if ($this->auth->check(array(auth::CurrLOG)) !== TRUE) {
			return;
		}
		
		if ( $this->auth->get_type() === 'patient'){
			$res = $this->patient_model->update_personal_info(array(
																$this->input->post('firstname'),
																$this->input->post('middlename'),
																$this->input->post('lastname'),
																$this->input->post('dob'),
																$this->input->post('sex'),
																$this->input->post('tel'),
																$this->input->post('fax'),
																$this->input->post('address'),
																$this->auth->get_account_id()
														)); 
		}

		else if ($this->auth->get_type() === 'hcp'){
			$res = $this->hcp_model->update_personal_info(array(
																$this->input->post('firstname'),
																$this->input->post('middlename'),
																$this->input->post('lastname'),
																$this->input->post('dob'),
																$this->input->post('sex'),
																$this->input->post('tel'),
																$this->input->post('fax'),
																$this->input->post('spec'),
																$this->input->post('org'),
																$this->input->post('address'),
																$this->auth->get_account_id()
														)); 
		}
		else{
			$this->ui->set_error('Server Error.', 'server');
			return;
		}
		
		if ($res === -1) {
			$this->ui->set_query_error();
			return;
		}
		
		$msg = 'Your changes have been made.';
		
		// Update session cookie
		$this->session->set_userdata(array(
			'first_name' => $this->input->post('firstname'),
			'last_name' => $this->input->post('lastname')
		));
		
		$this->ui->set_message($msg,'Confirmation');
	}
}
/** @} */
?>

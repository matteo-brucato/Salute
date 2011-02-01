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
		$this->load->library('ajax');	
		$this->load->library('auth');
	}

	/*
	 * Fn index -- default 
	 * checks that user is logged in
	 * loads the main welcome screen for when a patient or doctor is logged in. 
	 * if patient, load respective views
	 * else if doctor, load respective views
 	 * else error
	 * */
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

	/*
	 * fn my_info
	 * checks that user is logged in
	 * loads the user's information in the main panel
	 * loads the user's menu bar in the side panel  
	 * if patient, load respective views
	 * else if doctor, load respective views
 	 * else error
	 * */
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
	/*
	 * fn user
	 * prints a another user's profile under the condition that they are connected
	 * @param id is used to check type(hcp or patient) of the user who's profile is to be viewed
	 * 			checks if they are connected
	 * @return loads the friend's profile in the main panel || error page
	 * @tests
	 * 		Successful:
	 * 			invalid id input
	 * 			hcp trying to view pending patient : denies permission
	 * 			hcp trying to view pending hcp: denies permission
	 * 			pat trying to view pending hcp: 'You are not connected. Permission Denied. '		
	 * 			hcp trying to view unconnected patient: 'Sorry! An HCP can only view profiles of connected patients'
	 * 			pat trying to view unconnected hcp: 'You are not connected. Permission Denied.' 
	 * 			hcp trying to view unconnected hcp: 'You are not connected. Permission Denied.'
	 * 			pat trying to view patient: 'Sorry! Patients cannot be connected with other patients'
	 * Fails:
	 * 			hcp trying to view connected hcp: Query error!'
	 * 			hcp trying to view unconnected hcp: 'You are not connected. Permission Denied.'
	 * 			hcp trying to view connected patient: Sorry! An HCP can only view profiles of connected patients
	 * 			pat trying to view connected hcp: 'Query error!'
	 * 
	 * 
	 * */
	function user($id = NULL) {
		$this->auth->check_logged_in();
		// check that id is an intenger
		if (!is_numeric($id)){
			show_error('Invalid id type.',500);
			return;
		}
		if ($id == NULL) {
			$this->ajax->redirect('/profile');
			//$this->ajax->show_app_error();
			return;
		}
					
		$this->load->model('hcp_model');
		$this->load->model('patient_model');
		$this->load->model('connections_model');
		
		// Checks the user_id, if passes, get their info 
		if ($this->hcp_model->is_doctor(array($id))) {
			$info = $this->hcp_model->get_doctor(array($id));
			$id_type = 'doctor';
		}
		else if ($this->patient_model->is_patient(array($id))) {
			$info = $this->patient_model->get_patient(array($id));
			$id_type = 'patient';
		} else {
			show_error('The specified <i>id</i> does not correspond
			neither to an HCP nor a patient');
			return;
		}
		// error checking for get_doctor / get_patient fn calls
		switch ($info) {
			case -1:
				$view = 'Query error!';
				$error = TRUE;
				break;
			case -7:
				$view = 'This account id is not an hcp';
				break;
			default:
				$error = FALSE;
				break;
		}
		
		if($error){
			$this->ajax->view(array($view,''));
			return;
		}
		
		// check that logged in user is a doctor. 
		if ($this->auth->get_type() == 'doctor' && $id_type == 'patient') {
			show_error('Sorry! An HCP can only view profiles of connected patients');
			return;
		}
		else if ($this->auth->get_type() == 'patient' && $id_type == 'patient') {
			show_error('Sorry! Patients cannot be connected with other patients',500);
			return;
		}
	
		// check that the id is friends with logged in user
		echo $this->auth->get_account_id().' '.$id;
		$is_my_friend = $this->connections_model->is_connected_with($this->auth->get_account_id(), $id);
		echo ' '.$is_my_friend;
		$is_my_friend = true;
		switch ($is_my_friend) {
			case -1:
				$view = 'Query error!';
				$error = TRUE;
				break;
			case FALSE:
				$view = 'You are not connected. Permission Denied.';
				$error = TRUE;
				break;
			default:
				$view = 'Ok.';
				$error = FALSE;
				break;
		}
		
		if ($error) {
			$this->ajax->view(array($view,''));
			return;
		}

		// Show the side panel based on logged in type.
		if ($this->auth->get_type() == 'doctor') {
			$sideview = $this->load->view('sidepane/doctor-profile', '' , TRUE);
		} else if ($this->auth->get_type() == 'patient') {
			$sideview = $this->load->view('sidepane/patient-profile', '' , TRUE);
		} else {
				show_error('Internal Logic Error.',500);
				return;
		}
		
		// Load up the right view
		if ($id_type == 'doctor') {
			$this->ajax->view(array(
				$this->load->view('mainpane/see_doctor',
					array('info' => $info[0], 'is_my_friend' => $is_my_friend), TRUE), 
				$sideview
			));
		} else if($id_type == 'patient') { // looking for a patient profile
			$this->ajax->view(array(
				$this->load->view('mainpane/see_patient',
					array('info' => $info[0], 'is_my_friend' => $is_my_friend), TRUE),
				$sideview
			));
		} else {
				show_error('Internal Logic Error.',500);
				return;			
		}
	}

	/*
	 * fn edit
	 * loads a form that allows the logged in user to edit their personal info
	 * @return database is updated || error page
	 * @todo this is not tested, unsure about logic. 
	 * */
	function edit() {
		$this->auth->check_logged_in();

		$this->ajax->view(array(
					$this->load->view('mainpane/edit_info', '', TRUE),
					$this->load->view('sidepane/default', '', TRUE)
				));

		$email = $this->input->post('email');
		$password = $this->input->post('password');
		$first_name = $this->input->post('first_name');
		$middle_name = $this->input->post('middle_name');
		$last_name = $this->input->post('last_name');
		$dob = $this->input->post('dob');
		$sex = $this->input->post('sex');
		$ssn = $this->input->post('ssn');
		$tel_no = $this->input->post('tel_no');
		$fax_no = $this->input->post('fax_no');
		$address = $this->input->post('address');

		if ( $this->auth->get_type() === 'patient'){
			$this->load->model('patient_model');
			$this->patient_model->update_personal_info(array(
								'account_id' => $account_id, 
								'first_name' => $first_name, 
								'last_name' => $last_name,
								'middle_name' => $middle_name, 
								'ssn' => $ssn, 
								'dob' => $dob, 
								'sex' => $sex, 
								'tel_number' => $tel_no, 
								'fax_number' => $fax_no, 
								'address' => $address
							)); 
		}

		else if ($this->auth->get_type() === 'doctor'){
			$this->load->model('hcp_model');
			$this->hcp_model->update_personal_info(array(
								'account_id' => $account_id, 
								'first_name' => $first_name, 
								'last_name' => $last_name,
								'middle_name' => $middle_name, 
								'ssn' => $ssn, 
								'dob' => $dob, 
								'sex' => $sex, 
								'tel_number' => $tel_no, 
								'fax_number' => $fax_no, 
								'address' => $address
						)); 
		}
		else{
			show_error('Unknown Error.', 500);
			return;
		}


	}

}
/** @} */
?>

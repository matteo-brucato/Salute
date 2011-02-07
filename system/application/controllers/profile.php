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

	/**
	 * Default profile function
	 * @attention user must be logged in
	 * loads the main welcome screen for when a patient or hcp is logged in. 
	 * if patient, load respective views
	 * else if hcp, load respective views
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

		else if ($this->auth->get_type() === 'hcp') {
			$this->ajax->view(array(
				$this->load->view('mainpane/hcp-profile', '', TRUE),
				$this->load->view('sidepane/hcp-profile', '', TRUE)
			));
		}

		else {
			show_error('Access to this page not allowed', 500);
			return;
		}

		// Fancy Features: pass notifications from model to view via the 2nd parameter in the load->view call. 
	}

	/**
	 * Loads logged-in user's information
	 * @attention user must be logged in
	 * loads the user's information in the main panel
	 * loads the user's menu bar in the side panel  
	 * if patient, load respective views
	 * else if hcp, load respective views
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
		else if ($this->auth->get_type() === 'hcp') {		
			$this->ajax->view(array(
				$this->load->view('mainpane/hcp-info', '', TRUE),
				$this->load->view('sidepane/hcp-profile', '', TRUE)
			));		
		}	
		else{
			show_error('Unknown Error.', 500);
			return;
		}
	}
	
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
		$this->auth->check_logged_in();
		// check that id is an intenger
		if ($id == NULL) {
			$this->ajax->redirect('/profile');
			//$this->ajax->show_app_error();
			return;
		}
		
		if (!is_numeric($id)) {
			show_error('Invalid id type.',500);
			return;
		}
			
		$this->load->model('hcp_model');
		$this->load->model('patient_model');
		$this->load->model('connections_model');
		
		// Checks the user_id, if passes, get their info 
		if ($this->hcp_model->is_hcp(array($id))) {
			$info = $this->hcp_model->get_hcp(array($id));
			$id_type = 'hcp';
		}
		else if ($this->patient_model->is_patient(array($id))) {
			$info = $this->patient_model->get_patient(array($id));
			$id_type = 'patient';
		} else {
			show_error('The specified <i>id</i> does not correspond
			neither to an HCP nor a patient');
			return;
		}
		
		if( $info === -1 ){
			$this->ajax->view(array('Query error grom get_doctor/get_patient function!',''));
			return;
		}
		
		// check that logged in user is a hcp. 
		/*if ($this->auth->get_type() == 'hcp' && $id_type != 'patient') {
			show_error('Sorry! An HCP can only view profiles of connected patients');
			return;
		}*/
		if ($this->auth->get_type() == 'patient' && $id_type == 'patient') {
			show_error('Sorry! Patients cannot be connected with other patients',500);
			return;
		}
	
		// check that the id is friends with logged in user
		$is_my_friend = $this->connections_model->is_connected_with($this->auth->get_account_id(), $id);
		
		if ($is_my_friend === -1){
			$this->ajax->view(array('Query error from is_connected_with function!',''));
			return;		
		}else if (!$is_my_friend && $id_type === 'patient' ){
			$this->ajax->view(array('You are not connected. Permission Denied.',''));
			return;		
		}

		// Show the side panel based on logged in type.
		if ($this->auth->get_type() == 'hcp') {
			$sideview = $this->load->view('sidepane/hcp-profile', '' , TRUE);
		} else if ($this->auth->get_type() == 'patient') {
			$sideview = $this->load->view('sidepane/patient-profile', '' , TRUE);
		} else {
				show_error('Internal Logic Error.',500);
				return;
		}
		
		// Load up the right view
		if ($id_type == 'hcp') {
			$this->ajax->view(array(
				$this->load->view('mainpane/see_hcp',
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
	 * Loads a form that shows their current information, with ability to change and submit.
	 * */
	function edit() {
		$this->auth->check_logged_in();
		$this->load->model('patient_model');
		$this->load->model('hcp_model');
		
		// Get my current info
		if ($this->auth->get_type() === 'patient') {
			$res = $this->patient_model->get_patient(array($this->auth->get_account_id()));
			$mainview = 'mainpane/edit_patient_info';
		}
		else if ($this->auth->get_type() === 'hcp') {
			$res = $this->hcp_model->get_hcp(array($this->auth->get_account_id()));
			$mainview = 'mainpane/edit_hcp_info';
		} else {
			show_error('Server error');
			return;
		}
		
		if ($res === -1) {
			show_error('Query error');
			return;
		}
		else if (count($res) <= 0) {
			show_error('Server error');
			return;
		}
		
		$this->ajax->view(array(
			$this->load->view($mainview, array('curr_info' => $res[0]), TRUE),
			''
		));
	}

	/* *	
	 * Updates database with user's editted personal information
	 * @return confirmation statement + email || error
	 * */
	function edit_do() {
		$this->auth->check_logged_in();
		
		if ( $this->auth->get_type() === 'patient'){
			$this->load->model('patient_model');
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
			$this->load->model('hcp_model');
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
			show_error('Server Error.', 500);
			return;
		}
		
		if ($res === -1) {
			$this->ajax->view(array('Query error',''));
			return;
		}
		
		$view = 'Your changes have been made. Please <a href="https://'.$_SERVER['SERVER_NAME'].
				'/home/logout/">logout</a> and log back in for changes to take full effect.';
				
		$this->ajax->view(array($view,''));
	}
}
/** @} */
?>

<?php
class Profile extends Controller {

	function __construct(){
		parent::Controller();
		$this->load->library('ajax');	
		$this->load->library('auth');
	}

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

	function user($id = NULL) {
		if ($id == NULL) {
			$this->ajax->redirect('/profile');
			//$this->ajax->show_app_error();
			return;
		}
		
		// Authentication issue
		$this->auth->check_logged_in();
		
		$this->load->model('hcp_model');
		$this->load->model('patient_model');
		$this->load->model('connections_model');
		
		// Check if the given id is a patient or a doctor
		if ($this->hcp_model->is_doctor(array($id))) {
			$info = $this->hcp_model->get_doctor(array($id));
			$id_type = 'doctor';
		}
		else if ($this->patient_model->is_patient(array($id))) {
			$info = $this->patient_model->get_patient(array($id));
			$id_type = 'patient';
		}
		else {
			show_error('The specified <i>id</i> does not correspond
			neither to an HCP nor a patient');
			return;
		}
		
		// Check whether they are connected
		$is_my_friend = $this->connections_model->is_connected_with($this->auth->get_account_id(), $id);
		
		// A doctor can view only connected patients profiles
		if ($this->auth->get_type() == 'doctor' && $id_type == 'patient' && ! $is_my_friend) {
			show_error('Sorry! An HCP can only view profiles of connected patients');
			return;
		}
		
		// Select the right side-pane view
		if ($this->auth->get_type() == 'doctor') {
			$sideview = $this->load->view('sidepane/doctor-profile', '' , TRUE);
		} else {
			$sideview = $this->load->view('sidepane/patient-profile', '' , TRUE);
		}
		
		// Load up the right view
		if ($id_type == 'doctor') {
			$this->ajax->view(array(
				$this->load->view('mainpane/see_doctor',
					array('info' => $info[0], 'is_my_friend' => $is_my_friend), TRUE), 
				$sideview
			));
		} else { // looking for a patient profile
			$this->ajax->view(array(
				$this->load->view('mainpane/see_patient',
					array('info' => $info[0], 'is_my_friend' => $is_my_friend), TRUE),
				$sideview
			));
		}
	}

	// loads form that allows me to edit my info
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
			this->patient_model->update_personal_info(array(
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
			this->hcp_model->update_personal_info(array(
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
?>

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

	function user($id){
		$this->auth->check_logged_in();

		if ($this->auth->get_type() === 'doctor' ){

			/* only should work if they are connected!
			NEED A MODEL AND IS CONNECTED FUNCTION
			$this->load->model('_______');  
			$check = $this->____->is_connected(array('id' => $id)); 
			*/
			$this->ajax->view(array(
				$this->load->view('mainpane/see_patient', $id , TRUE),  /* pass in account id! */
				$this->load->view('sidepane/doctor-profile', $id , TRUE)
			));

		}

		else if ($this->auth->get_type() === 'patient' ){
			$this->ajax->view(array(
				$this->load->view('mainpane/see_doctor', '', TRUE), 
				$this->load->view('sidepane/patient-profile', '', TRUE)
			));
		}

		else{
			show_error('Unknown Error.', 500);
			return;
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

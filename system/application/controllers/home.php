<?php
class Home extends Controller {

	function __construct() {
		parent::Controller();
		$this->load->library('ajax');
		$this->load->library('auth');
	}
	
	// Default function: loads Default Home Page
	function index()
	{
		// Not logged in
		if (!$this->auth->is_logged_in()) {
			$this->ajax->view(array(
				$this->load->view('mainpane/welcome', '', TRUE),
				$this->load->view('sidepane/login', '', TRUE)
			));
		}

		// Already logged in
		else {
			$this->ajax->redirect('/profile');
		}
	}

	function login()
	{
		if ($this->auth->is_logged_in()) {
			show_error('You are already logged in', 200);
			return;
		}
		
		// get email & password
		$email = $this->input->post('email');
		$password = $this->input->post('password');
		
		if ($email == FALSE || $password == FALSE) {
			show_error('Access to this page not allowed', 500);
			return;
		}
		
		$this->load->model('login_model');
		
		// verify login
		//returns array: of an array that has : 1st element = type, 2nd element= whole user's tuple
		$results = $this->login_model->authorize(array("email" => $email,"password" => $password));
		
		// login fails : error view
		if ($results === NULL)
		{
			$this->ajax->view(array(
				'',
				$this->load->view('sidepane/login_failed', '', TRUE)
			));
		}

		// login successful : store info for session id, go to user profile
		else
		{
			if ($results[0] != 'patient' && $results[0] != 'doctor') {
				$this->ajax->view(array(
					'',
					$this->load->view('sidepane/login_failed', '', TRUE)
				));
			} else {
				$login_data = array(
					'account_id' => $results[1]["account_id"],
					'email' => $results[1]["email"],
					'type' => $results[0],
					'first_name' => $results[1]["first_name"],
					'last_name' => $results[1]["last_name"]
				);
				$this->session->set_userdata($login_data);
				$this->ajax->redirect('/profile');
			}
		}

	}

	// Logout User: Clear session id, redirect to default view
	function logout()
	{
		$this->session->sess_destroy();
		$this->ajax->redirect('/');
	}

	/* @todo- view to retrieve password*/
	function retrieve_password()
	{
		$this->load->view('mainpane/retrieve_password', '', TRUE);
		$email = $this->input->post('email');

		// need a retrieve password function in login model
		$this->load->model('account_model');
		$password = this->account_model->get_password($email); 
		
		echo "Your password is $password";		
		// Fancy Feature later: actually email the password to the user.
	}

	function register()
	{
		$this->ajax->view(array(
					$this->load->view('mainpane/registration', '', TRUE),
					$this->load->view('sidepane/default', '', TRUE)
				));
		$email = $this->input->post('email');
		$password = $this->input->post('password');
		$type = $this->input->post('type');
		$first_name = $this->input->post('first_name');
		$middle_name = $this->input->post('middle_name');
		$last_name = $this->input->post('last_name');
		$dob = $this->input->post('dob');
		$sex = $this->input->post('sex');
		$ssn = $this->input->post('ssn');
		$tel_no = $this->input->post('tel_no');
		$fax_no = $this->input->post('fax_no');
		$address = $this->input->post('address');

		$this->load->model('account_model');
		$account_id = this->account_model->add_account(array('email' => $email, 'password' => $password)); 

		if ($type === 'patient'){
			$this->load->model('patient_model');
			this->patient_model->register(array(
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

		else if ($type === 'doctor'){
			$this->load->model('hcp_model');
			$this->hcp_model->register(array(
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

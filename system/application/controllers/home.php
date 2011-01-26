<?php
class Home extends Controller {

	private $account_id;

	function __construct() {
		parent::Controller();
		$this->load->library('ajax');
		$this->account_id = $this->session->userdata('account_id');	
	}
	
	// Default function: loads Default Home Page
	function index()
	{
		// Not logged in
		if (!$this->account_id) {
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
		// get email & password
		$email = $this->input->post('email');
		$password = $this->input->post('password');
		
		if ($email == FALSE || $password == FALSE) {
			show_error('Access to this page not allowed', 500);
			return;
		}
		
		$this->load->model('login');
		
		// verify login
		//returns array: of an array that has : 1st element = type, 2nd element= whole user's tuple
		$results = $this->login->authorize(array("email" => $email,"password" => $password));
		
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
				header ("Location: /profile/index");
			}
		}

	}

	// Logout User: Clear session id, redirect to default view
	function logout()
	{
		$this->session->sess_destroy();
		header ("Location: /");
	}

	/* Fancy Feature?
	function retrieve_password()
	{
		/** @todo Change *
		// need a retrieve password form(user should input email address)
		$this->load->view('mainpane/retrieve_password', '', TRUE);
		$email = $this->input->post('email');

		// need a retrieve password function in login model
		$this->load->model('login');
		$password = this->login->get_password($email); 
		
		$this->ajax->view(array("Your password has been emailed to you.",""));

		// Fancy Feature later: actually email the password to the user.
	}

	function register()
	{
		/** @todo Change *
		$this->ajax->view(array("Register here!",""));
		$this->load->view('mainpane/register_form', '', TRUE);

		// Fancy Feature later: upon completion, email the user a confirmation report. 
	}*/

}
?>

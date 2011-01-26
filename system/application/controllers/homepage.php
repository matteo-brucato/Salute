<?php
class Homepage extends Controller {

	function __construct() {
		parent::Controller();
		$this->load->library('ajax');
	}
	
	// Default function: loads Default Home Page
	function index()
	{
		// Main Panel: Welcome Statements
		// Side Panel: Login/Password Fields, Forgot Pwd, Register
		$this->ajax->view(array(
			$this->load->view('mainpane/welcome', '', TRUE),
			$this->load->view('sidepane/login', '', TRUE)
		));
	}

	function login()
	{
		// get email & password
		$email = $this->input->post('email');
		$password = $this->input->post('password');

		$this->load->model('login');

		// verify login
		//returns array: of an array that has : 1st element = type, 2nd element= whole user's tuple
		$results = $this->login->authorize(array("email" => $email,"password" => $password)); // <-- this will be an array
		$type = ($results[0]);
		
		// login fails : error view
		if ($results === NULL || ($type != 'patient' && $type != 'doctor') 
		{
			$this->ajax->view(array(
				'',
				$this->load->view('sidepane/login_failed', '', TRUE)
			));
		}

		// login successful : store info for session id, go to user profile
		else
		{
			$login_data = array('account_id'=> $results[1]["account_id"] , 'email' => $results[1]["email"], 'type' => $type);
			$this->session->set_userdata($login_data);
			redirect('profile/index','location');
		}

	}

	// Logout User: Clear session id, redirect to default view
	function logout()
	{
		$array = array('account_id' => '', 'email' => '' , 'type' => '');
		$this->session->unset_userdata($array);
		redirect('homepage/index','location');
	}

	// Fancy Feature?
	function retrieve_password()
	{
		/** @todo Change */
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
		/** @todo Change */
		$this->ajax->view(array("Register here!",""));
		$this->load->view('mainpane/register_form', '', TRUE);

		// Fancy Feature later: upon completion, email the user a confirmation report. 
	}

}
?>

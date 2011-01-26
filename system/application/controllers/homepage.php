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
		// if login/password // aka call a model that accesses database, returns ID and type of user(patient||doctor), store in a session_id(private)
		//	if success -> load profile view
		// 	else -> load error view
		$email = $this->input->post('email');
		$password = $this->input->post('password');
//		echo "$email $password";

		$this->load->model('login');
		$results = $this->login->authorize(array("email" => $email,"password" => $password)); // <-- this will be an array

//		$results = NULL;
		// if login fails, only change side panel w/ login failed message
		if ($results === NULL) {
			$this->ajax->view(array(
				'',
				$this->load->view('sidepane/login_failed', '', TRUE)
			));
		}

		// login successful, store info for session id, go to user profile
		else{
			// parse out the email, password, type from $results and store in an array to pass into session class fn
			// $array = ( $results[0].email, $results[0].password, $results[0].type ); 
			// $this->session->set_userdata(array);
			// redirect to profile page. 
			// redirect('profile/index','location')
		}

	}

	function logout()
	{
		// returns to index/Default home page view	
		// clear session id (cookie of info)	
	}

	function retrieve_password()
	{
		/** @todo Change */
		$this->ajax->view(array("Your password has been emailed to you.",""));
	}

	function register()
	{
		/** @todo Change */
		$this->ajax->view(array("Register here!",""));
	}

}
?>

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
		$this->load->view('welcome', '', TRUE),
		$this->load->view('login', '', TRUE)
		));
	}

	function login()
	{
		// if login/password // aka call a model that accesses database, returns ID and type of user(patient||doctor), store in a session_id(private)
		//	if success -> load profile view
		// 	else -> load error view
		$email = $this->input->post('email');
		$password = $this->input->post('password');
		echo "$email $password";
	}

	function logout()
	{
		// returns to index/Default home page view	
		// clear session id (cookie of info)	
	}

}
?>

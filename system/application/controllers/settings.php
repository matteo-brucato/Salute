<?php
class Settings extends Controller {

//	private $type;

	function __construct(){
		parent::Controller();
		$this->load->library('ajax');	
		$this->load->library('auth');
		//$this->type = $this->session->userdata('type');
	}

	// Default
	function index(){
		$this->auth->check_logged_in();
		// load view that provides the following links: 
		//	deactivate account(link to fn below)
		//	edit my info(link to profile controller fn called 'edit_info')
		// 	edit permissions(link to list of med recs)
	}

	// Deactivate Account
	function deactivate() {
		$this->auth->check_logged_in();
	}
}
?>

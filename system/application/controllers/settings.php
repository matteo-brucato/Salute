<?php
/**
 * @file settings.php
 * @brief Controller to manage user settings
 *
 * @defgroup ctr Controllers
 * @ingroup ctr
 * @{
 */

class Settings extends Controller {

	function __construct(){
		parent::Controller();
		$this->load->library('ajax');	
		$this->load->library('auth');
	}

	// Default
	function index(){
		$this->auth->check_logged_in();
		// load view that provides the following links: 
		//	deactivate account(link to fn below)
		//	edit my info(link to profile controller fn called 'edit_info')
		// 	edit permissions(link to list of med recs)
	}
	
	function change_password(){
		$this->auth->check_logged_in();	
		
	}
	
	function change_email(){
		$this->auth->check_logged_in();	
	
	}

	function change_email_do(){
		$this->auth->check_logged_in();	
		$this->load->model('account_model');	
	}
	
	function change_password_do(){
			$this->auth->check_logged_in();	
			$this->load->model('account_model');		
	}
	
	// Deactivate Account
	function deactivate() {
		$this->auth->check_logged_in();
	}
}
/** @} */
?>

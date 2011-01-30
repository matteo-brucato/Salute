<?php
/**
 * @file messages.php
 * @brief Controller to send/receive/view messages
 *
 * @defgroup ctr Controllers
 * @ingroup ctr
 * @{
 */

class Messages extends Controller {

// 	private $type;

	function __construct(){
		parent::Controller();
		$this->load->library('ajax');	
		$this->load->library('auth');
		// $this->type = $this->session->userdata('type');	
	}

	// Default: call inbox function
	function index(){
		$this->auth->check_logged_in();
	}

  	/* List all messages in Inbox */      	   
	function inbox() { 
		$this->auth->check_logged_in();
	}

	/*View only sent messages*/
	function sent() {
		$this->auth->check_logged_in();
	}

	/*View only messages saved as a draft*/	
	function drafts(){
		$this->auth->check_logged_in();
	}
	
	/*Compose an email*/
	function compose() {
		$this->auth->check_logged_in();
	}
	
	/*Send the email (called from compose function) */
	function send() {
		$this->auth->check_logged_in();
	}	

	// Delete an email
	function delete() {
		$this->auth->check_logged_in();
	}
}
/** @} */
?>

<?php
class Messages extends Controller {

// 	private $type;

	function __construct(){
		parent::Controller();
		$this->load->library('ajax');	
		//check if you're logged in
		// $this->type = $this->session->userdata('type');	
	}

	// Default: call inbox function
	function index()
	{}

  	/* List all messages in Inbox */      	   
	function inbox()
	{}

	/*View only sent messages*/
	function sent()
	{}

	/*View only messages saved as a draft*/	
	function drafts()
	{}
	
	/*Compose an email*/
	function compose()
	{}
	
	/*Send the email (called from compose function) */
	function send()
	{}	

	// Delete an email
	function delete()
	{}
}
?>

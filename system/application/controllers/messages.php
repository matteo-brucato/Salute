<?php
class Messages extends Controller {

	function __constructor()
	{
		parent::Controller();
		//check if you're logged in	
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

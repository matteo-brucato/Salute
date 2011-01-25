<?php
class Settings extends Controller {

	function __constructor()
	{
		parent::Controller();
		//check if you're logged in	
	}

	// Default
	function index()
	{
		// load view that provides the following links: 
		//	deactivate account(link to fn below)
		//	edit my info(link to profile controller fn called 'edit_info')
		// 	edit permissions(link to list of med recs)
	}

	// Deactivate Account
	function deactivate()
	{}
}
?>

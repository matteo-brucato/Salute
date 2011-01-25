<?php
class Connection extends Controller {

	function __constructor()
	{
		parent::Controller();
		//check if you're logged in	
	}

	// Default: call list_all
	function index()
	{}

	// list all my connections
	function list_all()
	{}

	// list all my pending connections
	function pending() 
	{
		// Case 1: Doctor's Incoming Doctor Requests
		// Case 2: Doctor's Outgoing Doctor Requests
		// Case 3: Doctor's Incoming Patient Requests
		// Case 4: Patients's Outgoing Doctor Requests
	}

	// Request a connection ( aka request to be friends with a doctor )
	function request()
	{
		// Case 1: Patient requests connection with a Doctor
		// Case 2: Doctor requests connection with another Doctor
	}

	// Establish Connection ( aka accept friend request )
	// Only Doctors do this 
	function establish()
	{}

	// destroy connection (un-friend someone)
	function destroy()
	{}


}
?>

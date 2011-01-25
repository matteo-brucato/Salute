<?php
class Bills extends Controller {

	function __constructor()
	{
		parent::Controller();
		//check if you're logged in	
	}

	/* Default: call all function */
	function index()
	{}
  	
	/* List all bills  */      	   
	function all()
	{
		// if Doctor --> Additional Option: list all bills of a specific patient id
		// 	e.g. Doctor wants to see the all bills a specific patient has with him/her
	}

	/* List Current Bills */
	function current()
	{
		// if Doctor --> Additional Option: list all current bills of a specific patient id
		// 	e.g. Doctor wants to see the current bills a specific patient owes him/her
	}

	/* Lists past bills */	
	function past()
	{
		// if Doctor --> Additional Option: list all past bills of a specific patient id
		// 	e.g. Doctor wants to see all the bills a specific patient paid him/her
	}

	// load form , charge patient an amount for an procedure/appointment/test, upload itemized receipt	
	// update database
	// Only available for doctors
	function issue_new_bill()
	{}

	// only available to patient: pay a bill
	function pay()
	{}

}
?>

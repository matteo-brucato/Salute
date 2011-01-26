<?php
class Homepage extends Controller {

	function index()
	{
		// loads 'Default Home Page' (see google Doc)	
	}

	function login()
	{
		// if login/password // aka call a model that accesses database, returns ID and type of user(patient||doctor), store in a session_id(private)
		//	if success -> load profile view
		// 	else -> load error view
	}

	function logout()
	{
		// returns to index/Default home page view	
		// clear session id (cookie of info)	
	}

}
?>

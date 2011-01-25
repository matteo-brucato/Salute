<?php
class Example extends Controller {
	
	function __construct() {
		parent::Controller();
		$this->load->library('ajax');
	}
	
	function index()
	{
		// Example of changing both panels (main AND side)
		$this->ajax->view(array(
			$this->load->view('example', '', TRUE),
			$this->load->view('lorem-ipsum', '', TRUE)
		));
	}
	
	function different()
	{
		// Example of changing ONLY main panel
		$this->ajax->view(array(
			$this->load->view('try.html', '', TRUE),
			''
		));
	}
	
	function comments()
	{
		$this->load->model('try_model');
		$results = $this->try_model->get_entries(); // <-- this will be an array
		
		// Example of changing ONLY side panel and how to pass
		// to a view some data result retriven from a database
		$this->ajax->view(array(
			'',
			$this->load->view('show_account', array('users' => $results), TRUE)
		));
	}
	
}
?>

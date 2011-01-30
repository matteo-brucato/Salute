<?php
class Example extends Controller {
	
	function __construct() {
		parent::Controller();
		$this->load->library('ajax');
		$this->load->library('auth');
	}
	
	function index()
	{
		$this->load->model('hcp_model');
		$this->load->model('connections_model');
		$results = $this->hcp_model->get_doctors();
		// Example of changing both panels (main AND side)
		$this->ajax->view(array(
			$this->load->view('mainpane/alldoctors', array('doc_list' => $results) , TRUE),
			$this->load->view('sidepane/patient-profile', '', TRUE)
		));
	}
	
	function different()
	{
		// Example of changing ONLY main panel
		$this->ajax->view(array(
			$this->load->view('mainpane/try.html', '', TRUE),
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
			$this->load->view('sidepane/show_account', array('users' => $results), TRUE)
		));
	}
	
}
/** @} */
?>

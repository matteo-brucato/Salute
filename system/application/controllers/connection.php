<?php
class Connection extends Controller {

 	private $type;
	private $account_id;

	function __construct(){
		parent::Controller();
		$this->load->library('ajax');	
		$this->type = $this->session->userdata('type');
		$this->account_id = $this->session->userdata('account_id');	
	}

	// Default: call list_all
	function index()
	{
		header ("Location: /connection/list_all");
	}

	// list all my connections
	function list_all()
	{
		$this->load->model('connections');
		$results = $this->connections->list_all(array('account_id' => $this->account_id,'type' => $this->type)); 
		$this->ajax->view(array($this->load->view('mainpane/list_connections', $results , TRUE)));

	}

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

<?php
class Connection extends Controller {

 	private $type;
	private $account_id;
	private $email;
	private $first_name;
	private $last_name;

	function __construct(){
		parent::Controller();
		$this->load->library('ajax');	
		$this->type = $this->session->userdata('type');
		$this->account_id = $this->session->userdata('account_id');	
		$this->email = $this->session->userdata('email');
		$this->last_name = $this->session->userdata('last_name');	
		$this->first_name = $this->session->userdata('first_name');		
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

	// Request a connection ( aka request to be friends with a doctor )
	function request($id)
	{
		// TODO: model to check that $id is a doctor, returns boolean
		$this->load->model(Doctor); 
		$check = $this->Doctor->isDoctor(array('id' => $id)); 

		if (!$check || $this->type === 'patient'){
			// error: Only Doctors can establish connections
			// 	  Only Doctors can be connected to. 
			show_error('Permission Denied.', 500);
			return;
		}

		else if{}

		else{
			// You are not logged in. redirect!
			$this->ajax->redirect('/');
		}

					
		// Send default friend request email to doctor
		$this->load->library('email');
		$this->email->from($this->email, $this->first_name $this->last_name);
		$this->email->to('someone@example.com'); // need the email!
		$this->email->subject('New Connection Request');
		
		// need to put a link in here to 'accept' connection, 
		$this->email->message('You have a new connection request from [first name] [Last name]. Click here to accept.');
		$this->email->send();
	}

	// Establish Connection ( aka accept friend request )
	// Only Doctors do this 
	function establish($id) // this needs to take in an account id as an argument. 
	{
		// doctor is connecting with a doctor
		else if ($this->type === 'doctor' && $check){
			// TODO: model, insert a connection
			$this->load->model('connections');
			$results = $this->connections->connect(array('account_id' => $this->account_id)); 
			// expecting a bool from $results
			if($results){
				// TODO: main panel view that says "Your connection has been successfully requested, you will receive an email upon confirmation"
				$this->ajax->view(array($this->load->view('mainpane/connection_requested', '' , TRUE)));
			}		
		}

	}

	// destroy connection (un-friend someone)
	function destroy($id) // TODO: needs to take in an id.
	{
		// TODO: need model to delete connection
		$this->load->model('connections');
		$delete = $this->connections->remove(array('account_id_1' => $this->account_id , 'account_id_2' => $id )); // expecting boolean
		if ($delete){
			echo "Success";
		}
		else {
			echo "Unable to remove connection";
		}
	}

	/* Fancy Feature: list all my pending connections
	function pending() 
	{
		// Case 1: Doctor's Incoming Doctor Requests
		// Case 2: Doctor's Outgoing Doctor Requests
		// Case 3: Doctor's Incoming Patient Requests
		// Case 4: Patients's Outgoing Doctor Requests
	}*/


}
?>

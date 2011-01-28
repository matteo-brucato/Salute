<?php
class Connections extends Controller {

	function __construct(){
		parent::Controller();
		$this->load->library('ajax');
		$this->load->library('auth');
	}


	function index()
	{
		show_error('Direct access to this resource is forbidden', 500);
		return;
	}

	//TODO: fix this to make it only list docs
	function list_doctors()
	{
		//$this->output->enable_profiler(TRUE); // Nice debug feature
		$this->auth->check_logged_in();		/** @attention Put this function in each
												controller function that needs authorization
												to be executed */
		$this->load->model('connections_model');
		//$results = $this->connections->list_doctors(array('account_id' => $this->account_id)); 
		$results = array(
			array('first_name' => 'Mario', 'last_name' => 'Rossi', 'specialty' => 'Murderer'),
			array('first_name' => 'Matteo', 'last_name' => 'Brucato', 'specialty' => 'Surgeon')
		); /** @todo Remove this and uncomment above, when Model is available */
		$this->ajax->view(array(
			$this->load->view('mainpane/mydoctors', array('hcp_list' => $results) , TRUE),
			''	/** @note Remember to specify always 2 views... 
					What view do you want to give as sidepanel? 
					I put '', just to fix the error. */
		));
	}

	//TODO: fix this to make it only list patients
	function list_patients()
	{
		//$this->output->enable_profiler(TRUE); // Nice debug feature
		$this->auth->check_logged_in();
		
		if ($this->auth->get_type() !== 'doctor'){
			show_error($this->load->view('errors/not_hcp', '', TRUE));
			return;
		}

		$this->load->model('connections_model');
		//$results = $this->connections->list_patients(array('account_id' => $this->account_id)); 
		$results = array(
			array('first_name' => 'Mario', 'last_name' => 'Rossi', 'specialty' => 'Murderer'),
			array('first_name' => 'Matteo', 'last_name' => 'Brucato', 'specialty' => 'Surgeon')
		); /** @todo Remove this and uncomment above, when Model is available */
		$this->ajax->view(array(
			$this->load->view('mainpane/mypatients', array('pat_list' => $results) , TRUE),
			''	/** @note Remember to specify always 2 views... 
					What view do you want to give as sidepanel? 
					I put '', just to fix the error. */
		));
	}

	// Request a connection ( aka request to be friends with a doctor )
	function request($id)
	{
		//$this->output->enable_profiler(TRUE); // Nice debug feature
		$this->auth->check_logged_in();
		
		// TODO: model to check that $id is a patient, returns boolean
		$this->load->model('connections'); 
		$check = $this->search->is_patient(array('id' => $id)); 
		$check2 = $this->search->is_doctor(array('id' => $id)); 

		// if the id you are requesting is a patient, not allowed
		if (!$check){
			show_error('Permission Denied.', 500);
			return;
		}

		// the id requested is a doctor
		else if($check2){
			// Send default friend request email to doctor
			echo "$this->first_name $this->last_name";
			return;
			$this->load->library('email');
			$this->email->from($this->email, "$this->auth->first_name $this->auth->last_name");
			$this->email->to('someone@example.com'); // need the email!
			$this->email->subject('New Connection Request');
			
			// need to put a link in here to 'accept' connection, 
			$this->email->message('You have a new connection request from [first name] [Last name]. Click here to accept.');
			$this->email->send();
		}

		else{
			show_error('Unexpected Errors have occured.', 500);
			return;
		}			
	}

	// Establish Connection ( aka accept friend request )
	// Only Doctors do this 
	function establish($id) // this needs to take in an account id as an argument. 
	{
		$this->load->model('connections');
		// doctor is connecting with a doctor
		if ($this->auth->type === 'doctor'){
			// TODO: model, insert a connection b/w doc - doc
			$results = $this->connections->connect_doc(array('account_id' => $this->account_id, 'account_id_2' => $id )); 
		}

		// patient is connecting with doctor		
		else if ($this->auth->type === 'patient') {
			// TODO: model, insert a connection b/w patient - doc
			$results = $this->connections->connect_pat(array('account_id' => $this->account_id, 'account_id_2' => $id )); 
		}
		else {
			// you are not logged in
			$this->ajax->redirect('/');
		}
			
		// expecting a bool from $results
		if($results){
			// TODO: main panel view that says "Your connection has been successfully requested, you will receive an email upon confirmation"
			// $this->ajax->view(array($this->load->view('mainpane/_______', '' , TRUE)));
			echo "success";
		}
		else{
			show_error('Connection Establishment Failed.', 500);
			return;
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

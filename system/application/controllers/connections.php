<?php
/**
 * @file connections.php
 * @brief Controller to handle connections
 *
 * @defgroup ctr Controllers
 * @ingroup ctr
 * @{
 */

/**
 * Class Controller Connections
 * 
 * @test The whole class has been succesfully tested.
 * @bug No known bugs reported
 * */
class Connections extends Controller {

	function __construct() {
		parent::Controller();
		$this->load->library('ui');
		$this->load->library('auth');
		$this->load->model('connections_model');
		$this->load->model('hcp_model');
	}

	/**
	 * Default method
	 * @attention should never be accessible
	 * @return error
	 * */
	function index() {
		//$this->ui->set_error('Direct access to this resource is forbidden', 'forbidden');
		//return;
		$this->myhcps();
	}

	/**
	 * List all hcps that current user is connected with
	 * 
	 * @attention Available for both patients and hcps
	 * 
	 * @return: List view of hcps I'm connected with
	 * 
	 * @test Works fine
	 * */
	function myhcps() {
		$check = $this->auth->check(array(auth::CurrLOG));
		if ($check !== TRUE) return;
		
		$results  = $this->connections_model->list_my_hcps($this->auth->get_account_id()); 
		
		if ($results === -1) {
			$this->ui->set_query_error();
			return;
		}
		
		$mainview = $this->load->view('mainpane/lists/hcps',
			array('list_name' => 'My Hcps', 'list' => $results, 'status' => 'connected') , TRUE);
		
		// Give results to the client
		$this->ui->set(array($mainview));
	}

	/**
	 * List all patients that current hcp is connected with
	 * 
	 * @return: List view of patients
	 *
	 * @test Works fine
	 * */
	function mypatients()
	{
		$check = $this->auth->check(array(auth::CurrLOG));
		if ($check !== TRUE) return;
		
		$res = $this->connections_model->list_my_patients($this->auth->get_account_id());
		
		if ($res === -1) {
			$this->ui->set_query_error();
			return;
		}
		
		$mainview = $this->load->view('mainpane/lists/patients',
			array('list_name' => 'My Patients', 'list' => $res, 'status' => 'connected') , TRUE);
		
		// Give results to the client
		$this->ui->set(array($mainview));
	}
	
	/**
	 * Lists the pending connections that this user has initiated
	 * 
	 * @param
	 *   $direction
	 *   If it is 'out', it lists pending outgoing connections,
	 *   otherwise it lists pending incoming connections.
	 * 
	 * @attention Only hcps can see pending(in)
	 * 
	 * @test Tested in and out and also incorrect inputs.
	 * @test Tested pendings for both patients and hcps and worked fine
	 * */
	function pending($direction = 'out')
	{
		if ($direction == 'in') {
			$this->_pending_in();
		}
		else if ($direction == 'out') {
			$this->_pending_out();
		}
		else {
			$this->ui->set_error('Input not valid: <b>'.$direction.'</b>');
		}
	}
	
	/**
	 * Private function to list all pending outgoing connection requests
	 * 
	 * @note This function is available for both patients and hcps
	 * */
	function _pending_out()
	{
		$check = $this->auth->check(array(auth::CurrLOG));
		if ($check !== TRUE) return;
		
		// Take pending incoming from other patients
		$pats = $this->connections_model->pending_outgoing_patients(array($this->auth->get_account_id()));
		// Take pending incoming from other hcps
		$hcps = $this->connections_model->pending_outgoing_hcps(array($this->auth->get_account_id()));
		if ($pats === -1 || $hcps === -1) {
			$this->ui->set_query_error();
			return;
		}
		
		// Show the pending requests to doctors
		$mainview = $this->load->view('mainpane/lists/hcps',
			array('list_name' => 'Pending Outgoing Requests to Hcps', 'list' => $hcps, 'status' => 'pending_out') , TRUE);
		
		// Only a patient can send pending requests to patients
		if ($this->auth->get_type() === 'patient') {
			$mainview .= $this->load->view('mainpane/lists/patients',
			array('list_name' => 'Pending Outgoing Requests to Patients', 'list' => $pats, 'status' => 'pending_out') , TRUE);
		}
		
		// Give results to the client
		$this->ui->set(array($mainview));
	}
	
	/**
	 * Private function that lists all pending connections that this 
	 * user has received (incoming)
	 * 
	 * @note Lists both requests from hcps and patients
	 * 
	 * @test Tested!
	 * */
	function _pending_in() 
	{
		$check = $this->auth->check(array(auth::CurrLOG));
		if ($check !== TRUE) return;
		
		if (DEBUG) $this->output->enable_profiler(TRUE);
		
		// Take pending incoming from other patients
		$pats = $this->connections_model->pending_incoming_patients(array($this->auth->get_account_id()));
		// Take pending incoming from other hcps
		$hcps = $this->connections_model->pending_incoming_hcps(array($this->auth->get_account_id()));
		if ($pats === -1 || $hcps === -1) {
			$this->ui->set_query_error();
			return;
		}
		
		// Show the pending requests from patients
		$mainview  = $this->load->view('mainpane/lists/patients',
			array('list_name' => 'Pending Incoming Requests from Patients', 'list' => $pats, 'status' => 'pending_in') , TRUE);
		
		// Only a doctor can receive pending requests from doctors
		if ($this->auth->get_type() === 'hcp') {
			$mainview .= $this->load->view('mainpane/lists/hcps',
				array('list_name' => 'Pending Incoming Requests from Hcps', 'list' => $hcps, 'status' => 'pending_in') , TRUE);
		}
		
		// Give results to the client
		$this->ui->set(array($mainview));
	}
	
	/**
	 * Request a new connection to a hcp.
	 * 
	 * @param
	 *   $id is the id of a hcp you want to connect to
	 * 
	 * @attention
	 *   Can be called by both patients and hcps, but a 
	 * hcp can only request for another hcp.
	 * 
	 * @test Tested different inputs: nothing, string, invalid id
	 * */
	function request($id = NULL)
	{
		$check = $this->auth->check(array(auth::CurrLOG));
		if ($check !== TRUE) return;
		
		// If current user is a hcp
		if ($this->auth->get_type() === 'hcp') {
			
			// An HCP can request connection only to other HCPs
			$check = $this->auth->check(array(auth::HCP, $id));
			if ($check !== TRUE) return;
			
			// Add the connection in the db
			$res = $this->connections_model->add_connection(array(
				$this->auth->get_account_id(),
				$id
			));
		}
		
		// If current user is a patient
		else if ($this->auth->get_type() === 'patient') {
			// Add the connection in the db
			$res = $this->connections_model->add_connection(array(
				$this->auth->get_account_id(),
				$id
			));
		}
		
		else {
			$this->ui->set_error('Internal server logic error.', 'server');
			return;
		}
		
		// Switch the response from the model, to select the correct view
		switch ($res) {
			case -1:
				$this->ui->set_query_error();
				return;
			case -3:
				$this->ui->set_error('This connection has been already requested');
				return;
			default:
				$mainview = 'Your request has been submitted.';
				$type = 'Confirmation';
				$this->load->library('email');
				$config['mailtype'] = 'html';
				$this->email->initialize($config);
				//$this->email->from($this->auth->get_email());
				$this->email->from('salute-noreply@salute.com');
				//$this->email->to($results['email']);
				$this->email->to('mattfeel@gmail.com');
				$this->email->subject('New Connection Request');
				
				$this->email->message(
					'You have a new connection request from '.
					$this->auth->get_first_name().' '.$this->auth->get_last_name().
					'. Click <a href="https://'.$_SERVER['SERVER_NAME'].'/connections/accept/'.$this->auth->get_account_id().'/'.$id.'">here</a> to accept.');
				
				$this->email->send();
				break;
		}
		
		// Give results to the client
		$this->ui->set_message($mainview, $type);
	}

	/** 
	 * Accept an existing connection request
	 * 
	 * @param
	 * 		$requester_id the id of the account you accept a connection with
	 * 
	 * @attention
	 * 		If I'm a patient I can accept connections only from patients
	 * 		If I'm an hcp I can accept connections from hcp's and patients
	 * */
	function accept($requester_id = NULL)
	{
		$check = $this->auth->check(array(auth::CurrLOG));
		if ($check !== TRUE) return;
		
		if ($this->auth->get_type() === 'patient') {
			$check = $this->auth->check(array(auth::PAT, $requester_id));
			if ($check !== TRUE) return;
		}
		
		// Check if parameters are specified
		if ($requester_id == NULL) {
			$this->ui->set_error('ids not specified.', 'Missing Arguments');
			return;
		}
		
		if ($this->patient_model->is_patient(array($requester_id))) {
			$res = $this->connections_model->accept_patient_hcp(array($requester_id, $this->auth->get_account_id()));
		}
		else if ($this->hcp_model->is_hcp(array($requester_id))) {
			$res = $this->connections_model->accept_hcp_hcp(array($requester_id, $this->auth->get_account_id()));
		}
		else {
			$this->ui->set_error('The requester id does not match any id in the database');
			return;
		}
		
		// Switch the response from the model, to select the correct view
		switch ($res) {
			case -1:
				$this->ui->set_query_error();
				return;
			case -2:
				$error = 'Connection does not exist.';
				$type = 'Permission Denied';
				break;
			case -3:
				$error = 'This connection has already been accepted.';
				$type = 'Notice';
				break;
			default:
				$this->ui->set_message('You have accepted the connection.','Confirmation');
				return;
		}

		$this->ui->set_error($error,$type);
	}

	/**
	 * deletes connection (un-friend someone)
	 * @param
	 * 		id is the account_id of the hcp or patient the user would like to disconnect from
	 * @return 
	 * 		error 
	 * 			id not specified (the one to disconnect from)
	 * 			query fails
	 * 			connection doesnt exist
	 * 		success: deleted the connection
	 * 
	 * @test Tested
	 */
	function destroy($id = NULL)
	{
		$this->auth->check_logged_in();
		if (DEBUG) $this->output->enable_profiler(TRUE);
		
		if ($id == NULL) {
			$this->ui->set_error('id not specified');
			return;
		}
		
		$this->load->model('connections_model');
		$res = $this->connections_model->remove_connection($this->auth->get_account_id(), $id);
		
		/*if ($this->patient_model->is_patient(array($id))) {
			$res = $this->connections_model->remove_pd_connection(array($this->auth->get_account_id(), $id)); 
		}
		else if ($this->hcp_model->is_hcp(array($id))) {
			$res = $this->connections_model->remove_dd_connection(array($this->auth->get_account_id(), $id)); 
		}
		else {
			$this->ui->error('Internal Logic Error.', 500);
			return;
		}*/
		
		// Switch the response from the model, to select the correct view
		switch ($res) {
			case -1:
				$this->ui->set_query_error();
				break;
			case -2:
				$this->ui->set_error('Connection does not exist.','Notice');
				break;
			default:
				$this->ui->set_message('You have been disconnected from that health care provider.','Confirmation');
				break;
		}
	}
	
	/**
	 * Removes a pending outgoing connection request
	 * @param
	 * 		id is the account_id of the hcp or patient the user would 
	 * 		like to cancel a connection request to
	 * @return 
	 * 		error 
	 * 			id not specified (the one to disconnect from)
	 * 			query fails
	 * 			connection doesnt exist
	 * 		success: deleted the connection
	 * 
	 * @attention The current user can cancel only requests that he/she
	 * personally made!
	 * @test Tested
	 */
	function cancel($id = NULL)
	{
		$this->auth->check_logged_in();
		if (DEBUG) $this->output->enable_profiler(TRUE);
		
		if ($id == NULL) {
			$this->ui->set_error('id not specified.', 'Missing Arguments');
			return;
		}
		
		$this->load->model('connections_model');
		
		$conn = $this->connections_model->get_connection($this->auth->get_account_id(), $id);
		
		if ($conn === -1) {
			$res = -1;
		}
		else if ($conn === NULL) {
			$res = -2;
		}
		else if ($conn['requester_id'] = $this->auth->get_account_id()) {
			// If I requested this connection, I can cancel it
			$res = $this->connections_model->remove_pending($this->auth->get_account_id(), $id);
		} else {
			$res = -5;
		}
		
		// Switch the response from the model, to select the correct view
		switch ($res) {
			case -1:
				$this->ui->set_query_error();
				break;
			case -2:
				$this->ui->set_error('Connection does not exist.','Permission Denied');
				break;
			case -5:
				$this->ui->set_error('This connection request has not been initiated by you.','Notice');
				break;
			default:
				$this->ui->set_message('Your connection request has been canceled.','Confirmation');
				break;
		}
	}
	
	/**
	 * Removes a pending incoming connection request
	 * @param
	 * 		id is the account_id of the hcp or patient the user would 
	 * 		like to cancel a connection request to
	 * @return 
	 * 		error 
	 * 			id not specified (the one to disconnect from)
	 * 			query fails
	 * 			connection doesnt exist
	 * 		success: deleted the connection
	 * 
	 * @attention The current user can cancel only requests that he/she
	 * personally received!
	 * 
	 * @test Tested
	 */
	function reject($id = NULL)
	{
		$this->auth->check_logged_in();
		if (DEBUG) $this->output->enable_profiler(TRUE);
		
		if ($id == NULL) {
			$this->ui->set_error('id not specified.', 'Missing Arguments');
			return;
		}
		
		$this->load->model('connections_model');
		
		$conn = $this->connections_model->get_connection($this->auth->get_account_id(), $id);
		
		if ($conn === -1) {
			$res = -1;
		}
		else if ($conn === NULL) {
			$res = -2;
		}
		else if ($conn['accepter_id'] = $this->auth->get_account_id()) {
			// If I requested this connection, I can cancel it
			$res = $this->connections_model->remove_pending($this->auth->get_account_id(), $id);
		} else {
			$res = -5;
		}
		
		// Switch the response from the model, to select the correct view
		switch ($res) {
			case -1:
				$this->ui->set_query_error();
				break;
			case -2:
				$this->ui->set_error('Connection does not exist.');
				break;
			case -5:
				$this->ui->set_message('This connection request has been initiated by you.<br />
				Click here to <a href="/connections/cancel/'.$id.'">cancel this request</a>.');
				break;
			default:
				$this->ui->set_message('This connection has been rejected.','Confirmation');
				break;
		}
	}
}
/** @} */
?>

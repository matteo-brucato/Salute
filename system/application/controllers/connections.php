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
		$this->load->model('medical_records_model');
		$this->load->model('hcp_model');
		$this->load->model('account_model');
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
		
		$results  = $this->connections_model->list_hcps_connected_with($this->auth->get_account_id()); 
		
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
		
		$res = $this->connections_model->list_patients_connected_with($this->auth->get_account_id());
		
		if ($res === -1) {
			$this->ui->set_query_error();
			return;
		}
		
		$mainview = $this->load->view('mainpane/lists/patients',
			array('list_name' => 'My Patient Friends', 'list' => $res, 'status' => 'connected') , TRUE);
		
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
	 * Shows a view with all the medical records that you are sharing
	 * and not sharing with a specific account $aid (connected with you)
	 * */
	function permissions($aid = NULL) {
		if ($this->auth->check(array(
			auth::CurrLOG,
			auth::CurrPAT,
			auth::CurrCONN, $aid
		)) !== TRUE) return;
		
		// First, get all the records that $aid can see
		$allowedrecs = $this->medical_records_model->get_patient_records(array($this->auth->get_account_id(), $aid));
		
		// Then, get all the records of the current user (must be a patient)
		$allrecs = $this->medical_records_model->list_my_records(array($this->auth->get_account_id()));
		
		// Get tuple for $aid
		$info = $this->account_model->get_account_info($aid);
		if ($info === -1) {
			$this->ui->set_query_error();
			return;
		}
		if ($info === NULL) {
			$this->ui->set_error('No such account');
			return;
		}

		// All ok
		$this->ui->set(array(
			$this->load->view('mainpane/forms/change_conn_perms',
				array(
				'list_name' => 'Select medical records to share with '.$info['first_name'].' '.$info['last_name'],
				'list' => $allrecs,
				'list2'=>$allowedrecs,
				'aid'=>$aid), TRUE)
		));
	}
	
	function permissions_do($aid = NULL) {
		if ($this->auth->check(array(
			auth::CurrLOG,
			auth::CurrPAT,
			auth::CurrCONN, $aid
		)) !== TRUE) return;
		
		//get array of medical_rec_ids that were selected, empty array if none are selected
		if (isset($_POST["box"]) && is_array($_POST["box"]) && count($_POST["box"]) > 0) {
			$box = $_POST['box'];
		}
		else{
			$box = array();
		}
		
		//get a list of all my medical records
		$allrecs = $this->medical_records_model->list_my_records(array($this->auth->get_account_id()));
		
		for($i = 0; $i < count($allrecs); $i++ ){
			$temp = $allrecs[$i]['medical_rec_id'] ;
			$isit = FALSE;

		
			//check if that medical record was marked
			for( $j = 0; $j < count($box); $j++ )
				if( $box[$j] == $temp )
					$isit = TRUE;
			//if marked
			if( $isit === TRUE ){
				//check if allowed
				$res = $this->medical_records_model->is_account_allowed(array($temp,$aid));
				if ($res === -1) {
					$this->ui->set_query_error();
					return;
				}
				if ($res === FALSE) {
					$result = $this->medical_records_model->allow_permission(array($temp,$aid));
					if ($result === -1) {
						$this->ui->set_query_error();
						return;
					}
				}
			}
			//if not marked
			else if( $isit === FALSE ){
				//check if allowed
				$res = $this->medical_records_model->is_account_allowed(array($temp,$aid));
				if( $res === -1 ){
					$this->ui->set_query_error();
					return;
				}
				if( $res === TRUE ){
					$result = $this->medical_records_model->delete_permission(array($temp,$aid));
					if( $result === -1 ){
						$this->ui->set_query_error();
						return;
					}
					
				}
			}
		}
		
		$this->ui->set_message('Successfully changed Permissions.', 'Confirmation');
		$this->permissions($aid);
	}
	
	/**
	 * Request a new connection to a hcp.
	 * 
	 * @param
	 *   $id is the id of the account you want to connect to
	 * 
	 * @attention
	 *   Can be called by both patients and hcps, but a 
	 * hcp can only request for another hcp.
	 * 
	 * @bug You can still request connection to yourself
	 * */
	function request($receiver_id = NULL)
	{
		$check = $this->auth->check(array(
			auth::CurrLOG,
			auth::BE_PUBLIC, $receiver_id // the receiver must be a public account
		));
		if ($check !== TRUE) return;
		
		// Cannot request connection to yourself
		if ($this->auth->get_account_id() === $receiver_id) {
			$this->ui->set_error('Cannot request connection with yourself');
			return;
		}
		
		// If current user is a hcp
		if ($this->auth->get_type() === 'hcp') {
			// An HCP can request connection only to other HCPs
			$check = $this->auth->check(array(auth::HCP, $receiver_id));
			if ($check !== TRUE) return;
		}
		
		// Add the connection in the db
		$this->db->trans_start();
		$res = $this->connections_model->add_connection(array($this->auth->get_account_id(), $receiver_id));
		$this->db->trans_complete();
		
		// Check errors from model
		if ($res === -1) {
			$this->ui->set_query_error();
			return;
		}
		else if ($res === -2) {
			$this->ui->set_error('This connection has already been requested');
			return;
		}
		else if ($res === -3) {
			$this->ui->set_error('This connection has already been accepted');
			return;
		}
		
		// Get the email of id_refered doctor
		$result = $this->account_model->get_account_email(array($receiver_id));
		if ($result === -1) {
			$this->ui->set_query_error();
			return;
		}
		elseif (count($result) <= 0) {
			$this->ui->set_error('No account found');
			return;
		}
		
		// Everything's fine
		$body = 'You have a new connection request from '.
			$this->auth->get_first_name().' '.$this->auth->get_last_name().
			'. Click <a href="https://'.$_SERVER['SERVER_NAME'].'/connections/accept/'.$this->auth->get_account_id().'">here</a> to accept.';
		
		$this->load->helper('email');
		send_email(
			'salute-noreply@salute.com',
			$result[0]['email'],
			'New Connection Request',
			$body
		);
		
		/*$this->load->library('email');
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
			'. Click <a href="https://'.$_SERVER['SERVER_NAME'].'/connections/accept/'.$this->auth->get_account_id().'/'.$receiver_id.'">here</a> to accept.');
		$this->email->send();*/
		
		// Give results to the client
		$this->ui->set_message('Your request has been submitted.', 'Confirmation');
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
	 * @attention Anybody can delete a connection
	 */
	function destroy($id = NULL)
	{
		$check = $this->auth->check(array(
			auth::CurrLOG,
			auth::CurrCONN, $id		// current must be connected with id
		));
		if ($check !== TRUE) return;
		
		if (DEBUG) $this->output->enable_profiler(TRUE);
		
		// Destroy the connection
		$this->db->trans_start();
		$res = $this->connections_model->remove_connection($this->auth->get_account_id(), $id);
		$this->db->trans_complete();
		
		// Switch the response from the model, to select the correct view
		switch ($res) {
			case -1:
				$this->ui->set_query_error();
				break;
			case -2:
				$this->ui->set_error('Connection does not exist.', 'Notice');
				break;
			default:
				$this->ui->set_message('You have been disconnected from that health account', 'Confirmation');
				break;
		}
		
		$this->ui->set(array(''));
	}

	/** 
	 * Accept an existing connection request
	 * 
	 * @param
	 * 		$sender_id the id of the account you accept a connection with
	 * 
	 * @attention
	 * 		If I'm a patient I can accept connections only from patients
	 * 		If I'm an hcp I can accept connections from hcp's and patients
	 * 		I can accept only connection where I'm the accepter!
	 * */
	function accept($sender_id = NULL)
	{
		if ($this->auth->check(array(
			auth::CurrLOG,
			auth::CurrCONN_RECV, $sender_id	// to accept you must be the receiver of the request
		)) !== TRUE) return;
		
		// Apply logic restrictions
		if ($this->auth->get_type() === 'patient') {
			// If you are a patient, you can accept requests coming from patients
			$check = $this->auth->check(array(auth::PAT, $sender_id));
			if ($check !== TRUE) return;
		}
		
		// Accept the connection
		$this->db->trans_start();
		$res = $this->connections_model->accept_connection(array($sender_id, $this->auth->get_account_id()));
		$this->db->trans_complete();
		
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
				$this->_pending_in();
				return;
		}

		$this->ui->set_error($error, $type);
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
	function cancel($receiver_id = NULL)
	{
		$check = $this->auth->check(array(
			auth::CurrLOG,
			auth::CurrCONN_SND, $receiver_id	// to cancel you must be the sender of the request
		));
		if ($check !== TRUE) return;
		
		if (DEBUG) $this->output->enable_profiler(TRUE);
		
		$this->db->trans_start();
		$res = $this->connections_model->remove_pending($this->auth->get_account_id(), $receiver_id);
		$this->db->trans_complete();
		
		// Switch the response from the model, to select the correct view
		switch ($res) {
			case -1:
				$this->ui->set_query_error();
				break;
			case -2:
				$this->ui->set_error('Connection does not exist or is not pending', 'Permission Denied');
				break;
			default:
				$this->ui->set_message('Your connection request has been canceled', 'Confirmation');
				break;
		}
		
		$this->_pending_out();
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
	function reject($sender_id = NULL)
	{
		if ($this->auth->check(array(
			auth::CurrLOG,
			auth::CurrCONN_RECV, $sender_id	// to reject you must be the receiver of the request
		)) !== TRUE) return;
		
		if (DEBUG) $this->output->enable_profiler(TRUE);
		
		// Reject the request
		$this->db->trans_start();
		$res = $this->connections_model->remove_pending($this->auth->get_account_id(), $sender_id);
		$this->db->trans_complete();
		
		// Switch the response from the model, to select the correct view
		switch ($res) {
			case -1:
				$this->ui->set_query_error();
				break;
			case -2:
				$this->ui->set_error('Connection does not exist.', 'Permission Denied');
				break;
			default:
				$this->ui->set_message('This connection has been rejected.','Confirmation');
				break;
		}
	}
	
	
	/**
	 * Loads the view to change the connection level of an account with another account
	 * 
	 * @param $inputs
	 *   Is of the form:
	 * @return
	 *	 Loads the view
	 * */
	function change_level($aid = NULL) {
		$check = $this->auth->check(array(
			auth::CurrLOG,
			auth::CurrPAT,				// changing level only allowed to patients
			auth::CurrCONN, $aid		// current must be connected with id
		));
		if ($check !== TRUE) return;
		
		$con_level = $this->connections_model->get_connection($aid, $this->auth->get_account_id());
		
		// Switch the response from the model, to select the correct view
		switch ($con_level) {
			case -1:
				$this->ui->set_query_error();
				break;
			case NULL:
				$this->ui->set_error('Connection does not exist.', 'Permission Denied');
				break;
			default:
				if ( $this->auth->get_account_id() === $con_level['sender_id'] ) {
					$this->ui->set(array(
					$this->load->view('mainpane/forms/change_conn_level', array(
																				'aid' => $aid, 
																				'con_level' => $con_level['sender_level']), TRUE)));
				}
				else {
					$this->ui->set(array(
					$this->load->view('mainpane/forms/change_conn_level', array(
																				'aid' => $aid, 
																				'con_level' => $con_level['receiver_level']), TRUE)));
				}	
		}
	}
	
	
	/**
	 * Changes the connection level of an account with another account
	 * 
	 * @param $inputs
	 *   Is of the form: account_id for the account you would like to change the level
	 * @return
	 *	 Display message
	 * */
	function change_level_do($aid = NULL) {
		$check = $this->auth->check(array(
			auth::CurrLOG,
			auth::CurrPAT,				// changing level only allowed to patients
			auth::CurrCONN, $aid));		// current must be connected with id
		if ($check !== TRUE) return;
		
		$new_level = $this->input->post('level');
		if ($new_level == NULL) {
			$this->ui->set_error('Select a level.','Missing Arguments'); 
			return;
		}
		
		$connection = $this->connections_model->get_connection($aid, $this->auth->get_account_id());
		
		// Switch the response from the model, to select the correct view
		if ($connection === -1) {
			$this->ui->set_query_error();
			return;
		}
		if ($connection === NULL) {
			$this->ui->set_error('Connection does not exist.', 'Permission Denied');
			return;
		}
		
		// Decide whether you are the sender or the receiver
		if ($this->auth->get_account_id() === $connection['sender_id']) {
			$tochange = 'sender';
		}
		else {
			$tochange = 'receiver';
		}
		
		// Change the level
		$change = $this->connections_model->update_connection_level(array(
																		  $connection['connection_id'],
																		  $new_level,
																		  $tochange));
		//check return value
		if ($change == -1) {
			$this->ui->set_query_error();
			return;
		}
		$this->ui->set_message('Connection level updated.','Confirmation');
		
		// Now, if you changed to a high level (1 or 3), give him all your med recs
		if ($new_level == '1' || $new_level == '3') {
			if ($this->auth->get_account_id() === $connection['sender_id']) {
				$this->medical_records_model->give_all_ones_medrec_to($this->auth->get_account_id(), $connection['receiver_id']);
			} else {
				$this->medical_records_model->give_all_ones_medrec_to($this->auth->get_account_id(), $connection['sender_id']);
			}
		}
		//$this->change_level($aid);
	}
}
/** @} */
?>

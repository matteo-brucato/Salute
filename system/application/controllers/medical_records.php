<?php
/**
 * @file medical_records.php
 * @brief Controller to handle medical records
 *
 * @defgroup ctr Controllers
 * @ingroup ctr
 * @{
 */

class Medical_records extends Controller {

	function __construct(){
		parent::Controller();
		$this->load->library('ui');
		$this->load->library('auth');
		$this->load->helper(array('form', 'url'));
	}

	/*
	 * Default function call
	 * @return  redirects to list_my_med_recs function
	 */ 
	function index()
	{
		//$this->auth->check_logged_in();
		//$this->ui->redirect('/medical_records/list_med_recs');
		$this->myrecs();
	}

	/*
	 * Lists all medical records of the current patient
	 * 
	 * @return
	 *		if patient-list your records
	 * 		if hcp, redirect to list of his/her patients
	 * 
	 * @attention Only a patient can view this list
	 * 
	 * @bug The medical records model has a problem giving back
	 * information of records created by patients
	 * 
	 * @test Tested
	*/
	function myrecs()
	{
		if (DEBUG) $this->output->enable_profiler(TRUE);
		$this->auth->check_logged_in();
		$this->load->model('medical_records_model');

		// if patient, show all their med recs
		if ($this->auth->get_type() === 'patient') {
			$res = $this->medical_records_model->list_my_records(
				array($this->auth->get_account_id())
			); 
		}
		// if hcp, redirect to My Patients search List
		else if ( $this->auth->get_type() === 'hcp') {
			$this->ui->redirect('/connections/mypatients');
			return;
		} else {
			$this->ui->set_error('Server Error.', 'server');
			return;
		}
		if ($res === -1) {
			$this->ui->query_error();
			return;
		}
		
		// All ok
		$this->ui->set(array(
			$this->load->view('mainpane/list_medical_records',
				array('list_name' => 'My Medical Records', 'list' => $res) , TRUE)
		));
	}
	
	/**
	 * Shows all medical records that a certain patient is sharing with
	 * me.
	 * 
	 * @attention Called only by hpcs
	 * @attention Must access table Permissions to list ONLY records that
	 * have been shared with the current hcp
	 * 
	 * @tested
	 * */
	function patient($patient_id = NULL) {
		if (DEBUG) $this->output->enable_profiler(TRUE);
		$this->auth->check_logged_in();
		$this->load->model('medical_records_model');
		$this->load->model('hcp_model');
		$this->load->model('connections_model');
		
		if ($patient_id == NULL) {
			$this->ui->set_error('No patient id specified','Missing Arguments');
			return;
		}
		
		// Current user must be an hcp
		if ($this->auth->get_type() != 'hcp') {
			$this->ui->set_error('Only HCPs have access to this funtionality.<br />
			To see your medical records go <a href="/medical_records">here</a>', 'Permission Denied');
			return;
		}
		
		// Current user must be connected with the patient
		$check = $this->connections_model->is_connected_with(
			$this->auth->get_account_id(), $patient_id
		);
		if ($check === -1){ 
			$this->ui->set_query_error(); 
			return;
		}
		else if ($check == FALSE) {
			$this->ui->set_error('You are not connected with this patient','Permission Denied');
		} else {
			// Get the list of medical records from the model
			$recs = $this->medical_records_model->get_patient_records(
				array($patient_id, $this->auth->get_account_id())
			);
			if ($recs === -1){
				$this->ui->set_query_error(); 
				return;
			} else {
				// View the list
				$this->ui->set(array(
					$this->load->view('mainpane/list_medical_records',
					array('list_name' => 'Medical Records', 'list' => $recs) , TRUE)
				));
			}
		}
	}
	
	/**
	 * Loads view that allows you to upload a medical record
	 * 
	 * @test Tested
	 * */
	function upload($pid = NULL) {
		$this->auth->check_logged_in();
		
		// Get the patient_id of the medical record to upload
		if ($this->auth->get_type() === 'patient') {
			$patient_id = $this->auth->get_account_id();
		} else {
			if ($pid == NULL || ! is_numeric($pid)) {
				$this->ui->set_error('No patient specified');
				return;
			}
			$patient_id = $pid;
		}
		
		$this->ui->set(array(
			$this->load->view('mainpane/forms/upload_medrec',
				array('patient_id' => $patient_id) , TRUE)
			));
	}
	
	/*
	 * Add the new medical record
	 * @attention should the patient approve this first before having it added to their list of records?
	 *
	function upload_do()
	{
		$this->auth->check_logged_in();
		$this->load->model('medical_records_model');

		$file = $this->input->post('filepath');
		
		// Patient adding their own file
		if ($this->auth->get_type() === 'patient') {
			$results = $this->medical_record_model->add_med_record(array(
																			'patient_id' => $this->auth->get_account_id(), 
																			'account_id' => $this->auth->get_account_id(), 
																			'file_path' => $file,
																	)); 
		}

		// hcp adds medical record of a specific patient 
		else if ($this->auth->get_type() === 'hcp') {
			$patient_id = $this->input->post('patient_id');
			$results = $this->medical_record_model->add_med_record(array(
																			'patient_id' => $this->auth->get_account_id(),
																			'account_id' => $this->auth->get_account_id(), 
																			'file_path' => $file,
																	)); 
		}
		else {		
			$this->ui->error('Internal Logic Error.', 500);
			return;
		}
		
		switch ($results) {
			case -1:
				$this->ui->set(array('Query error!',''));
				break;
			default:
				$this->ui->redirect('/medical_records/list_med_recs');
				break; 
		}	
	}*/

	/**
	 * Gets called when an individual medical record is selected to be viewed
	 * loads a view that prints Name, Expanded Info, Date, ...etc
	 * should list all hcps who have permission to see it
	 * should have a button that lets you give another hcp permission , or remove permission 
	 * should have a button to delete a medical record
	 * @todo Implement this function and then make it 'public' (remove _ at
	 * the beginning of the name)
	 * @todo: need a view that lists medical records: Name, Description, link to file
	 * *
	function _view($med_rec_id) {
		$this->auth->check_logged_in();
		$this->load->model('medical_records_model');
		
		// Check if the medical record belongs to user
		if($this->auth->get_type() === 'patient'){
			$result = $this->medical_records_model->is_myrecord(array($this->auth->get_account_id(), $med_rec_id));
		}
		// If hcp: check if he/she has permission to see it
		else if($this->auth->get_type() === 'hcp'){
			$result = $this->permissions_model->____(array($this->auth->get_account_id(), $med_rec_id)); /*@todo: update fn call*
		}
		else {
				$this->ui->error('Internal Logic Error.',500);
				return;
		}
		
		switch ($result) {
			case -1:
				$mainview = 'Query error!';
				$sideview = '';
				$error = TRUE;
				break;
			case FALSE:
				$mainview = 'You do not have permission to see this medical record.';
				$sideview = '';
				$error = TRUE;
			default:
				$error = FALSE;
				break;
		}
		
		if($error){
			$this->ui->set(array($mainview,$sideview));
			return;
		}
				
		$res = $this->medical_records_model->get_medicalrecord(array($med_rec_id));
		
		$this->ui->set(array($this->load->view('mainpane/______', $res, TRUE),''));	
	}*/
	
	/**
	 * Show a form to ask for an hcp to add permission to
	 * 
	 * @attention Accessible only to patients
	 * 
	 * @test Tested
	 * */
	function add_permission($mrec_id) {
		$this->auth->check_logged_in();
		
		$this->ui->set(array(
			$this->load->view('mainpane/forms/add_permission', array('medrec_id' => $mrec_id), TRUE)
		));
	}
	
	/**
	 * Show a form to ask for an hcp to remove permission to
	 * 
	 * @test Tested
	 * */
	function remove_permission($mrec_id) {
		$this->auth->check_logged_in();
		
		$this->ui->set(array(
			$this->load->view('mainpane/forms/remove_permission', array('medrec_id' => $mrec_id), TRUE)
		));
	}
	
	/**
	 * Displays a list of all HCPs that have access to a particular
	 * medica record
	 * 
	 * @attention only accessible to patients
	 * */
	function see_permissions($mid) {
		$this->auth->check_logged_in();
		$this->load->model('medical_records_model');
		
		if ($mid == NULL) {
			$this->ui->set_error('Missing medical record id','Missing Arguments');
			return;
		}
		
		// Only for patients
		if ($this->auth->get_type() != 'patient') {
			$this->ui->set_error('Only patients can access this functionality','Permission Denied');
			return;
		}
		
		// Get tuple for this medical record
		$get = $this->medical_records_model->get_medicalrecord(array($mid));
		if ($get === -1) {
			$this->ui->set_query_error(); 
			return;
		}
		else if (sizeof($get) == 0) {
			$this->ui->set_error('Specified medical record does not exist');
			return;
		}
		else if ($get[0]['patient_id'] != $this->auth->get_account_id()) {
			$this->ui->set_error('Only the owner can modify this medical record','Permission Denied');
			return;
		}
		
		// Try to list permissions
		$res = $this->medical_records_model->get_medrec_allowed_accounts(array($mid));
		//$this->load->model('hcp_model');
		//$res = $this->hcp_model->get_hcps(array($mid));
		
		if ($res === -1){
			$this->ui->set_query_error(); 
			return;
		}
		$this->ui->set(array(
			$this->load->view('mainpane/list_permissions',
				array('list_name' => 'Permissions for medical record '.$mid, 'list' => $res), TRUE)
			));
	}
	
	/**
	 * Set medical record to hidden: not visible to your specified hcp
	 * 
	 * @test Tested
	 * */
	function remove_permission_do($mid) {
		$this->auth->check_logged_in();
		$this->load->model('medical_records_model');
		
		if ($mid == NULL) {
			$this->ui->set_error('Missing input medical record id','Missing Arguments');
			return;
		}
		
		// the account that will lose permission
		$account_id = $this->input->post('account_id');
		if ($account_id == '' || $account_id == FALSE) {
			$this->ui->set_error('No account id specified');
			return;
		}
		
		// Get tuple for this medical record
		$get = $this->medical_records_model->get_medicalrecord(array($mid));
		if ($get === -1) {
			$this->ui->set_query_error(); 
			return;
		} else if (sizeof($get) == 0) {
			$this->ui->set_error('Specified medical record does not exist');
			return;
		} else if ($get[0]['patient_id'] != $this->auth->get_account_id()) {
			$this->ui->set_error('Only the owner can modify this medical record','Permission Denied');
			return;
		}
		
		// Try to change permission
		if ($this->auth->get_type() === 'patient') {
			// Check if its already allowed to be seen by hcp
			$isalready = $this->medical_records_model->is_account_allowed(array($account_id, $mid));
			if ($isalready === -1) {
				$this->ui->set_query_error(); 
				return;
			}
			if (! $isalready) {
				$this->ui->set_message('This record is already forbidden to this hcp.','Notice');
				return;
			}
			$res = $this->medical_records_model->delete_permission(array($mid, $account_id));
			
		} else {
			$this->ui->set_error('Only patients can modify permissions','Permission Denied');
			return;
		}
		
		if($res === -1) {
			$this->ui->set_query_error(); 
			return;
		}
		
		$this->ui->set_message('This record is now forbidden to that HCP.');
	}

	/**
	 * Add permission to see the specified medical record to the 
	 * specified hcp.
	 * 
	 * @attention Cannot add a permission to another user you are
	 * not connected with
	 * 
	 * @test Tested
	 * */
	function add_permission_do($mid = NULL) {
		$this->auth->check_logged_in();
		$this->load->model('medical_records_model');
		$this->load->model('connections_model');
		
		if ($mid == NULL) {
			$this->ui->set_error('Missing medical record id','Missing Arguments');
			return;
		}
		
		// the account that will have permission
		$account_id = $this->input->post('account_id');
		if ($account_id == '' || $account_id == FALSE) {
			$this->ui->set_error('No account id specified','Missing Arguments');
			return;
		}
		
		// You must be connected with that account first
		$conn = $this->connections_model->is_connected_with(
			$this->auth->get_account_id(), $account_id
		);
		if ($conn === -1) {
			$this->ui->set_query_error(); 
			return;
		} else if ($conn == FALSE) {
			$this->ui->set_error('You must be connected to this hcp
			to grant him/her access to your medical records!');
			return;
		}
		
		// Get tuple for this medical record
		$get = $this->medical_records_model->get_medicalrecord(array($mid));
		if ($get === -1) {
			$this->ui->set_query_error(); 
			return;
		}
		else if (sizeof($get) == 0) {
			$this->ui->set_error('Specified medical record does not exist');
			return;
		}
		else if ($get[0]['patient_id'] != $this->auth->get_account_id()) {
			$this->ui->set_error('Only the owner can modify this medical record','Permission Denied');
			return;
		}
		
		// Try to change permission
		if ($this->auth->get_type() === 'patient') {
			
			// Check if its already allowed to be seen by hcp
			$isalready = $this->medical_records_model->is_account_allowed(array($account_id, $mid));
			if ($isalready === -1) {
				$this->ui->set_query_error(); 
				return;
			}
			if ($isalready) {
				$this->ui->set_message('This record is already allowed to this hcp.','Notice');
				return;
			}
			$res = $this->medical_records_model->allow_permission(array($mid, $account_id));
			
		} else {
			$this->ui->set_error('Only patients can modify permissions','Permission Denied');
			return;
		}
		
		switch ($res) {
			case -1:
				$this->ui->set_query_error(); 
				return;
			case -2:
				$this->ui->set_error('The account specified does not exist or you
				are trying to give an authorize account access');
				return;
			default:
				$this->ui->set_message('This record is now public to that HCP.', 'Confirmation');
				return;
		}
	}

	/**
	 * Delete medical record 
	 * 
	 * @attention ONLY patient should be able to do this
	 * 
	 * @test Tested
	 * */
	function delete($medical_record_id = FALSE) {
		if (DEBUG) $this->output->enable_profiler(TRUE);
		$this->auth->check_logged_in();
		$this->load->model('medical_records_model');
		
		// Check input
		if ($medical_record_id == FALSE) {
			$this->ui->set_error('Invalid input');
			return;
		}
		
		// Only the patient can delete it
		$get = $this->medical_records_model->get_medicalrecord($medical_record_id);
		
		if ($get === -1) {
			$this->ui->set_query_error(); 
			return;
		} else if (sizeof($get) == 0) {
			$error = 'Medical record does not exist';
		} else if ($get[0]['patient_id'] != $this->auth->get_account_id()) {
			$error = 'You don\'t have permissions to delete this record.';
			$type = 'Permission Denied';
		} else {
			$filename = $get[0]['file_path'];
			
			$del = $this->medical_records_model->delete_medical_record(array($medical_record_id));
			
			if ($del === -1) {
				$this->ui->set_query_error(); 
				return;
			} else {
				// Delete file from file system
				$filepath = 'resources/medical_records/'
					.$this->auth->get_account_id().'/'.$filename;
				if (! is_file($filepath)) {
					$error = 'Medical record entry deleted, but file does not exist!';
				}
				else if (! unlink($filepath)) {
					$error = 'Medical record entry deleted, but file still in the server!';
					$type = 'Notice';
				} else {
					$this->ui->set_message('Medical record deleted successfully!','Confirmation');
					return;
				}
			}
		}
		$this->ui->set_error($error,$type);
	}
}
/** @} */
?>

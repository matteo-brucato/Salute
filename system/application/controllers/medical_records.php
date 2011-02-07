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
		$this->load->library('ajax');
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
		//$this->ajax->redirect('/medical_records/list_med_recs');
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
		$this->output->enable_profiler(TRUE);
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
			$this->ajax->redirect('/connections/mypatients');
			return;
		} else {
			show_error('Server Error.', 500);
			return;
		}
		
		switch ($res) {
			case -1:
				$mainview = 'Query error!';
				$sideview = '';
				break;
			default:
				$mainview = $this->load->view('mainpane/list_medical_records',
					array('list_name' => 'My Medical Records', 'list' => $res) , TRUE);
				$sideview = '';
				break;
		}
		
		$this->ajax->view(array($mainview, $sideview));
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
		$this->output->enable_profiler(TRUE);
		$this->auth->check_logged_in();
		$this->load->model('medical_records_model');
		$this->load->model('hcp_model');
		$this->load->model('connections_model');
		
		if ($patient_id == NULL) {
			show_error('No patient id specified');
			return;
		}
		
		// Current user must be an hcp
		if ($this->auth->get_type() != 'hcp') {
			show_error('Only HCPs have access to this funtionality.<br />
			To see your medical records go <a href="/medical_records">here</a>');
			return;
		}
		
		// Current user must be connected with the patient
		$check = $this->connections_model->is_connected_with(
			$this->auth->get_account_id(), $patient_id
		);
		if ($check === -1) {
			$mainview = 'Query error';
			$sideview = '';
		}
		else if ($check == FALSE) {
			$mainview = 'You are not connected with this patient';
			$sideview = '';
		} else {
			// Get the list of medical records from the model
			$recs = $this->medical_records_model->get_patient_records(
				array($patient_id, $this->auth->get_account_id())
			);
			if ($recs === -1) {
				$mainview = 'Query error';
				$sideview = '';
				return;
			} else {
				$mainview = $this->load->view('mainpane/list_medical_records',
					array('list_name' => 'Medical Records', 'list' => $recs) , TRUE);
				$sideview = '';
			}
		}
		
		// View the list
		$this->ajax->view(array($mainview, $sideview));
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
			$sideview = $this->load->view('sidepane/patient-profile', '' , TRUE);
			$patient_id = $this->auth->get_account_id();
		} else {
			$sideview = $this->load->view('sidepane/hcp-profile', '' , TRUE);
			if ($pid == NULL || ! is_numeric($pid)) {
				show_error('No patient specified');
				return;
			}
			$patient_id = $pid;
		}
		
		$this->ajax->view(array(
			$this->load->view('mainpane/forms/upload_medrec',
				array('patient_id' => $patient_id) , TRUE),
			$sideview
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
			show_error('Internal Logic Error.', 500);
			return;
		}
		
		switch ($results) {
			case -1:
				$this->ajax->view(array('Query error!',''));
				break;
			default:
				$this->ajax->redirect('/medical_records/list_med_recs');
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
				show_error('Internal Logic Error.',500);
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
			$this->ajax->view(array($mainview,$sideview));
			return;
		}
				
		$res = $this->medical_records_model->get_medicalrecord(array($med_rec_id));
		
		$this->ajax->view(array($this->load->view('mainpane/______', $res, TRUE),''));	
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
		
		$this->ajax->view(array(
			$this->load->view('mainpane/forms/add_permission', array('medrec_id' => $mrec_id), TRUE),
			''
		));
	}
	
	/**
	 * Show a form to ask for an hcp to remove permission to
	 * 
	 * @test Tested
	 * */
	function remove_permission($mrec_id) {
		$this->auth->check_logged_in();
		
		$this->ajax->view(array(
			$this->load->view('mainpane/forms/remove_permission', array('medrec_id' => $mrec_id), TRUE),
			''
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
			$this->ajax->view(array('Missing input: medical record id',''));
			return;
		}
		
		// Only for patients
		if ($this->auth->get_type() != 'patient') {
			$this->ajax->view(array('Only patients can access this functionality',''));
			return;
		}
		
		// Get tuple for this medical record
		$get = $this->medical_records_model->get_medicalrecord(array($mid));
		if ($get === -1) {
			$this->ajax->view(array('Query error',''));
			return;
		}
		else if (sizeof($get) == 0) {
			$this->ajax->view(array('Medical record specified does not exist',''));
			return;
		}
		else if ($get[0]['patient_id'] != $this->auth->get_account_id()) {
			$this->ajax->view(array('Only the owner can modify this medical record',''));
			return;
		}
		
		// Try to list permissions
		$res = $this->medical_records_model->get_medrec_allowed_accounts(array($mid));
		//$this->load->model('hcp_model');
		//$res = $this->hcp_model->get_hcps(array($mid));
		
		if ($res === -1) {
			$this->ajax->view(array('Query error',''));
			return;
		}
		
		$this->ajax->view(array(
			$this->load->view('mainpane/list_permissions',
				array('list_name' => 'Permissions for medical record '.$mid, 'list' => $res), TRUE),
			$this->load->view('sidepane/patient-profile', '', TRUE)
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
			$this->ajax->view(array('Missing input: medical record id',''));
			return;
		}
		
		// the account that will lose permission
		$account_id = $this->input->post('account_id');
		if ($account_id == '' || $account_id == FALSE) {
			$this->ajax->view(array('No account id specified',''));
			return;
		}
		
		// Get tuple for this medical record
		$get = $this->medical_records_model->get_medicalrecord(array($mid));
		if ($get === -1) {
			$this->ajax->view(array('Query error',''));
			return;
		}
		else if (sizeof($get) == 0) {
			$this->ajax->view(array('Medical record specified does not exist',''));
			return;
		}
		else if ($get[0]['patient_id'] != $this->auth->get_account_id()) {
			$this->ajax->view(array('Only the owner can modify this medical record',''));
			return;
		}
		
		// Try to change permission
		if ($this->auth->get_type() === 'patient') {
			
			// Check if its already allowed to be seen by hcp
			$isalready = $this->medical_records_model->is_account_allowed(array($account_id, $mid));
			if ($isalready === -1) {
				$this->ajax->view(array('Query error',''));
				return;
			}
			if (! $isalready) {
				$this->ajax->view(array('This record is already forbidden to this hcp.',''));
				return;
			}
			$res = $this->medical_records_model->delete_permission(array($mid, $account_id));
			
		} else {
			$this->ajax->view(array('Only patients can modify permissions',''));
			return;
		}
		
		switch ($res) {
			case -1:
				$mainview = 'Query error!';
				$sideview = '';
				break;
			default:
				$mainview = 'This record is now forbidden to that HCP.';
				$sideview = '';
				break;
		}
		
		$this->ajax->view(array($mainview, $sideview));
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
			$this->ajax->view(array('Missing input: medical record id',''));
			return;
		}
		
		// the account that will have permission
		$account_id = $this->input->post('account_id');
		if ($account_id == '' || $account_id == FALSE) {
			$this->ajax->view(array('No account id specified',''));
			return;
		}
		
		// You must be connected with that account first
		$conn = $this->connections_model->is_connected_with(
			$this->auth->get_account_id(), $account_id
		);
		if ($conn === -1) {
			$this->ajax->view(array('Query error',''));
			return;
		}
		else if ($conn == FALSE) {
			$this->ajax->view(array('You must be connected to this account
			to grant it access to your medical records!',''));
			return;
		}
		
		// Get tuple for this medical record
		$get = $this->medical_records_model->get_medicalrecord(array($mid));
		if ($get === -1) {
			$this->ajax->view(array('Query error',''));
			return;
		}
		else if (sizeof($get) == 0) {
			$this->ajax->view(array('Medical record specified does not exist',''));
			return;
		}
		else if ($get[0]['patient_id'] != $this->auth->get_account_id()) {
			$this->ajax->view(array('Only the owner can modify this medical record',''));
			return;
		}
		
		// Try to change permission
		if ($this->auth->get_type() === 'patient') {
			
			// Check if its already allowed to be seen by hcp
			$isalready = $this->medical_records_model->is_account_allowed(array($account_id, $mid));
			if ($isalready === -1) {
				$this->ajax->view(array('Query error',''));
				return;
			}
			if ($isalready) {
				$this->ajax->view(array('This record is already allowed to this hcp.',''));
				return;
			}
			$res = $this->medical_records_model->allow_permission(array($mid, $account_id));
			
		} else {
			$this->ajax->view(array('Only patients can modify permissions',''));
			return;
		}
		
		switch ($res) {
			case -1:
				$mainview = 'Query error!';
				$sideview = '';
				break;
			case -2:
				$mainview = 'The account specified does not exist or you
				are trying to give an authorize account access';
				$sideview = '';
				break;
			default:
				$mainview = 'This record is now public to that HCP.';
				$sideview = '';
				break;
		}
		
		$this->ajax->view(array($mainview,$sideview));
	}

	/**
	 * Delete medical record 
	 * 
	 * @attention ONLY patient should be able to do this
	 * 
	 * @test Tested
	 * */
	function delete($medical_record_id = FALSE) {
		$this->output->enable_profiler(TRUE);
		$this->auth->check_logged_in();
		$this->load->model('medical_records_model');
		
		// Check input
		if ($medical_record_id == FALSE) {
			show_error('Invalid input');
			return;
		}
		
		// Only the patient can delete it
		$get = $this->medical_records_model->get_medicalrecord($medical_record_id);
		
		if ($get === -1) {
			$mainview = 'Query error!';
		}
		else if (sizeof($get) == 0) {
			$mainview = 'Medical record does not exist';
		}
		else if ($get[0]['patient_id'] != $this->auth->get_account_id()) {
			$mainview = 'You don\'t have permissions to delete this record.';
		}
		else {
			$filename = $get[0]['file_path'];
			
			$del = $this->medical_records_model->delete_medical_record(array($medical_record_id));
			
			if ($del === -1) {
				$mainview = 'Query error, cannot delete medical record entry!';
			} else {
				// Delete file from file system
				$filepath = 'resources/medical_records/'
					.$this->auth->get_account_id().'/'.$filename;
				if (! is_file($filepath)) {
					$mainview = 'Medical record entry deleted, but file does not exist!';
				}
				else if (! unlink($filepath)) {
					$mainview = 'Medical record entry deleted, but file still in the server!';
				} else {
					$mainview = 'Medical record deleted successfully!';
				}
			}
		}
		
		$this->ajax->view(array($mainview, ''));
	}
}
/** @} */
?>

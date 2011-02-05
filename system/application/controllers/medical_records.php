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
					array('list_name' => 'Medical Records', 'list' => $res) , TRUE);
				$sideview = '';
				break;
		}
		
		$this->ajax->view(array($mainview,$sideview));
	}
	
	/**
	 * Shows all medical records that a certain patient is sharing with
	 * me.
	 * 
	 * @attention Called only by hpcs
	 * @attention Must access table Permissions to list ONLY records that
	 * have been shared with the current hcp
	 * */
	function patient($patient_id) {
		$this->output->enable_profiler(TRUE);
		$this->auth->check_logged_in();
		$this->load->model('medical_records_model');
	}
	
	/**
	 * Loads view that allows you to upload a medical record
	 * @todo need a view
	 * */
	function upload() {
		$this->auth->check_logged_in();
		
		if ($this->auth->get_type() === 'patient') {
			$sideview = $this->load->view('sidepane/patient-profile', '' , TRUE);
		} else {
			$sideview = $this->load->view('sidepane/hcp-profile', '' , TRUE);
		}
		
		$this->ajax->view(array(
			$this->load->view('mainpane/forms/upload_medrec',
				array('patient_id' => $this->auth->get_account_id()) , TRUE),
			$sideview
		));
	}
	
	/**
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
	 * */
	function _view($med_rec_id) {
		$this->auth->check_logged_in();
		$this->load->model('medical_records_model');
		
		// Check if the medical record belongs to user
		if($this->auth->get_type() === 'patient'){
			$result = $this->medical_records_model->is_myrecord(array($this->auth->get_account_id(), $med_rec_id));
		}
		// If hcp: check if he/she has permission to see it
		else if($this->auth->get_type() === 'hcp'){
			$result = $this->permissions_model->____(array($this->auth->get_account_id(), $med_rec_id)); /*@todo: update fn call*/
		}
		else{
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
	}

	/**
	 * Set medical record to hidden: not visible to your specified hcp
	 * */
	function set_private($medical_record_id, $hcp_id) {
		$this->auth->check_logged_in();
		$this->load->model('medical_records_model');
		$this->load->model('permissions_model');
		
		// Check if the medical record belongs to user
		if($this->auth->get_type() === 'patient'){
			$result = $this->medical_records_model->is_myrecord(array($this->auth->get_account_id(), $med_record_id));
			if(!$result){
				show_error('This is not your record. Permission Denied.',500);
				return;
			} else {
				// Check if its already hidden from hcp
				$result = $this->permissions_model->get_permission_status($hcp_id,$medical_record_id);
				if(!$result){
					show_error('This record is already private from that hcp.',500);
					return;					
				}
				$res = $this->permissions_model->delete_permission($hcp_id,$medical_record_id);
			}
		}
		// If hcp: check if he/she has permission to see it
		else if($this->auth->get_type() === 'hcp'){
				show_error('Permission Denied.',500);
				return;
		}
		else{
				show_error('Internal Logic Error.',500);
				return;
		}
		switch ($res) {
			case -1:
				$mainview = 'Query error!';
				$sideview = '';
				$error = TRUE;
				break;
			default:
				$mainview = 'This record is now private to that hcp.';
				$sideview = '';
				break;
		}
		
		$this->ajax->view(array($mainview,$sideview));
	}

	/**
	 * Set medical record to viewable: public to your specified hcp
	 * */
	function set_public($medical_record_id, $hcp_id) {
		$this->auth->check_logged_in();
		$this->load->model('medical_records_model');
		$this->load->model('permissions_model');
		
		// Check if the medical record belongs to user
		if($this->auth->get_type() === 'patient'){
			$result = $this->medical_records_model->is_myrecord(array($this->auth->get_account_id(), $med_record_id));
			if(!$result){
				show_error('This is not your record. Permission Denied.',500);
				return;
			} else {
				// Check if its already allowed to be seen by hcp
				$result = $this->permissions_model->get_permission_status($hcp_id,$medical_record_id);
				if($result){
					show_error('This record is already public to this hcp.',500);
					return;					
				}
				$res = $this->permissions_model->allow_permission($hcp_id,$medical_record_id);
			}
		}
		// If hcp: check if he/she has permission to see it
		else if($this->auth->get_type() === 'hcp'){
				show_error('Permission Denied.',500);
				return;
		}
		else{
				show_error('Internal Logic Error.',500);
				return;
		}
		switch ($res) {
			case -1:
				$mainview = 'Query error!';
				$sideview = '';
				$error = TRUE;
				break;
			default:
				$mainview = 'This record is now public to that hcp.';
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

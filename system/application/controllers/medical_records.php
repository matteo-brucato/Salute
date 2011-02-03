<?php
/**
 * @file medical_records.php
 * @brief Controller to handle medical records
 *
 * @defgroup ctr Controllers
 * @ingroup ctr
 * @{
 */

class MedicalRecords extends Controller {

	function __construct(){
		parent::Controller();
		$this->load->library('ajax');
		$this->load->library('auth');	
		$this->type = $this->session->userdata('type');	
		$this->account_id = $this->session->userdata('account_id');	
	}

	/*
	 * Default function call
	 * @return  redirects to list_my_med_recs function
	 */ 
	function index()
	{
		//$this->auth->check_logged_in();
		//$this->ajax->redirect('/medical_records/list_med_recs');
		$this->list_med_recs();
	}

	/*
	 * lists all medical records
	 * 
	 * @return
	 *		if patient-list your records
	 * 		if hcp, redirect to list of his/her patients
	*/
	function list_med_recs()
	{
		$this->auth->check_logged_in();
		$this->load->model('medical_records_model');

		// if patient, show all their med recs
		if ($this->auth->get_type() === 'patient') {
			$res = $this->medical_record->list_my_records(array('account_id' => $this->auth->get_account_id() )); 
		}
		// if hcp, redirect to My Patients search List
		else if ( $this->auth->get_type() === 'hcp') {
			$this->ajax->redirect('/search/patient');
			return;
		} else {
			show_error('Unknown Error.', 500);
			return;
		}
		
		switch ($res) {
			case -1:
				$mainview = 'Query error!';
				$sideview = '';
				break;
			case -2:
				$mainview = 'You have no medical records.';
				$sideview = '';
				break;
			default:
				$mainview = $this->load->view('mainpane/myrecords', array('med_rec_list' => $res) , TRUE);
				$sideview = '';
				break;
		}
		
		$this->ajax->view(array($mainview,$sideview));		
	}

	/*
	 * Loads view that allows you to upload a medical record
	 * @todo need a view
	 * */
	function upload(){
		$this->auth->check_logged_in();

		$this->ajax->view(array(
								$this->load->view('mainpane/_____', '' , TRUE),
								''
						));	
		}
	}
	/*
	 * Add the new medical record
	 * @attention should the patient approve this first before having it added to their list of records?
	 */ 
	function add()
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
	}

	// gets called when an individual medical record is selected to be viewed
	// loads a view that prints Name, Expanded Info, Date, ...etc
	// should list all hcps who have permission to see it
	// should have a button that lets you give another hcp permission , or remove permission 
	// should have a button to delete a medical record
	/* @todo: need a view that lists medical records: Name, Description, link to file */
	function medical_record($med_rec_id) {
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

	// Set medical record to hidden: not public to your hcp(s)
	function set_private($medical_record_id,$hcp_id) {
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

	// Set medical record to viewable: public to your hcp(s)
	function set_public($medical_record_id, ,$hcp_id) {
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

	// Delete medical record 
	// ONLY patient should be able to do this
	// should be able to delete multiple recs at once
	/* @todo: waiting is_myrecord model function*/
	function destroy($medical_record_id, ,$hcp_id) {
		$this->auth->check_logged_in();
		$this->load->model('medical_records_model');
		if( $this->medical_records_model->is_myrecord(array($this->auth->get_account_id(),$medical_record_id)) ){
			$this->medical_records_model->delete_medical_record($medical_record_id);		
		}
 		else{
			show_error('This is not your record.', 500);
			return;
		}
		$this->ajax->redirect('/medical_records/list_med_recs');
	}
}
/** @} */
?>

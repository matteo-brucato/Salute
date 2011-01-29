<?php
class MedicalRecords extends Controller {

	function __construct(){
		parent::Controller();
		$this->load->library('ajax');
		$this->load->library('auth');	
		$this->type = $this->session->userdata('type');	
		$this->account_id = $this->session->userdata('account_id');	
	}

	// Default: call list_my_med_recs function
	function index()
	{
		$this->auth->check_logged_in();
		$this->ajax->redirect('/medical_records/list_med_recs');
	}

	// list all medical records
	function list_med_recs()
	{
		$this->auth->check_logged_in();
		$this->load->model('medical_records_model');

		// if patient, show all their med recs
		if ($this->auth->get_type() === 'patient') {
			$results = $this->medical_record->list_my_records(array('account_id' => $this->auth->get_account_id() )); 
			$this->ajax->view(array(
					'',
					$this->load->view('mainpane/list_patient_recs', $results , TRUE)
				));
		}
	
		// if doctor, redirect to My Patients search List
		else if ( $this->auth->get_type() === 'doctor') {
			$this->ajax->redirect('/search/patient');
		}
		else{
			show_error('Unknown Error.', 500);
			return;
		}		
	}

	// Add/Upload new medical record
	function add()
	{
		$this->auth->check_logged_in();
		$this->load->model('medical_records_model');

		// Case 1: Patient add their own file
		if ($this->auth->get_type() === 'patient') {

			/* TODO: Need a view for file uploads */
			// TODO: How to upload a file from a user's computer?
			// expects file on return... 
			$file = $this->ajax->view(array(
						$this->load->view('mainpane/upload', '' , TRUE),
						''
			));	
			
			/*TODO: fix model: when patient uploads their own file....don't need doc_id */
			$results = $this->medical_record_model->add_med_record(array(
										'patient_id' => $this->auth->get_account_id(), 
										'account_id' => $this->auth->get_account_id(), 
										'file_path' => $file,
			)); 
		}

		// Case 2: Doctor adds medical record of a specific patient 
		// 		Note: should the patient approve this first before having it added to their list of records?
		// DEFAULT permission: set_hidden

		else if ($this->auth->get_type() === 'doctor') {

			/* TODO: Need a view for file uploads */
			// TODO: How to upload a file from a user's computer?
			// expects file on return... 
			$file = $this->ajax->view(array(
						$this->load->view('mainpane/upload', '' , TRUE),
						''
			));	
			
			/*TODO: fix model: when patient uploads their own file....don't need doc_id */
			$results = $this->medical_record_model->add_med_record(array(
										'patient_id' => $this->auth->get_account_id(),
										'account_id' => $this->auth->get_account_id(), 
										'file_path' => $file,
			)); 
		}
		else {		
			show_error('Unknown Error.', 500);
			return;}
	}

	// gets called when an individual medical record is selected to be viewed
	// loads a view that prints Name, Expanded Info, Date, ...etc
	// should list all doctors who have permission to see it
	// should have a button that lets you give another doctor permission , or remove permission 
	// should have a button to delete a medical record
	function medical_record() {
		$this->auth->check_logged_in();
	}

	// Set medical record to hidden: not public to your doctor(s)
	// take in medical record id as a param
	// should be able to set multiple recs to hidden at once
	function set_hidden() {
		$this->auth->check_logged_in();
	}

	// Set medical record to viewable: public to your doctor(s)
	// take in medical record id as a param
	// should be able to set multiple recs to allowed at once
	function set_allowed() {
		$this->auth->check_logged_in();	
	}

	// Delete medical record 
	// ONLY patient should be able to do this
	// take in medical record id as a param
	// should be able to delete multiple recs at once
	function destroy() {
		$this->auth->check_logged_in();
	}

}
?>

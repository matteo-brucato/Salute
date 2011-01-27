<?php
class MedicalRecords extends Controller {

	private $type;
	private $account_id;

	function __construct(){
		parent::Controller();
		$this->load->library('ajax');	
		$this->type = $this->session->userdata('type');	
		$this->account_id = $this->session->userdata('account_id');	
	}

	// Default: call list_my_med_recs function
	function index()
	{
		$this->ajax->redirect('/medical_records/list_med_recs');
	}

	// list all medical records
	function list_med_recs()
	{

		$this->load->model('medical_record');

		// if patient, show all their med recs
		if ($this->type === 'patient') {
			$results = $this->medical_record->list_all(array('account_id' => $this->account_id,'type' => $this->type)); 
			$this->ajax->view(array(
					'',
					$this->load->view('mainpane/list_patient_recs', $results , TRUE)
				));
		}
	
		// if doctor, redirect to My Patients search List
		else if ( $this->type === 'doctor') {
			$this->ajax->redirect('/search/patient');
		}

		// you are not logged in, redirect to login page
		else{
			$this->session->sess_destroy(); // just in case something fishy was going on.
			$this->ajax->redirect('/');
		}		
	}

	// Add/Upload new medical record
	function add()
	{
		// Case 1: Patient add their own file
		// Case 2: Doctor adds medical record of a specific patient 
		// 		Note: should the patient approve this first before having it added to their list of records?
		// DEFAULT permission: set_hidden
	}

	// gets called when an individual medical record is selected to be viewed
	// loads a view that prints Name, Expanded Info, Date, ...etc
	// should list all doctors who have permission to see it
	// should have a button that lets you give another doctor permission , or remove permission 
	// should have a button to delete a medical record
	function medical_record()
	{}

	// Set medical record to hidden: not public to your doctor(s)
	// take in medical record id as a param
	// should be able to set multiple recs to hidden at once
	function set_hidden()
	{}

	// Set medical record to viewable: public to your doctor(s)
	// take in medical record id as a param
	// should be able to set multiple recs to allowed at once
	function set_allowed()
	{}

	// Delete medical record 
	// ONLY patient should be able to do this
	// take in medical record id as a param
	// should be able to delete multiple recs at once
	function destroy()
	{}

}
?>

<?php
class MedicalRecords extends Controller {

	function __constructor(){
		parent::Controller();
		$this->load->library('ajax');	
		//check if you're logged in	
	}
	// Default: call list_my_med_recs function
	function index()
	{}

	// list all my medical records
	// List Format: Date, Name, Category, Permissions Link
	function list_my_med_recs()
	{}

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

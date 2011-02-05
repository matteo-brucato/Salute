<?php
/**
 * @file download.php
 * @brief Controller to downlad resources from the server
 *
 * @defgroup ctr Controllers
 * @ingroup ctr
 * @{
 */

class Download extends Controller {

	function __construct(){
		parent::Controller();
		$this->load->library('ajax');
		$this->load->library('auth');
		$this->load->helper('download');
	}

	/*
	 * Default function call
	 */ 
	function index()
	{
		//$this->auth->check_logged_in();
		//$this->ajax->redirect('/medical_records/list_med_recs');
		//$this->list_med_recs();
	}
	
	function medical_record($patient_id, $filename) {
		// Check if I have permission to download the document
		if (! $patient_id == $this->auth->get_account_id()) {
			// Check if I'm an HCP connected with this patient
			if (! true) {
				show_error('You don\'t have permission to download this medical record');
			}
		}
		// Read the file's contents
		$data = file_get_contents('resources/medical_records/'.$patient_id.'/'.$filename);
		force_download($filename, $data);
	}
}
?>

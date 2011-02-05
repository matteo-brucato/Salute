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
	
	function medical_record($type, $filename) {
		// Read the file's contents
		$data = file_get_contents('/resources/'.$type.'/'.$filename);
		force_download($filename, $data);
	}
}
?>

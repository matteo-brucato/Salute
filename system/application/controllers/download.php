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
		$this->load->library('ui');
		$this->load->library('auth');
		$this->load->helper('download');
		$this->load->model('connections_model');
		$this->load->model('medical_records_model');
	}

	/**
	 * Default function call
	 */ 
	function index()
	{
		$this->ui->set_error('Access denied','forbidden');
	}
	
	/**
	 * Download a medical record
	 * 
	 * @attention Only the patient him/herself or an hcp connected with
	 * him/her can download a medical record
	 * */
	function medical_record($patient_id = NULL, $medical_record_id = NULL) {
		
		$check = $this->auth->check(array(
			auth::CurrLOG));
			
		if ($check !== TRUE) return;
				
		// Check if there is input
		if ($patient_id == NULL || $medical_record_id == NULL) {
			$this->ui->set_error('It\'s necessary to specify a patient and a medical record id','Missing Arguments');
			return;
		}
		
		// Check if I am the patient_id of the file to download
		if ($patient_id !== $this->auth->get_account_id()) {
			// Check if I'm an HCP connected with this patient
			if (! $this->connections_model->is_connected_with($patient_id, $this->auth->get_account_id())) {
				$this->ui->set_error('You are not connected with this patient','Permission Denied');
				return;
			}
			
			// And also check if I have permissions to see this record
			$perm = $this->medical_records_model->is_account_allowed(
				array($this->auth->get_account_id(), $medical_record_id));
			if ($perm === -1) {
				$this->ui->set_query_error();
				return;
			}
			else if ($perm == FALSE) {
				$this->ui->set_error('You don\'t have permissions to download this medical record!','Permission Denied');
				return;
			}
		}
		
		// Get tuple for this medical record
		$get = $this->medical_records_model->get_medicalrecord(array($medical_record_id));
		if ($get === -1) {
			$this->ui->set_query_error();
			return;
		}
		else if (sizeof($get) == 0) {
			$this->ui->set_error('Specified medical record does not exist');
		}
		
		$filepath = 'resources/medical_records/'.$patient_id.'/'.$get[0]['file_name'];
		echo $filepath;
		// Check if the file exist
		if (! is_file($filepath)) {
			$this->ui->set_error('File does not exist!');
			return;
		}
		
		// Read the file's contents and download it
		$data = file_get_contents($filepath);
		force_download($filepath, $data);
	}
}
/**@}*/
?>

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
	}

	/**
	 * Default function call
	 */ 
	function index()
	{
		$this->ui->error('Access denied');
	}
	
	/**
	 * Download a medical record
	 * 
	 * @attention Only the patient him/herself or an hcp connected with
	 * him/her can download a medical record
	 * */
	function medical_record($patient_id = NULL, $medical_record_id = NULL) {
		$this->auth->check_logged_in();
		$this->load->model('connections_model');
		$this->load->model('medical_records_model');
		
		// Check if there is input
		if ($patient_id == NULL || $medical_record_id == NULL) {
			$this->ui->error('It\'s necessary to specify a patient and a medical record id');
			return;
		}
		
		// Check if I am the patient_id of the file to download
		if ($patient_id !== $this->auth->get_account_id()) {
			// Check if I'm an HCP connected with this patient
			if (! $this->connections_model->is_connected_with($patient_id, $this->auth->get_account_id())) {
				$this->ui->error('You are not connected with this patient');
				return;
			}
			
			// And also check if I have permissions to see this record
			$perm = $this->medical_records_model->is_account_allowed(
				array($this->auth->get_account_id(), $medical_record_id));
			if ($perm === -1) {
				$this->ui->error('Query error!');
				return;
			}
			else if ($perm == FALSE) {
				$this->ui->error('You don\'t have permissions to download this medical record!');
				return;
			}
		}
		
		// Get tuple for this medical record
		$get = $this->medical_records_model->get_medicalrecord(array($medical_record_id));
		if ($get === -1) {
			$this->ui->error('Query error!');
			return;
		}
		else if (sizeof($get) == 0) {
			$this->ui->error('Medical record specified does not exist');
		}
		
		$filepath = 'resources/medical_records/'.$patient_id.'/'.$get[0]['file_path'];
		
		// Check if the file exist
		if (! is_file($filepath)) {
			$this->ui->error('File does not exist!');
			return;
		}
		
		// Read the file's contents and download it
		$data = file_get_contents($filepath);
		force_download($filepath, $data);
	}
}
/**@}*/
?>

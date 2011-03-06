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
	 * @attention Only the patient him/herself or an account connected with
	 * him/her can download a medical record
	 * */
	function medical_record($pid = NULL, $mid = NULL) {
		$check = $this->auth->check(array(
			auth::CurrLOG,
			auth::CurrIS_or_CONN, $pid	// current must be either the patient $pid or connected with $pid
		));
		if ($check !== TRUE) return;
		
		// If patient $pid is not me, I must have a permission
		if ($pid !== $this->auth->get_account_id()) {
			$perm = $this->medical_records_model->is_account_allowed(
				array($mid, $this->auth->get_account_id()));
			if ($perm === -1) {
				$this->ui->set_query_error();
				return;
			}
			else if ($perm !== TRUE) {
				$this->ui->set_error('You don\'t have permissions to download this medical record!','Permission Denied');
				return;
			}
		}
		
		// Get tuple for this medical record
		$get = $this->medical_records_model->get_medicalrecord(array($mid));
		if ($get === -1) {
			$this->ui->set_query_error();
			return;
		}
		else if (sizeof($get) <= 0) {
			$this->ui->set_error('Specified medical record does not exist');
			return;
		}
		
		$filepath = 'resources/medical_records/'.$pid.'/'.$get[0]['file_name'];
		//echo $filepath;
		// Check if the file exist
		if (! is_file($filepath)) {
			$this->ui->set_error('File does not exist!');
			return;
		}
		
		// Read the file's contents and download it
		$data = file_get_contents($filepath);
		$this->ui->disable();
		header('Content-type: application/pdf');
		header('Content-Disposition: attachment; filename="'.$get[0]['file_name'].'"');
		echo $data;
		//force_download($filepath, $data);
	}
	
	function account_picture($aid = NULL) {
		$check = $this->auth->check(array(
			auth::CurrLOG,
			auth::ACCOUNT, $aid // $aid must refer to an account
		));
		if ($check !== TRUE) return;
		
		$filepath = 'resources/images/account_pictures/'.$aid.'.jpg';
		
		// Select default image if file does not exist
		if ((! is_file($filepath)) || ($this->auth->check(array(auth::CurrIS_or_CONN, $aid)) !== TRUE)) {
			if ($this->auth->check(array(auth::PAT, $aid)) === TRUE) {
				$type = 'patient';
			} else {
				$type = 'hcp';
			}
			$filepath = 'resources/images/account_pictures/default_'.$type.'.jpg';
		}
		
		$data = file_get_contents($filepath);
		$this->ui->disable();
		header('Content-type: image/jpeg');
		header('Cache-Control: no-cache');
		echo $data;
	}
}
/**@}*/
?>

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
/**
 * @file download.php
 * @brief Controller to downlad resources from the server
 *
 * @defgroup ctr Controllers
 * @ingroup ctr
 * @{
 */

class Upload extends Controller {

	function __construct(){
		parent::Controller();
		$this->load->library('ui');
		$this->load->library('auth');
		$this->load->helper('download');
		$this->load->model('patient_model');
		$this->load->model('connections_model');
	}

	/*
	 * Default function call
	 */ 
	function index()
	{
		//$this->ui->set_redirect('/medical_records/upload');
		$this->ui->set_error('Access denied','Forbidden');
	}
	
	/**
	 * Uploads a medical record into /resources/medical_records/<patient_id>
	 * and adds a tuple for that file into the database
	 * 
	 * @attention Only the patient him/herself or a connected hcp 
	 * can upload a file
	 * */
	function medical_record($pid = NULL) {
		if ($this->auth->check(array(
			auth::CurrLOG,				// current must be logged in
			auth::PAT, $pid,			// $pid must refer to a patient
			auth::CurrIS_or_CONN, $pid	// current must be either the patient $pid or connected with $pid
		)) !== TRUE) return;
		
		// Get POST vars
		$issue = $this->input->post('issue');
		$info = $this->input->post('info');
		$description = $this->input->post('description');
		if ($issue == '' || $issue == FALSE || $description == FALSE || $description == '') {
			$this->ui->set_error('Please, specify Issue and Description','Missing Arguments');
			return;
		}
		
		// Set the new file options
		$config['upload_path'] = './resources/medical_records/'.$pid.'/';
		$config['allowed_types'] = 'pdf';
		$config['max_size']	= '2000';
		//$config['max_width']  = '1024';
		//$config['max_height']  = '768';
		
		// Check if the patient's folder already exists
		if (! is_dir($config['upload_path'])) {
			if (mkdir($config['upload_path'], 0777) == FALSE) {
				$this->ui->set_error('Unable to upload. Unable to create new folder');
				return;
			}
		}
		
		// Load upload library
		$this->load->library('upload', $config);
		
		// Try to upload the file
		if (! $this->upload->do_upload()) {
			//$error = array('error' => $this->upload->display_errors());
			//$mainview = $this->load->view('upload_form', $error);
			$this->ui->set_error($this->upload->display_errors());
			return;
		}
		
		// File uploaded!
		$data = $this->upload->data();
		
		// Start db transaction
		$this->db->trans_start();
		
		// Now, track this record into the DB (this automatically allow permission to
		$this->load->model('medical_records_model');
		$mid = $this->medical_records_model->add_medical_record(
			array($pid, $this->auth->get_account_id(), $issue, $info, $description, $data['file_name'])
		);
		if ($mid === -1) {
			$this->ui->set_error('Error recording the uploaded file in the database', 'Database Error');
			/** @bug This can yield to untracked uploaded files in the server
			 * You should simply remove the file from the server
			 *  */
			return;
		}
		if ($mid === -2) {
			$this->ui->set_error('Error retrieving last inserted medical record', 'Database Error');
			return;
		}
		if ($mid === NULL) {
			$this->ui->set_error('Did not insert anything in the db','Database Error');
			return;
		}
		
		// Give the person that added it permission to view the file
		if ($this->auth->get_type() !== 'patient') {
			$res = $this->medical_records_model->allow_permission(array($mid, $this->auth->get_account_id()));
			if ($res === -1) {
				$this->ui->set_query_error();
				return;
			}
			else if ($res === -2) {
				$this->ui->set_error('Error recording permission in the database for the uploaded file
				<br />However, the file was succesfully uploaded', 'Database error');
				return;
			}
		}
		
		// Now, add permission for this file, to every doctor with high level
		$result = $this->medical_records_model->add_permissions_to_high_levels($mid, $pid);
		if ($result === -1) {
			$this->ui->set_error('Medical record has been successfully uploaded,
			but permissions were not granted to high level connections');
			return;
		}
		
		$this->db->trans_complete();
		$this->ui->set(array('Medical record uploaded successfully!'));
	}
}
/**@}*/
?>

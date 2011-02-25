<?php
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
	function medical_record($patient_id = FALSE) {
		if (DEBUG) $this->output->enable_profiler(TRUE);
		$this->auth->check_logged_in();
		$this->load->model('patient_model');
		$this->load->model('connections_model');
		
		// Get the patient_id
		//$patient_id = $this->input->post('patient_id');
		$issue = $this->input->post('issue');
		$info = $this->input->post('info');
		if ($patient_id == FALSE || $issue == '' || $issue == FALSE) {
			$this->ui->set_error('Unable to upload.','Missing Arguments');
		}
		
		// Check if $patient_id actually refers to a patient
		if (! $this->patient_model->is_patient($patient_id)) {
			$this->ui->set_error('This id does not refer to a patient');
			return;
		}
		
		// Check if I am the patient_id of the file to upload
		if ($patient_id !== $this->auth->get_account_id()) {
			// Check if I'm an HCP connected with this patient
			if (! $this->connections_model->is_connected_with($patient_id, $this->auth->get_account_id())) {
				$this->ui->set_error('You don\'t have permissions to upload a medical record for this patient','Permission Denied');
				return;
			}
		}
		
		$config['upload_path'] = './resources/medical_records/'.$patient_id.'/';
		$config['allowed_types'] = 'pdf';
		$config['max_size']	= '1000';
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
		
		// Now, track this record into the DB
		$this->load->model('medical_records_model');
		$result = $this->medical_records_model->add_medical_record(
			array($patient_id, $this->auth->get_account_id(), $issue, $info, $data['file_name'])
		);
		
		switch ($result) {
			case -1:
				$this->ui->set_error('Error recording the uploaded file in the database.', 'Database Error');
				/** @bug This can lead to untracket uploaded files in the server */
				break;
			case -2:
				$this->ui->set_error('Error adding permissions to the database.','Database Error');
				break;
			default:
				$mainview = $this->load->view('mainpane/forms/upload_medrec_success', '', TRUE);
				$this->ui->set(array($mainview));
				break;
		}
	}
}
/**@}*/
?>

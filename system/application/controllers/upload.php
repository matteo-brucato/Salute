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
		$this->load->library('ajax');
		$this->load->library('auth');
		$this->load->helper('download');
	}

	/*
	 * Default function call
	 */ 
	function index()
	{
		$this->ajax->redirect('/medical_records/upload');
	}
	
	function upload_do($type = FALSE) {
		$this->auth->check_logged_in();
		$this->output->enable_profiler(TRUE);
		
		// Get the patient_id
		$patient_id = $this->input->post('patient_id');
		$issue = $this->input->post('issue');
		$info = $this->input->post('info');
		if ($type == FALSE || $patient_id == '' || $patient_id == FALSE || $issue == '' || $issue == FALSE) {
			show_error('Impossible to upload, missing some inputs');
		}
		
		// Check if I am the patient_id of the file to upload
		if ($patient_id !== $this->auth->get_account_id()) {
			// Check if I'm an HCP connected with this patient
			$this->load->model('connections_model');
			if (! $this->connections_model->is_connected_with($patient_id, $this->auth->get_account_id())) {
				show_error('You don\'t have permissions to upload a medical record for this patient');
				return;
			}
		}
		
		$config['upload_path'] = './resources/'.$type.'/'.$patient_id.'/';
		$config['allowed_types'] = 'pdf';
		$config['max_size']	= '1000';
		//$config['max_width']  = '1024';
		//$config['max_height']  = '768';
		
		// Check if the patient's folder already exists
		if (! is_dir($config['upload_path'])) {
			if (mkdir($config['upload_path'], 0777) == FALSE) {
				show_error('Impossible to upload, impossible to create new folder');
			}
		}
		
		$this->load->library('upload', $config);
		
		// Try to upload the file
		if (! $this->upload->do_upload()) {
			//$error = array('error' => $this->upload->display_errors());
			//$mainview = $this->load->view('upload_form', $error);
			show_error($this->upload->display_errors());
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
				$mainview = 'Error recording the uploaded file in the database.';
				/** @bug This can lead to untracket uploaded files in the server */
				break;
			default:
				$mainview = $this->load->view('mainpane/forms/upload_medrec_success', '', TRUE);
				break;
		}
		
		$this->ajax->view(array($mainview, ''));
	}
}
/**@}*/
?>

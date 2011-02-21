<?php
/**
 * @file search.php
 * @brief Controller to handle search requests
 *
 * @defgroup ctr Controllers
 * @ingroup ctr
 * @{
 */

class Search extends Controller {

	function __construct() {
		parent::Controller();
		$this->load->library('ui');
		$this->load->library('auth');
		$this->load->model('hcp_model');
		$this->load->model('patient_model');
		$this->load->model('connections_model');
	}

	/**
	 * Default to the advanced search
	 */
	function index() {
		if ($this->auth->check(array(auth::CurrLOG)) !== TRUE) return;
		$this->ui->set(array(
			$this->load->view('mainpane/forms/search', '' , TRUE)
		));
	}

	/**
	 * Search for hcps in the database
	 * */
	function hcps() {
		if ($this->auth->check(array(auth::CurrLOG)) !== TRUE) return;
		
		/** @todo Change this with the actual search! */
		$hcps = $this->hcp_model->get_hcps();
		
		for ($i = 0; $i < count($hcps); $i++) {
			if ($this->connections_model->is_connected_with(
				$this->auth->get_account_id(),
				$hcps[$i]['account_id']
			)) {
				$hcps[$i]['connected'] = TRUE;
			} else {
				$hcps[$i]['connected'] = FALSE;
			}
		}
		
		$this->ui->set(array(
			$this->load->view('mainpane/lists/all_hcps', array('doc_list' => $hcps) , TRUE)
		));
	}

	/**
	 * Search for patients
	 * 
	 * @todo Not implemented yet
	 * */
	function patients(){ 
		if ($this->auth->check(array(auth::CurrLOG)) !== TRUE) return;
		
		/** @todo Change this with the actual search! */
		$pats = $this->patient_model->get_patients();
		
		for ($i = 0; $i < count($pats); $i++) {
			if ($this->connections_model->is_connected_with(
				$this->auth->get_account_id(),
				$pats[$i]['account_id']
			)) {
				$pats[$i]['connected'] = TRUE;
			} else {
				$pats[$i]['connected'] = FALSE;
			}
		}
		
		$this->ui->set(array(
			$this->load->view('mainpane/lists/all_patients', array('pat_list' => $pats) , TRUE)
		));
	}

	/**
	 * Search for messages
	 * 
	 * @todo Not implemented yet
	 * */
	function messages() {
		$this->auth->check_logged_in();
	}

	/**
	 * Search for patients
	 * 
	 * @note if patient --> only search in my records
	 * @note if hcp --> only search in records i have access to
	 * @todo Not implemented yet
	 * */
	function medical_records() {
		$this->auth->check_logged_in();
	}

}
/** @} */
?>

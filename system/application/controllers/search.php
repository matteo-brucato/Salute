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
		$this->load->library('ajax');
		$this->load->library('auth');
	}

	// Default to the advanced search
	function index() {
		$this->auth->check_logged_in();
		$this->ajax->view(array(
			$this->load->view('mainpane/search_form', '' , TRUE),
			''
		));
	}

	/**
	 * Search for doctors in the database
	 * */
	function hcps() {
		$this->auth->check_logged_in();
		
		$this->load->model('hcp_model');
		$this->load->model('connections_model');
		
		/** @todo Change this with the actual search! */
		$hcps = $this->hcp_model->get_doctors();
		
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
		
		$this->ajax->view(array(
			$this->load->view('mainpane/allhcps', array('doc_list' => $hcps) , TRUE),
			''
		));
	}

	// Search for patients 
	// Note: Only for doctors, should only show patients they are connected with
	function patients(){ 
		$this->auth->check_logged_in();
	}

	// Search in my messages
	function messages() {
		$this->auth->check_logged_in();
	}

	// Search in medical records
	// Note: if patient --> only search in my records
	//	 if doctor --> only search in records i have access to
	function medical_records() {
		$this->auth->check_logged_in();
	}

}
/** @} */
?>

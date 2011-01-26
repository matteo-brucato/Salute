<?php
class Search extends Controller {

//	public $type;

	function __construct(){
		parent::Controller();
		//check if you're logged in	
		$this->load->library('ajax');	
		// $this->type = $this->session->userdata('type');
	}

	// Default to the advanced search
	function index()
	{}

	// Search for doctor
	function hcp()
	{}

	// Search for patients 
	// Note: Only for doctors, should only show patients they are connected with
	function patient()
	{}

	// Search in my messages
	function message()
	{}

	// Search in medical records
	// Note: if patient --> only search in my records
	//	 if doctor --> only search in records i have access to
	function medical_record()
	{}

}
?>

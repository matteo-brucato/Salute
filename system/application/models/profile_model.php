<?php
class Profile_model extends Model {
	function __construct() {
		parent::Model();
		$this->load->database();
	}

	//gets all of the patient information
	//$inputs is of the form(account_id)
	//returns array with all of patient information
	function get_patient($inputs) {
		$sql = "SELECT *
			FROM Patient_Account P
			WHERE P.account_id = ?";
		$query = $this->db->query($sql, $inputs);
		return $query->result_array();		
	}

	//gets all of the doctor information
	//$inputs is of the form(account_id)
	//returns array with all of the doctor information
	function get_doctor($inputs) {
		$sql = "SELECT *
			FROM HCP_Account H
			WHERE H.account_id = ?";
		$query = $this->db->query($sql, $inputs);
		return $query->result_array();		
	}
}
?>

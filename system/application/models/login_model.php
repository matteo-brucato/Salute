<?php
class Login_model extends Model {
	
	function __construct() {
		parent::Model();
		$this->load->database();
	}
	
	//verify if the user is in the system or not
	function authorize($inputs) {
		//test to see if user is a PATIENT
		$sql = "SELECT * 
			FROM Accounts A, Patient_Account P 
			WHERE A.account_id = P.account_id AND email = ? AND password = ?";
		$query = $this->db->query($sql, $inputs);
		$result = $query->result_array();
		if( count($result) >= 1 ){
			return array("patient", $result[0]);
		}
			
		//test to see if the user is a doctor
		$sql = "SELECT * 
			FROM Accounts A, HCP_Account H
			WHERE A.account_id = H.account_id AND email = ? AND password = ?";
		$query = $this->db->query($sql, $inputs);
		$result = $query->result_array();
		if( count($result) >= 1 ){
			return array("doctor", $result[0]);
		}
		
		return NULL;
	}
	
	

	/*function patients($inputs) {
		$sql = "SELECT * FROM patient_account WHERE 
		first_name LIKE '%?%' AND
		last_name LIKE '%?%' ";
		$query = $this->db->query($sql, $inputs);
		return $query->result_array();
	}*/
	
}
?>

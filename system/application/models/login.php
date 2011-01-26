<?php
class Login extends Model {
	
	function __construct() {
		parent::Model();
		$this->load->database();
	}
	
	function login_authorization($inputs) {
		$sql = "SELECT * 
			FROM Accounts A, Patient_Account P 
			WHERE A.account_id = P.account_id AND email = ? AND password = ?";
		$query = $this->db->query($sql, array($inputs);
		$result = $query->result_array();
		if( count($result) >= 1 ){
			return array("Patient", $result[0]);
		}
			
		$sql = "SELECT * 
			FROM Accounts A, HCP_Account H
			WHERE A.account_id = H.account_id AND email = ? AND password = ?";
		$query = $this->db->query($sql, array($inputs);
		$result = $query->result_array();
		if( count($result) >= 1 ){
			return array("Doctor", $result[0]);
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

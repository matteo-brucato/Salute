<?php
class Login extends Model {
	
	function __construct() {
		parent::Model();
		$this->load->database();
	}
	
	function authorize($inputs) {
		$sql = "SELECT * 
			FROM Accounts A, Patient_Account P 
			WHERE A.account_id = P.account_id AND A.email = ? AND A.password = ?;";
		$query = $this->db->query($sql, $inputs);
		$result = $query->result_array();
		if( count($result) >= 1 ){
			return array("patient", $result[0]);
		}
			
		$sql = "SELECT * 
			FROM Accounts A, HCP_Account H
			WHERE A.account_id = H.account_id AND A.email = ? AND A.password = ?;";
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

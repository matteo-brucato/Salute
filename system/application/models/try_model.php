<?php
class Try_model extends Model {
	
	function __construct() {
		parent::Model();
		$this->load->database();
	}
	
	function get_entries() {
		$sql = "SELECT * FROM accounts WHERE email = ? AND account_id = ?";
		$query = $this->db->query($sql, array('patient6@gmail.com', 854459854));
		return $query->result_array();
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

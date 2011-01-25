<?php
class Try_model extends Model {
	
	function Try_model() {
		parent::Model();
	}
	
	function get_entries() {
		$this->load->database();
		$sql = "SELECT * FROM accounts WHERE email = ? AND account_id = ?";
		$query = $this->db->query($sql, array('patient6@gmail.com', 854459854));
		return $query->result_array();
	}
}
?>

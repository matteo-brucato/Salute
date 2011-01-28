<?php
class Search_model extends Model {
	function __construct() {
		parent::Model();
		$this->load->database();
	}

	//$inputs is of the form(account_id)
	function is_patient($inputs) {
		$sql = "SELECT *
			FROM Patient_Account P
			WHERE P.account_id = ?";
		$query = $this->db->query($sql, $inputs);
		$result = $query->result_array();
		if( count( $result )  > 0 )
			return true;
		return false;
				
	}

	//$inputs is of the form(account_id)
	function is_doctor($inputs) {
		$sql = "SELECT *
			FROM HCP_Account H
			WHERE H.account_id = ?";
		$query = $this->db->query($sql, $inputs);
		$result = $query->result_array();
		if( count( $result )  > 0 )
			return true;
		return false;
				
	}

}
?>


<?php
class Login_model extends Model {
	
	function __construct() {
		parent::Model();
		$this->load->database();
	}
	

	/**
	 * Authenticates an account
	 * 
	 * @params $inputs
	 *  Is of the form: array(email, password)
	 * @return
	 * -1 in case of error in a query
	 * empty array if no tuples returned by query
	 * if patient exists: array of form ("patient", tuple of accounts merged with patient_account )
	 * if hcp exists: array of form ("hcp", tuple of accounts merged with hcp_account )
	 * */
	function authorize($inputs) {
		//test to see if user is a PATIENT
		$sql = "SELECT * 
			FROM accounts A, patient_account P 
			WHERE A.account_id = P.account_id AND email = ? AND password = ?";
		$query = $this->db->query($sql, $inputs);
		if ($this->db->trans_status() === FALSE)
			return -1;
		
		if( $query->num_rows() > 0){
			$result = $query->result_array();
			return array( "patient", $result[0] );
		}

		//test to see if the user is a hcp
		$sql = "SELECT * 
			FROM accounts A, hcp_account H
			WHERE A.account_id = H.account_id AND email = ? AND password = ?";
		$query = $this->db->query($sql, $inputs);
		if ($this->db->trans_status() === FALSE)
			return -1;
		if( $query->num_rows() > 0){
			$result = $query->result_array();
			return array( "hcp", $result[0] );
		}	
	
		return array();

	}
	
	
}
?>

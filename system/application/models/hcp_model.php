<?php
class Hcp_model extends Model {

	function __construct() {
		parent::Model();
		$this->load->database();
	}
	
 	//account_id,
	//--hcp_id,
	//first_name,
	//last_name,
	//middle_name,
	//ssn,
	//dob,
	//sex,
	//tel_number,
	//fax_number,
	//specialization,
	//org_name,
	//address

	//detemines wheather an account_id is for a doctor
	//$inputs is of the form(account_id)
	//returns TRUE if its a doctor OR FALSE otherwise
	function is_doctor($inputs){
		
		$sql = "SELECT H.account_id
			FROM HCP_Account H
			WHERE H.account_id = ?";
		$query = $this->db->query($sql, $inputs);
		$result = $query->result_array();
		if( count($result) == 1 )
			return TRUE;
		return FALSE;
	}
	
	/**
	 * Gets all of the doctor information
	 * 
	 * @param $inputs
	 *   Is of the form: array(account_id)
	 * @return
	 *   Array with all of the doctor information
	 * */
	function get_doctor($inputs) {
		$sql = "SELECT *
			FROM HCP_Account H
			WHERE H.account_id = ?";
		$query = $this->db->query($sql, $inputs);
		return $query->result_array();
	}
	
	/**
	 * @return
	 *   Array with all the existing doctors in the database
	 * */
	function get_doctors() {
		$sql = "SELECT * FROM HCP_Account";
		return $this->db->query($sql)->result_array();
	}
	
	
	/**
	 * Searches everything in the HCP_Account table
	 * 
	 * @param $inputs
	 *   Is of the form: array( account_id, first_name, last_name, middle_name, ssn, dob, sex, tel_number, fax_number, specialization, orgname, address)
	 * @return
	 *   Array all of the criteria that matches OR NULL if nothing matches
	 * */
	 function search_doctor_all($inputs){
	 
	 	$sql = "SELECT *
	 		FROM HCP_Account
	 		WHERE string(account_id) LIKE '%?%' AND first_name LIKE '%?%' AND last_name LIKE '%?%' AND middle_name LIKE '%?%' AND 
	 		      string(ssn) LIKE '%?%' AND string(dob) LIKE '%?%' AND sex LIKE '%?%' AND string(tel_number) LIKE '%?%' AND
	 		      string(fax_number) LIKE '%?%' AND specialization LIKE '%?%'orgname LIKE '%?%' and address LIKE '%?%'";
	 	$query = $this->db->query($sql, $inputs[0], $inputs[1], $inputs[2], $inputs[3], $inputs[4], $inputs[5], $inputs[6], 
	 					$inputs[7], $inputs[8], $inputs[9], $inputs[10], $inputs[11], $inputs[12]);
	 	$result = $query->result_array();
	 	if ( count($result) >= 1)
	 		return $result;
	 	return NULL;
	 }
	
	/**
	 * registers a doctor
	 * 
	 * @param $inputs 
	 *   Is of the form( account_id, first_name, last_name, middle_name, ssn, dbo, sex, tel_number, fax_number, specialization, orgname, address)
	 * @return
	 * */
	function register($inputs){
	
		$data = array( 'account_id' => $inputs[0], 'first_name' => $inputs[1], 'last_name' => $inputs[2], 'middle_name' => $inputs[3], 'ssn' => $inputs[4],  
			       'dob' => $inputs[5], 'sex' => $inputs[6], 'tel_number' => $inputs[7], 'fax_number' => $inputs[8], 'specialization' => $inputs[9], 
			       'org_name' => $inputs[10], 'address' => $inputs[11]);
		$this->db->insert('HCP_Account', $data);
	}
	
	//update doctor information
	//$inputs is of the form( account_id, first_name, last_name, middle_name, tel_number, fax_number, specialization, orgname, address)
	//updates the HCP_Account table
	function update_personal_info($inputs){
	
		$data = array( 'first_name' => $inputs[1], 'last_name' => $inputs[2], 'middle_name' => $inputs[3], 'tel_number' => $inputs[4], 
			       'fax_number' => $inputs[5], 'specialization' => $inputs[6], 'org_name' => $inputs[7], 'address' => $inputs[8]);
		$this->db->update('HCP_Account', $data, array('account_id' => $inputs[0]));
	}
}
?>

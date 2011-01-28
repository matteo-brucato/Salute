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
	
	//registers a doctor
	//$inputs is of the form( first_name, last_name, middle_name, ssn, dbo, sex, tel_number, fax_number, specialization, orgname, address)
	//adds the doctor to the HCP_Account
	function register($inputs){
	
		$data = array( 'first_name' => $inputs[0], 'last_name' => $inputs[1], 'middle_name' => $inputs[2], 'ssn' => $inputs[3], 'dob' => $inputs[4], 
			       'sex' => $inputs[5], 'tel_number' => $inputs[6], 'fax_number' => $inputs[7], 'specialization' => $inputs[8], 
			       'org_name' => $inputs[9], 'address' => $inputs[10]);
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

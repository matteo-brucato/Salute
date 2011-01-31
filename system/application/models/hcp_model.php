<?php
/**
 * @file hcp_model.php
 * @brief Model to give access to the HCP table in the database
 *
 * @defgroup mdl Model
 * @ingroup mdl
 * @{
 */

class Hcp_model extends Model {

	function __construct() {
		parent::Model();
		$this->load->database();
	}

	/**
	 * Detemines wheather an account_id is for a doctor
	 * 
	 * @param $inputs
	 *   Is of the form: array(account_id)
	 * @return
	 *   -1 in case of error in a query
	 *    TRUE if it is
	 * 	  FALSE otherwise
	 * */
	function is_doctor($inputs){

		$sql = "SELECT H.account_id
			FROM hcp_account H
			WHERE H.account_id = ?";
		$query = $this->db->query($sql, $inputs);
		
		if ($this->db->trans_status() === FALSE)
			return -1;	
	 	if ($query->num_rows() > 0)
			return TRUE;
			
		return FALSE;
	}
	
	
	/**
	 * Gets all of the doctor information
	 * 
	 * @param $inputs
	 *   Is of the form: array(account_id)
	 * @return
	 *  -1 in case of error in a query
	 *  -7 if account_id does not exist
	 *   Array with all of the doctor information
	 * */
	function get_doctor($inputs) {
					
		$sql = "SELECT *
			FROM hcp_account H
			WHERE H.account_id = ?";
		$query = $this->db->query($sql, $inputs);
		
		if ($this->db->trans_status() === FALSE)
			return -1;	
		if ($query->num_rows() < 1)
			return -7;
					
		return $query->result_array();
	}
	
	/**
	 * Gets all of the doctor information
	 * 
	 * @param $inputs
	 *   Is of the form: Does not take anything in
	 * @return
	 *  -1 in case of error in a query
	 *   Array with all the existing doctors in the database
	 *   NULL if there are not any doctors in the database
	 * */
	function get_doctors() {
		$sql = "SELECT * FROM hcp_account";
		$query = $this->db->query($sql);
		
		if ($this->db->trans_status() === FALSE)
			return -1;
		if ($query->num_rows() > 0)
			return $query->result_array();
		
		return NULL;	
	}
	
	
	/**
	 * Searches everything in the HCP_Account table
	 * 
	 * @param $inputs
	 *   Is of the form: array( account_id, first_name, last_name, middle_name, ssn, dob, sex, tel_number, fax_number, specialization, orgname, address)
	 * @return
	 *  -1 in case of error in a query
	 *   Array all of the criteria that matches OR NULL if nothing matches
	 *   NULL if nothing was found
	 * @note
	 * 	 RIGHT NOW IT SHOULD NOT WORK. NEED TO FIX THE THING WITH THE QUOTES AROUND THE STRINGS FOR THIS TO WORK.
	 * */
	 function search_doctor_all($inputs){
	 
	 	$sql = "SELECT *
	 		FROM hcp_account
	 		WHERE string(account_id) LIKE '%?%' AND first_name LIKE '%?%' AND last_name LIKE '%?%' AND middle_name LIKE '%?%' AND 
	 		      string(ssn) LIKE '%?%' AND string(dob) LIKE '%?%' AND sex LIKE '%?%' AND string(tel_number) LIKE '%?%' AND
	 		      string(fax_number) LIKE '%?%' AND specialization LIKE '%?%'orgname LIKE '%?%' and address LIKE '%?%'";
	 	$query = $this->db->query($sql, $inputs[0], $inputs[1], $inputs[2], $inputs[3], $inputs[4], $inputs[5], $inputs[6], 
	 					$inputs[7], $inputs[8], $inputs[9], $inputs[10], $inputs[11], $inputs[12]);
	 					
	 	if ($this->db->trans_status() === FALSE)
			return -1;
		if ($query->num_rows() > 0)
			return = $query->result_array();

	 	return NULL;
	 }
	
	/**
	 * Registers a doctor
	 * 
	 * @param $inputs 
	 *   Is of the form( account_id, first_name, last_name, middle_name, ssn, dbo, sex, tel_number, fax_number, specialization, orgname, address)
	 * @return
	 *  -1 in case of error in a query
	 *   0 if everything goes fine and a new tuple is inserted into the hcp_account table
	 * */
	function register($inputs){
	
		//$data = array( 'account_id' => $inputs[0], 'first_name' => $inputs[1], 'last_name' => $inputs[2], 'middle_name' => $inputs[3], 'ssn' => $inputs[4],  
			       //'dob' => $inputs[5], 'sex' => $inputs[6], 'tel_number' => $inputs[7], 'fax_number' => $inputs[8], 'specialization' => $inputs[9], 
			       //'org_name' => $inputs[10], 'address' => $inputs[11]);
		//$this->db->insert('HCP_Account', $data);
		
		$sql = "INSERT INTO hcp_account (account_id, first_name, last_name, middle_name, ssn, dob, sex, tel_number, fax_number, specialization, org_name, address)
			VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		$query = $this->db->query($sql, $inputs);
		
		if ($this->db->trans_status() === FALSE)
			return -1;
			
		return 0;
	}
	

	/**
	 * Update doctor information
	 * 
	 * @param $inputs 
	 *   Is of the form: array(account_id, first_name, last_name, middle_name, tel_number, fax_number, specialization, orgname, address)
	 * @return
	 *  -1 in case of error in a query
	 *  -7 if the account_id does not exist
	 *   0 if everything goes fine and the tuple is updated in the hcp_account table
	 * */
	function update_personal_info($inputs){
		
		//test to see if account_id exists
		$sql = "SELECT H.account_id
			FROM hcp_account H
			WHERE H.account_id = ?";
		$query = $this->db->query($sql, array($inputs[0]));
		
		if ($this->db->trans_status() === FALSE)
			return -1;
		if ($query->num_rows() < 1)
			return -7;
	
		//$data = array( 'first_name' => $inputs[1], 'last_name' => $inputs[2], 'middle_name' => $inputs[3], 'tel_number' => $inputs[4], 
			       //'fax_number' => $inputs[5], 'specialization' => $inputs[6], 'org_name' => $inputs[7], 'address' => $inputs[8]);
		//$this->db->update('HCP_Account', $data, array('account_id' => $inputs[0]));
		
		$sql = "UPDATE hcp_account
			SET first_name = ?, last_name = ?, middle_name = ?, tel_number = ?, fax_number = ?, specialization = ?, org_name = ?, address = ?
			WHERE account_id = ?";
		$query = $this->db->query($data, array($inputs[1], $inputs[2], $inputs[3], $inputs[4], $inputs[5],
						       $inputs[6], $inputs[7], $inputs[8], $inputs[0]));
						       
		if ($this->db->trans_status() === FALSE)
			return -1;
			
		return 0;
	}
}
/** @} */
?>

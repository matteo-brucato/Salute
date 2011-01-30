<?php
/**
 * @file patient_model.php
 * @brief Model to give access to the Patient table in the database
 *
 * @defgroup mdl Model
 * @ingroup mdl
 * @{
 */

class Patient_model extends Model {

	function __construct() {
		parent::Model();
		$this->load->database();
	}
	
	
	//detemines wheather an account_id is for a patient
	//$inputs is of the form(account_id)
	//returns TRUE if its a patient OR FALSE otherwise
	function is_patient($inputs){
		
		$sql = "SELECT A.account_id
			FROM Patient_Account A
			WHERE A.account_id = ?";
		$query = $this->db->query($sql, $inputs);
		$result = $query->result_array();
		if( count($result) == 1 )
			return TRUE;
		return FALSE;
	}
	
	//gets all of the patient information
	//$inputs is of the form(account_id)
	//returns array with all of patient information
	function get_patient($inputs) {
	
		$sql = "SELECT *
			FROM Patient_Account P
			WHERE P.account_id = ?";
		$query = $this->db->query($sql, $inputs);
		return $query->result_array();		
	}
	
	
	//registers a patient
	//$inputs is of the form( account_id, first_name, last_name, middle_name, ssn, dbo, sex, tel_number, fax_number, address)
	//adds the patient to the Patient_Account
	function register($inputs) {
	
		//$data = array( 'account_id' => $inputs[0], 'first_name' => $inputs[1], 'last_name' => $inputs[2], 'middle_name' => $inputs[3], 'ssn' => $inputs[4],  
			       //'dob' => $inputs[5],'sex' => $inputs[6], 'tel_number' => $inputs[7], 'fax_number' => $inputs[8], 'address' => $inputs[9]);
		//$this->db->insert('Patient_Account', $data);
		
		$sql = "INSERT INTO patient_account (account_id, first_name, last_name, middle_name, ssn, dob, sex, tel_number, fax_number, address)
			VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		$query = $this->db->query($sql, $inputs);
	}
	
	
	//update patient information
	//$inputs is of the form ( account_id, first_name, last_name, middle_name, tel_number, fax_number, address)
	//updates the Patient_Account table
	function update_personal_info($inputs){
	
		//$data = array( 'account_id' => $inputs[0], 'first_name' => $inputs[1], 'last_name' => $inputs[2], 'middle_name' => $inputs[3], 'tel_number' => $inputs[4], 
		               //'fax_number' => $inputs[5], 'address' => $inputs[6]);
		//$this->db->update('Patient_Account', $data, array('account_id' => $inputs[0]));
		
		$sql = "UPDATE Patient_Account
			SET first_name = ?, last_name = ?, middle_name = ?, tel_number = ?, fax_number = ?, address = ?
			WHERE account_id = ?";
		$query = $this->db->query($data, array($inputs[1], $inputs[2], $inputs[3], $inputs[4], $inputs[5],
						       $inputs[6], $inputs[0]));
	}
}
/** @} */
?>

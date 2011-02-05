<?php
/**
 * @file medical_records_model.php
 * @brief Model to give access to the medical_records table in the database
 *
 * @defgroup mdl Model
 * @ingroup mdl
 * @{
 */

class Medical_records_model extends Model {
	
	function __construct() {
		parent::Model();
		$this->load->database();
	}
	
	/**
	 * Retreives all records for a patient along with the patient information and the account information of the person that 
	 * upload the medical record
	 * 
	 * @params $inputs
	 *  Is of the form: array(patient_id)
	 * @return
	 * -1 in case of error in a query
	 * emtpy array if patient has no medical records
	 * */
	function list_my_records($inputs){
		$sql = "SELECT M.*, P.first_name AS pat_first_name, A.*, H.*
			FROM medical_record M, patient_account P, accounts A, hcp_account H
			WHERE M.patient_id = ? AND M.patient_id = P.account_id AND M.account_id = A.account_id AND 
				((A.account_id = H.account_id) OR (A.account_id = P.account_id))";
		$query = $this->db->query($sql, $inputs);
		
		if ($this->db->trans_status() === FALSE)
			return -1;
		if( $query->num_rows() > 0) {
			return $query->result_array();
		}
		return array();
	}
	
	/**
	 * States wheather a medical record belongs to a patient
	 * 
	 * @param $inputs
	 *   Is of the form: array(patient_id, medical_rec_id)
	 * @return
	 *   Returns -1 if error in query
	 *   Returns TRUE if it is, FALSE otherwise
	 * *
	 function belongs_to($inputs){
	 
	 	$sql = "SELECT *
	 		FROM medical_record M
	 		WHERE M.patient_id = ? AND M.medical_rec_id = ?";
		$query = $this->db->query($sql, $inputs);
		if ($this->db->trans_status() === FALSE)
			return -1;	
		if( $query->num_rows() > 0){
			return true;
		}
		return false;
	 }*/
	
	
	/**
	 * List all information regarding a medical record
	 * 
	 * @param $inputs
	 *   Is of the form: array(medical_rec_id)
	 * @return
	 *   -1 if error in querry
	 *   empty array if medical record doesn't exist
	 *   Array with all the infomation regarding medical record with id medical_rec_id
	 * */
	function get_medicalrecord($inputs) {
	
		$sql = "SELECT *
			FROM medical_record M
			WHERE M.medical_rec_id = ?";
		$query = $this->db->query($sql, $inputs);
		if ($this->db->trans_status() === FALSE)
			return -1;	
		if( $query->num_rows() > 0){
			return $query->result_array();
		}
		return array();
	}
	
	/**
	 * Add a medical record
	 * 
	 * @param $inputs
	 *   Is of the form: array(patient_id, account_id (person adding), issue, suplementary_info, file_path)
	 * @return
	 *   -1 if error in insert
	 *   1 if medical record was properly inserted
	 * */
	function add_medical_record($inputs){
	
		//$data = array( 'patient_id' => $inputs[0], 'account_id' => $inputs[1], 'issue' => $inputs[2], 'suplementary_info' => $inputs[3], 'file_path' => $inputs[4]);
		//$this->db->insert('Medical_Records', $data);	
		
		$sql = "INSERT INTO medical_record (patient_id, account_id, issue, suplementary_info, file_path)
			VALUES (?, ?, ?, ?, ?)";
		$query = $this->db->query($sql, $inputs);
		if ($this->db->trans_status() === FALSE)
			return -1;
		return 1;
	}
	
	/**
	 * Delete a medical record
	 * 
	 * @param $inputs
	 *   Is of the form: array(medical_rec_id)
	 * @return
	 *   -1 if error in delete
	 *   1 if medical record was properly deleted
	 * */
	function delete_medical_record($inputs){
		$sql = "DELETE FROM medical_record
			WHERE medical_rec_id = ?";
		$query = $this->db->query($sql, $inputs);
		if ($this->db->trans_status() === FALSE)
			return -1;
		return 1;
	}
	
	/**
	 * Allows an account_id permission to see medical record
	 * 
	 * @param $inputs
	 *   Is of the form: array(medical_rec_id, account_id)
	 * @return
	 *  -1 if error in insert
	 *  1 otherwise
	 * */
	function allow_permission($inputs){
		//$data = array( 'medical_rec_id' => $inputs[0], 'account_id' => $inputs[1]);
		//$this->db->insert( 'Permissions', $data);
		

		$sql = "INSERT INTO permissions (medical_rec_id, account_id)
			VALUES (?, ?)";
		$query = $this->db->query($data, $inputs);
		if ($this->db->trans_status() === FALSE)
			return -1;
		return 1;
	
	}
	
	/**
	 * Removes the ability for an account_id to view a medical record
	 * 
	 * @param $inputs
	 *   Is of the form: array(medical_rec_id, account_id)
	 * @return
	 *   Deletes a row in the Permissions table
	 * */
	function delete_permission($inputs){
	
		//$this->db->delete('Permissions', array( 'medical_rec_id' => $inputs[0], 'account_id' => $inputs[1]);
		
		$sql = "DELETE FROM permissions
			WHERE medical_rec_id = ? AND account_id = ?";
		$query = $this->db->query($data, $inputs);
		if ($this->db->trans_status() === FALSE)
			return -1;
		return 1;		
	}
	
	/**
	 * Determines if a medical record can be viewed by a hcp
	 * 
	 * @param $inputs
	 *   Is of the form: array(hcp_id, medical_rec_id)
	 * @return
	 *   True or Flase
	 *   -1 if error in query
	 * */
	function is_allowed($inputs){
		
		$sql = "SELECT *
			FROM permission P
			WHERE P.account_id = ? and P.medical_rec_id = ?";
		$query = $this->db->query($sql, $inputs);
		if ($this->db->trans_status() === FALSE)
			return -1;
		if( $query->num_rows() > 0)
			return TRUE;	
		return FALSE;
	}
	
	
	/**
	 * Gives a hcp permission to view all of a patients medical records
	 * 
	 * @param $inputs
	 *   Is of the form: array(patient_id, hcp_id)
	 * @return
	 *   -1 if error in query
	 *   -3 if the connection does not exist
	 *    0 if everything goes fine
	 * */
	function set_all_allowed($inputs){
		
		//first see if the connection exists
		$sql = "SELECT * 
			FROM p_d_connection
			WEHRE patient_id = ? AND hcp_id = ?";
		$query = $this->db->query( $sql, $inputs);
		if ($this->db->trans_status() === FALSE)
			return -1;
		if ( $query->num_rows() < 1 )
			return -3;
			
		//find all of that patients medical records
		$sql = "SELECT M.medical_rec_id
			FROM medical_records M
			WHERE M.patient_id = ?";
		$query = $this->db->query($sql, array($inputs[0]));
		if ($this->db->trans_status() === FALSE)
			return -1;	
	
		$result = $query->result_array();
		
		//allow the hcp to view those medical records
		foreach( $result as $value){
			
			$sql = "INSERT INTO permission (medical_rec_id, account_id)
				VALUES (?, ?)";
			$query = $this->db->query($sql, array($value, $inputs[1]));
			
			if ($this->db->trans_status() === FALSE)
				return -1;
		}
		
		return 0;
	}
	
	
	/**
	 * Does not allow a hcp to view any of the patients medical records
	 * 
	 * @param $inputs
	 *   Is of the form: array(patient_id, hcp_id)
	 * @return
	 *   -1 if error in query
	 *    0 if everything goes fine
	 * */
	function set_all_hidden($inputs){
		
		//find all of that patients medical records
		$sql = "SELECT M.medical_rec_id
			FROM medical_records M
			WHERE M.patient_id = ?";
		$query = $this->db->query($sql, array($inputs[0]));
		if ($this->db->trans_status() === FALSE)
			return -1;
		
		$result = $query->result_array();
		
		//delete that tuple from the permission table	
		foreach ( $query as $value) {
			
			$sql = "DELETE FROM permission	
				WHERE medical_rec_id = ? AND account_id = ?";
			$query = $this->db->query($sql, array($value, $inputs[1]));
			
			if ($this->db->trans_status() === FALSE)
				return -1;
		}
		
		return 0;
	}
}
/** @} */
?>

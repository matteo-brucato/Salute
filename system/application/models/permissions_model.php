<?php
class Permissions_model extends Model {
	
	function __construct() {
		parent::Model();
		$this->load->database();
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
	 * Determines if a medical record can be viewed by a doctor
	 * 
	 * @param $inputs
	 *   Is of the form: array(hcp_id, medical_rec_id)
	 * @return
	 *   True or Flase
	 *   -1 if error in query
	 * */
	function is_allowed($inputs){
		
		$sql = "SELECT *
			FROM permissions P
			WHERE P.account_id = ? and P.medical_rec_id = ?";
		$query = $this->db->query($sql, $inputs);
		if ($this->db->trans_status() === FALSE)
			return -1;
		if( $query->num_rows() > 0)
			return TRUE;	
		return FALSE;
	}
	
	
	/**
	 * Gives a doctor permission to view all of a patients medical records
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
		
		//allow the doctor to view those medical records
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
	 * Does not allow a doctor to view any of the patients medical records
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
?>

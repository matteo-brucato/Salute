<?php
class Medical_Records_model extends Model {
	


	function __construct() {
		parent::Model();
		$this->load->database();
	}
	
	/**
	 * Retreives all records for a patient
	 * 
	 * @params $inputs
	 *  Is of the form: array(patient_id)
	 * @return
	 * -1 in case of error in a query
	 * emtpy array if patient has no medical records
	 * */
	function list_my_records($inputs){
		$sql = "SELECT *
			FROM medical_records
			WHERE patient_id = ?";
		$query = $this->db->query($sql, $inputs);
		if ($this->db->trans_status() === FALSE)
			return -1;	
		if( $query->num_rows() > 0){
			$result = $query->result_array();
			return $result;
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
	 * */
	 function is_myrecord($inputs){
	 
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
	 }
	
	
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
	function get_medicalrecord($inputs){
	
		$sql = "SELECT *
			FROM medical_record M
			WHERE M.medical_rec_id = ?";
		$query = $this->db->query($sql, $inputs);
		if ($this->db->trans_status() === FALSE)
			return -1;	
		if( $query->num_rows() > 0){
			$result = $query->result_array();
			return $result;
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
		
		$sql = "INSERT INTO medical_records (patient_id, account_id, issue, suplementary_info, file_path)
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
		$sql = "DELETE FROM medical_records
			WHERE medical_rec_id = ?";
		$query = $this->db->query($sql, $inputs);
		if ($this->db->trans_status() === FALSE)
			return -1;
		return 1;
	}
}
?>

<?php
class Medical_Records_model extends Model {
	


	function __construct() {
		parent::Model();
		$this->load->database();
	}
	
	//view all medical records the patient has
	//I assume $inputs will be of the form (patient_id)
	//returns an array with all of the medical records OR NULL if there arent any
	function list_my_records($inputs){
		$sql = "SELECT *
			FROM Medical_Records
			WHERE patient_id = ?";
		$query = $this->db->query($sql, $inputs);
		$result = $query->result_array();
		if( count($result) > 0 )
			return $result;
			
		return NULL;
	}
	
	//vill allow a patient OR doctor to add a medical record
	//I assume $inputs will be of the form (patient_id, account_id (person adding), issue, suplementary_info, file_path)
	//inserts the new medical record into the patients account
	function add_medical_record(){
	
		$data = array( 'patient_id' => $inputs[0], 'account_id' => $inputs[1], 'issue' => $inputs[2], 'suplementary_info' => $inputs[3], 'file_path' => $inputs[4]);
		$this->db->insert('Medical_Records', $data);	
	}
	
	//patient deletes medical record
	//I assume $inputs will be of the form (medical_rec_id)
	//deletes the medical record from the account
	function delete_medical_record(){
	
		$this->db->delete('Medical_Records', array('medical_rec_id' == $inputs));
	}
}
?>

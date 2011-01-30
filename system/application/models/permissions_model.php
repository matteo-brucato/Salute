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
	 *   Inserts a row in the Permissions table
	 * */
	function allow_permission($inputs){
	
		//$data = array( 'medical_rec_id' => $inputs[0], 'account_id' => $inputs[1]);
		//$this->db->insert( 'Permissions', $data);
		
		$sql = "INSERT INTO permissions (medical_rec_id, account_id)
			VALUES (?, ?)";
		$query = $this->db->query($data, $inputs);
	
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
	
	}





?>

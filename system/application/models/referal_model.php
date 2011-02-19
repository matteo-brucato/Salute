<?php
/**
 * @file referal.php
 * @brief Model to give access to referal table in the database
 *
 * @defgroup mdl Models
 * @ingroup mdl
 * @{
 */

	//referal_id
	//refering_id
	//is_refered_id
	//patient_id
	//date_time now()
	
class Referal_model extends Model {
	
	function __construct() {
		parent::Model();
		$this->load->database();
	}
	
	
	/**
	 * States wheather a referal belongs to a patient or doctor
	 * 
	 * @param $inputs
	 *   Is of the form: array(account_id, referal_id)
	 * @return
	 *  -1 in case of error in a query
	 *  -2 referal_id does not exist
	 *   TRUE if it is
	 *   FALSE otherwise
	 * */
	 function is_myreferal($inputs){
		 
		//test to see if the referal_id exists
		$sql = "SELECT *
			FROM refers R
			WHERE R.referal_id = ?";
		$query = $this->db->query($sql, array($inputs[1]));
		
		if ($this->db->trans_status() === FALSE)
			return -1;			
		if ($query->num_rows() < 1)
			return -2;

	 	$sql = "SELECT *
			FROM refers R
			WHERE (R.patient_id = ? OR R.refering_id = ?) AND R.referal_id = ?";
	 	$query = $this->db->query($sql, array($inputs[0], $inputs[0], $inputs[1]));
	 	
	 	if ($this->db->trans_status() === FALSE)
			return -1;	
	 	
		if ($query->num_rows() > 0)
			return TRUE;

	 	return FALSE;
	 }
	
	/**
	 * hcp refers refers hcp to patient
	 * 
	 * @param $inputs
	 *   Is of the form: array(account_id of hcp, account_id of refering hcp, account_id of patient)
	 * @return
	 *  -1 in case of error in a query
	 *   0 if everything goes fine approved status is changed to TRUE
	 * */
	function create_referal($inputs){
		
		$sql = "INSERT INTO refers (refering_id, is_refered_id, patient_id)
			VALUES (?, ?, ?)";
		$query = $this->db->query($sql, $inputs);
		
		if ($this->db->trans_status() === FALSE)
			return -1;
			
		return 0;
	}
	
	
	/**
	 * hcp or patient views their referals
	 * 
	 * @param $inputs
	 *   Is of the form: array(account_id, type of account(hcp or patient))
	 * @return
	 *  -1 in case of error in a query
	 *   Array with all appointments
	 *   empty array() if there are no appointments
	 * */
	function view_referals($inputs){
		
		//list all referals a patient has received
		if( $inputs['type'] === 'patient'){
			$sql = "SELECT R.referal_id, H2.first_name, H2.last_name, H3.first_name, H3.last_name, H3.specialization
				FROM refers R, hcp_account H, hcp_account H2, hcp_account H3
				WHERE R.patient_id = ? AND R.refering_id = H.account_id AND R.refering_id = H2.account_id
									   AND R.is_refered_id = H.account_id AND R.is_refered_id = H3.account_id"
			
			$query = $this->db->query($sql, array($inputs['account_id']));
			
			if ($this->db->trans_status() === FALSE)
				return -1;
			
			if ($query->num_rows() > 0)
				return $query->result_array();

			return array();	
		}
		
		//list all referals an hcp has issued
		$sql = "SELECT R.referal_id, P2.first_name, P2.last_name, H2.first_name, H2.last_name
			FROM refers R, patient_account P, patient_account P2, hcp_account H, hcp_account H2
			WHERE R.refering_id = ? AND R.patient_id = P.account_id AND R.patient_id = P2.account_id
								    AND R.is_refered_id = H.account_id AND R.is_refered_id = H2.account_id"
			
			$query = $this->db->query($sql, array($inputs['account_id']));
			
			if ($this->db->trans_status() === FALSE)
				return -1;
			
			if ($query->num_rows() > 0)
				return $query->result_array();

			return array();
	}
	
	
	/**
	 * hcp or patient deletes the referal
	 * 
	 * @param $inputs
	 *   Is of the form: array(referal_id)
	 * @return
	 *  -1 in case of error in a query
	 *  -2 referal_id does not exist
	 *   0 if everything goes fine approved status is changed to TRUE
	 * */
	function delete_referal($inputs){		
		
		//test to see if the referal_id exists
		$sql = "SELECT *
			FROM refers R
			WHERE R.referal_id = ?";
		$query = $this->db->query($sql, $inputs);
		
		if ($this->db->trans_status() === FALSE)
			return -1;		
		if ($query->num_rows() < 1)
			return -2;
		
		$sql = "DELETE FROM refers
				WHERE referal_id = ?";
		$query = $this->db->query($sql, $inputs);
		
		if ($this->db->trans_status() === FALSE)
			return -1;
		
		return 0;
	}
}
/**@}*/
?>


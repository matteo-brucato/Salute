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
	 * States wheather a referal belongs to a patient or the doctor making the referal
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
	 * Gets all of the information regarding a referal
	 * 
	 * @param $inputs
	 *   Is of the form: array(referal_id)
	 * @return
	 *  -1 in case of error in a query
	 *  -2 referal_id does not exist
	 *	 array with all of the referal_information
	 * */
	 function get_referal($inputs){
		 
		$sql = "SELECT *
			FROM refers R
			WHERE R.referal_id = ?";
		$query = $this->db->query($sql, $inputs);
		
		if ($this->db->trans_status() === FALSE)
			return -1;			
		if ($query->num_rows() < 1)
			return -2;
		
		return $query->result_array();
	 }
	 
	 
	/**
	 * hcp refers refers hcp to patient
	 * 
	 * @param $inputs
	 *   Is of the form: array(account_id of hcp, account_id of hcp being refered, account_id of patient)
	 * @return
	 *  -1 in case of error in a query
	 *  -2 referal ID does not exist
	 *  -3 the doctor has already created the same referral
	 *  -4 the doctor being refered is already friends with the patient
	 *   referal_id if everything goes fine
	 * */
	function create_referal($inputs){
		
		//check if the doctor has already created the referal
		$sql = "SELECT *
			FROM refers R
			WHERE R.refering_id = ? AND R.patient_id = ?";
		$query = $this->db->query($sql, array($inputs[0], $inputs[2]));
		if ($this->db->trans_status() === FALSE)
			return -1;
		if ($query->num_rows() > 0)
			return -3;
			
		//check if the patient and the is refered hcp are already connected
		$sql = "SELECT *
			FROM connections C
			WHERE (C.sender_id = ? OR C.receiver_id = ?) AND (C.sender_id = ? OR C.receiver_id = ?) AND C.accepted = TRUE";
		$query = $this->db->query($sql, array($inputs[1], $inputs[1], $inputs[2], $inputs[2]));
		if ($this->db->trans_status() === FALSE)
			return -1;
		if ($query->num_rows() > 0)
			return -4; 
		
		//create the referral
		$sql = "INSERT INTO refers (refering_id, is_refered_id, patient_id)
			VALUES (?, ?, ?)";
		$query = $this->db->query($sql, $inputs);
		
		if ($this->db->trans_status() === FALSE)
			return -1;
		
		//get the referral id of the referral just created,
		//  so that if the level is high to be able to accept the
		//  the referral
		$sql = "select last_value from refers_referal_id_seq";
			$query = $this->db->query($sql);
			if ($this->db->trans_status() === FALSE)
				return -1;
			
			if ($query->num_rows() > 0) {
				$res = $query->result_array();
				$referal_id = $res[0]['last_value'];
			} else {
				return -2;
			}
			
		// set the status of the referral to true (meaning request sent)
		//   if the same request already exists and it has been accepted
		$sql = "SELECT *
			FROM refers R
			WHERE R.is_refered_id = ? AND R.patient_id = ? AND R.status = TRUE";
		$query = $this->db->query($sql, array($inputs[1], $inputs[2]));
		
		if ($this->db->trans_status() === FALSE)
			return -1;
			
		if ($query->num_rows() > 1) {
			
			$sql = "UPDATE refers
				SET status = TRUE
				WHERE referal_id = ?";
			$query = $this->db->query($sql, $referal_id);
			
			if ($this->db->trans_status() === FALSE)
				return -1;
		}
				
		return $referal_id;
	}
	
	
	/**
	 * hcp or patient views their referals
	 * 
	 * @param $inputs
	 *   Is of the form: array(account_id, type of account(hcp or patient))
	 * @return
	 *  -1 in case of error in a query
	 *   Array with all referals
	 *   empty array() if there are no appointments
	 * */
	function view_referals($inputs){
		
		//list all referals a patient has received
		if( $inputs['type'] === 'patient'){
			$sql = "SELECT R.referal_id, R.status, R.is_refered_id, H.first_name AS ref_fn, H.last_name AS ref_ln,
					H2.first_name AS is_ref_fn, H2.last_name AS is_ref_ln, H2.specialization, R.date_time, R.patient_id
				FROM refers R, hcp_account H, hcp_account H2
				WHERE R.patient_id = ? AND R.refering_id = H.account_id AND R.is_refered_id = H2.account_id";
			
			$query = $this->db->query($sql, array($inputs['account_id']));
			
			if ($this->db->trans_status() === FALSE)
				return -1;
			
			if ($query->num_rows() > 0)
				return $query->result_array();

			return array();	
		}
		
		//list all referals an hcp has issued
		$sql = "SELECT R.referal_id, R.status, P.first_name AS pat_fn, P.last_name AS pat_ln, 
				H.first_name AS is_ref_fn, H.last_name AS is_ref_ln, H.specialization, R.date_time 
			FROM refers R, patient_account P, hcp_account H 
			WHERE R.refering_id = ? AND R.patient_id = P.account_id AND R.is_refered_id = H.account_id";
			
			$query = $this->db->query($sql, array($inputs['account_id']));
			
			if ($this->db->trans_status() === FALSE)
				return -1;
			
			if ($query->num_rows() > 0)
				return $query->result_array();

			return array();
	}
	
	
		/**
	 * hcp or patient views their referals(top 5)
	 * 
	 * @param $inputs
	 *   Is of the form: array(account_id, type of account(hcp or patient))
	 * @return
	 *  -1 in case of error in a query
	 *   Array with all referals
	 *   empty array() if there are no appointments
	 * */
	function view_top_five($inputs){
		
		//list all referals a patient has received
		if( $inputs['type'] === 'patient'){
			$sql = "SELECT R.referal_id, R.status, R.is_refered_id, H.first_name AS ref_fn, H.last_name AS ref_ln,
					H2.first_name AS is_ref_fn, H2.last_name AS is_ref_ln, H2.specialization, R.date_time, R.patient_id
				FROM refers R, hcp_account H, hcp_account H2
				WHERE R.patient_id = ? AND R.refering_id = H.account_id AND R.is_refered_id = H2.account_id
				ORDER BY R.date_time
				LIMIT 5";
			
			$query = $this->db->query($sql, array($inputs['account_id']));
			
			if ($this->db->trans_status() === FALSE)
				return -1;
			
			if ($query->num_rows() > 0)
				return $query->result_array();

			return array();	
		}
		
		//list all referals an hcp has issued
		$sql = "SELECT R.referal_id, R.status, P.first_name AS pat_fn, P.last_name AS pat_ln, 
				H.first_name AS is_ref_fn, H.last_name AS is_ref_ln, H.specialization, R.date_time 
			FROM refers R, patient_account P, hcp_account H 
			WHERE R.refering_id = ? AND R.patient_id = P.account_id AND R.is_refered_id = H.account_id
			ORDER BY R.date_time
			LIMIT 5";
			
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
	function delete($inputs){		
		
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
	
	/**
	 * patient approves a referal request (only sets the status to true.
	 * 	actual connection id done by the function add_connection from the
	 * 	connections controller
	 * 
	 * @param $inputs
	 *   Is of the form: array(referal_id)
	 * @return
	 *  -1 in case of error in a query
	 *  -2 if referal_id does not exist
	 *   0 if everything goes fine approved status is changed to TRUE
	 * */
	function approve($inputs){
		
		//test to see if the appointment_id exists
		$sql = "SELECT *
			FROM refers R
			WHERE R.referal_id = ?";
		$query = $this->db->query($sql, $inputs);
		
		if ($this->db->trans_status() === FALSE)
			return -1;		
		if ($query->num_rows() < 1)
			return -2;
		
		$result = $query->result_array();
		
		//if a low level referal comes in firts, and then its follewed by a high level referal,
		//  set the low level referal status to true (meaning) request sent
		$sql = "UPDATE refers
			SET status = TRUE
			WHERE patient_id = ? and is_refered_id = ?";
		$query = $this->db->query($sql, array($result[0]['patient_id'], $result[0]['is_refered_id']));
		
		if ($this->db->trans_status() === FALSE)
			return -1;
		
		return 0;
	}
}
/**@}*/
?>


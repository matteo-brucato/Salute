<?php
/**
 * @file bills_model.php
 * @brief Model to give access to the bill table into the database
 *
 * @defgroup mdl Models
 * @ingroup mdl
 * @{
 */

class Bills_model extends Model {
	
	function __construct() {
		parent::Model();
		$this->load->database();
	}

	/**
	 * States wheather a bill belongs to a patient_id
	 * 
	 * @param $inputs
	 *   Is of the form: array(patient_id, bill_id)
	 * @return
	 *  -1 in case of error in a query
	 *  -6 in case bill_id does not exist
	 *   TRUE if it is
	 *   FALSE otherwise
	 * */
	 function is_mybill($inputs){
		 
		//test to see if bill_id exists
		$sql = "SELECT *
			FROM payment P
			WHERE P.bill_id = ?";
		$query = $this->db->query($sql, array($inputs[1]));
		
		if ($this->db->trans_status() === FALSE)
			return -1;
		if ($query->num_rows() < 1)
			return -6;
	 
	 	$sql = "SELECT *
	 		FROM payment P
	 		WHERE (P.patient_id = ? AND P.bill_id = ?) OR
					(P.hcp_id = ? AND P.bill_id = ?)";
	 	$query = $this->db->query($sql, array($inputs[0], $inputs[1], $inputs[0], $inputs[1]));
	 	
	 	if ($this->db->trans_status() === FALSE)
			return -1;
		if ($query->num_rows() > 0)
			return TRUE;
			
	 	return FALSE;
	 }
	 
	/**
	 * Gets a bill specified by the input
	 * 
	 * @param $inputs
	 *   Is of the form: bill_id
	 * @return
	 *  -1 in case of error in a query
	 *   Array with the bill
	 *   empty array() if there are no bills
	 * */
	 function get_bill($input){
		 
		$sql = "SELECT *
			FROM payment P
			WHERE P.bill_id = ?";
		$query = $this->db->query($sql, $input);
		
		if ($this->db->trans_status() === FALSE)
			return -1;
		if ($query->num_rows() > 0)
			return $query->result_array();
		return array();
	 }


	/**
	 * View all bills a patient has received OR all bills a hcp has issued
	 * 
	 * @param $inputs
	 *   Is of the form: array(account_id, type of account(hcp or patient))
	 * @return
	 *  -1 in case of error in a query
	 *   Array with all bills
	 *   empty array() if there are no bills
	 * */
	function view_all($inputs){
	
		//lists all bills a patient has received
		if( $inputs[1] === 'patient'){
			$sql = "Select B.*, H2.first_name, H2.last_name
				FROM payment B, hcp_account H, hcp_account H2
				WHERE B.hcp_id = H.account_id AND B.patient_id = ? AND B.hcp_id = H2.account_id AND patient_kept = TRUE";
			$query = $this->db->query($sql, $inputs[0]);
			
			if ($this->db->trans_status() === FALSE)
				return -1;
			if ($query->num_rows() > 0){
				return $query->result_array();
			}
			return array();	
		}

		//lists all bills a hcp has issued
		$sql = "Select B.*, P2.first_name, P2.last_name
			FROM payment B, patient_account P, patient_account P2
			WHERE B.patient_id = P.account_id AND B.hcp_id = ? AND B.patient_id = P2.account_id AND hcp_kept = TRUE";
		$query = $this->db->query($sql, $inputs[0]);

		if ($this->db->trans_status() === FALSE)
			return -1;			
		if ($query->num_rows() > 0)
			return $query->result_array();

		return array();	
	}

	/**
	 * View top 5  bills a patient has received OR top 5 bills a hcp has issued
	 * 
	 * @param $inputs
	 *   Is of the form: array(account_id, type of account(hcp or patient))
	 * @return
	 *  -1 in case of error in a query
	 *   Array with all bills
	 *   empty array() if there are no bills
	 * */
	function view_top_five($inputs){
	
		//lists all bills a patient has received
		if( $inputs[1] === 'patient'){
			$sql = "Select B.*, H2.first_name, H2.last_name
				FROM payment B, hcp_account H, hcp_account H2
				WHERE B.hcp_id = H.account_id AND B.patient_id = ? AND B.hcp_id = H2.account_id AND patient_kept = TRUE
				ORDER BY B.due_date desc
				LIMIT 5";
			$query = $this->db->query($sql, $inputs[0]);
			
			if ($this->db->trans_status() === FALSE)
				return -1;
			if ($query->num_rows() > 0){
				return $query->result_array();
			}
			return array();	
		}

		//lists all bills a hcp has issued
		$sql = "Select B.*, P2.first_name, P2.last_name
			FROM payment B, patient_account P, patient_account P2
			WHERE B.patient_id = P.account_id AND B.hcp_id = ? AND B.patient_id = P2.account_id AND hcp_kept = TRUE
			ORDER BY B.due_date desc
			LIMIT 5";
		$query = $this->db->query($sql, $inputs[0]);

		if ($this->db->trans_status() === FALSE)
			return -1;			
		if ($query->num_rows() > 0)
			return $query->result_array();

		return array();	
	}
	
	
	
		/**
	 * View top 5  bills a patient has received OR top 5 bills a hcp has issued
	 * 
	 * @param $inputs
	 *   Is of the form: array(account_id, type of account(hcp or patient))
	 * @return
	 *  -1 in case of error in a query
	 *   Array with all bills
	 *   empty array() if there are no bills
	 * */
	function view_top_five_between($inputs){
	
		//lists all bills a patient has received
		if( $inputs[1] === 'patient'){
			$sql = "Select B.*, H2.first_name, H2.last_name
				FROM payment B, hcp_account H, hcp_account H2
				WHERE B.hcp_id = H.account_id AND B.patient_id = ? AND B.hcp_id = H2.account_id AND patient_kept = TRUE AND B.hcp_id = ?
				ORDER BY B.due_date desc
				LIMIT 5";
			$query = $this->db->query($sql, array($inputs[0], $inputs[2]));
			
			if ($this->db->trans_status() === FALSE)
				return -1;
			if ($query->num_rows() > 0){
				return $query->result_array();
			}
			return array();	
		}

		//lists all bills a hcp has issued
		$sql = "Select B.*, P2.first_name, P2.last_name
			FROM payment B, patient_account P, patient_account P2
			WHERE B.patient_id = P.account_id AND B.hcp_id = ? AND B.patient_id = P2.account_id AND hcp_kept = TRUE AND B.patient_id = ?
			ORDER BY B.due_date desc
			LIMIT 5";
		$query = $this->db->query($sql, array($inputs[0], $inputs[2]));

		if ($this->db->trans_status() === FALSE)
			return -1;			
		if ($query->num_rows() > 0)
			return $query->result_array();

		return array();	
	}
	/**
	 * View all CURRENT bills a patient has received OR all CURRENT bills a hcp has issued
	 * 
	 * @param $inputs
	 *   Is of the form: array(account_id, type of account(hcp or patient))
	 * @return
	 *  -1 in case of error in a query
	 *   Array with all bills
	 *   empty array() if there are no bills
	 * */
	function view_current($inputs){

		//lists all current bills a patient has received
		if( $inputs[1] == 'patient'){
			$sql = "Select B.*, H2.first_name, H2.last_name
				FROM payment B, hcp_account H, hcp_account H2
				WHERE B.hcp_id = H.account_id AND B.patient_id = ? AND B.hcp_id = H2.account_id AND B.due_date >= NOW() AND patient_kept = TRUE";
			$query = $this->db->query($sql, $inputs[0]);
			
			if ($this->db->trans_status() === FALSE)
				return -1;			
			if ($query->num_rows() > 0)
				return $query->result_array();
				
			return array();	
		}

		//list all current bills a hcp has issued
		$sql = "Select B.*, P2.first_name, P2.last_name
			FROM payment B, patient_account P, patient_account P2
			WHERE B.patient_id = P.account_id AND B.hcp_id = ? AND B.patient_id = P2.account_id AND B.due_date >= NOW() AND hcp_kept = TRUE";
		$query = $this->db->query($sql, $inputs[0]);
		
		if ($this->db->trans_status() === FALSE)
				return -1;			
		if ($query->num_rows() > 0)
			return $query->result_array();			
		
		return array();	
	}


	/**
	 * View all PAST bills a patient has received OR all PAST bills a hcp has issued
	 * 
	 * @param $inputs
	 *   Is of the form: array(account_id, type of account(hcp or patient))
	 * @return
	 *  -1 in case of error in a query
	 *   Array with all bills
	 *   empty array() if there are no bills
	 * */
	function view_past($inputs){

		//lists all past bills a patient has received
		if( $inputs[1] == 'patient'){
			$sql = "Select B.*, H2.first_name, H2.last_name
				FROM payment B, hcp_account H, hcp_account H2
				WHERE B.hcp_id = H.account_id AND B.patient_id = ? AND B.hcp_id = H2.account_id AND B.due_date < NOW() AND patient_kept = TRUE";
			$query = $this->db->query($sql, $inputs[0]);
			
			if ($this->db->trans_status() === FALSE)
				return -1;			
			if ($query->num_rows() > 0)
				return $query->result_array();	
			
			return array();	
		}

		//list all past bills a hcp has issued
		$sql = "Select B.*, P2.first_name, P2.last_name
			FROM payment B, patient_account P, patient_account P2
			WHERE B.patient_id = P.account_id AND B.hcp_id = ? AND B.patient_id = P2.account_id AND B.due_date < NOW() AND hcp_kept = TRUE";
		$query = $this->db->query($sql, $inputs[0]);
		
		if ($this->db->trans_status() === FALSE)
				return -1;			
		if ($query->num_rows() > 0)
			return $query->result_array();	
			
		return array();
	}


	/**
	 * View all PAST bills a patient has received OR all PAST bills a hcp has issued THAT HAVE NOT CLEARED
	 * 
	 * @param $inputs
	 *   Is of the form: array(account_id, type of account(hcp or patient))
	 * @return
	 *  -1 in case of error in a query
	 *   Array with all bills
	 *   emmpty array() if there are no bills
	 * */	
	function view_past_not_cleared($inputs){

		//lists all past bills a patient has received that are NOT CLEARED
		if( $inputs[1] == 'patient'){
			$sql = "Select B.bill_id, H2.first_name, H2.last_name, B.amount, B.descryption, B.due_date, B.cleared
				FROM payment B, hcp_account H, hcp_account H2
				WHERE B.hcp_id = H.account_id AND B.patient_id = ? AND B.hcp_id = H2.account_id AND B.due_date < curdate() AND B.cleared == FALSE  AND patient_kept = TRUE";
			$query = $this->db->query($sql, $inputs[0]);
				
			if ($this->db->trans_status() === FALSE)
				return -1;			
			if ($query->num_rows() > 0)
				return $query->result_array();	
			
			return array();	
		}

		//list all past bills a hcp has issued that are NOT CLEARED
		$sql = "Select B.bill_id, P2.first_name, P2.last_name, B.amount, B.descryption, B.due_date, B.cleared
			FROM payment B, patient_account P, patient_account P2
			WHERE B.patient_id = P.account_id AND B.hcp_id = ? AND B.patient_id = P2.account_id AND B.due_date < curdate() AND B.cleared = FALSE  AND hcp_kept = TRUE";
		$query = $this->db->query($sql, $inputs[0]);
		
		if ($this->db->trans_status() === FALSE)
				return -1;			
		if ($query->num_rows() > 0)
			return $query->result_array();	
			
		return array();
	}


	/**
	 * hcp issues a new bill to patient
	 * 
	 * @param $inputs
	 *   Is of the form: array(patient_id, hcp_id, amount, descryption, due_date)
	 * @return
	 *  -1 in case of error in a query
	 *   0 if everything goes fine and a new tuple is inserted into the Payment table
	 * @note
	 *   NEED TO ADD ATTRIUTE TO THE DATABASE TO BE ABLE TO ADD ITEMIZED RECEIPT
	 * */	
	function issue_bill($inputs){

		//$data = array( 'patient_id' => $inputs[0], 'hcp_id' => $inputs[1], 'amount' => $inputs[2], 'descryption' => $inputs[3], 'due_date' => $inputs[4]);
		//$this->db->insert('Payment', $data);
		
		$sql = "INSERT INTO payment (patient_id, hcp_id, amount, descryption, due_date)
			VALUES (?, ?, ?, ?, ?)";
		$query = $this->db->query($sql, $inputs);
		
		if ($this->db->trans_status() === FALSE)
			return -1;
			
		return 0;	
	}


	/**
	 * Patient pays a bill
	 * 
	 * @param $inputs
	 *   Is of the form: array(bill_id)
	 * @return
	 *  -1 in case of error in a query
	 *  -6 in case the bill_id does not exist
	 *   0 if everything goes fine and cleared attribute is assigned TRUE
	 * */	
	function pay_bill($inputs){
	
		//test to see if bill_id exists
		$sql = "SELECT *
			FROM payment P
			WHERE P.bill_id = ?";
		$query = $this->db->query($sql, $inputs);
		
		if ($this->db->trans_status() === FALSE)
			return -1;
		if ($query->num_rows() < 1)
			return -6;
			
		//$data = array('cleared' => TRUE);
		//$this->db->update('Payment', $data, array('bill_id' => $inputs[0]));
		
		$sql = "UPDATE payment
			SET cleared = TRUE
			WHERE bill_id = ?";
		$query = $this->db->query($sql, $inputs);
		
		if ($this->db->trans_status() === FALSE)
			return -1;
			
		return 0;
	}
	
	/**
	 * Allows hcps to delete a bill
	 * 
	 * @param $inputs
	 *   Is of the form: array(hcp_id, bill_id)
	 * @return
	 *  -1 in case of error in a query
	 *  -6 in case the bill_id does not exist
	 *   0 if everything goes fine and the tuple is removed from the Payments table
	 * */
	 function delete_bill($inputs){
		 
		//test to see if bill_id exists
		$sql = "SELECT *
			FROM payment P
			WHERE P.bill_id = ?";
		$query = $this->db->query($sql, $inputs[0]);
		
		if ($this->db->trans_status() === FALSE)
			return -1;
		if ($query->num_rows() < 1)
			return -6;
		
		if( $inputs[1] === 'hcp' ){
			$sql = "UPDATE payment
				SET hcp_kept = FALSE
				WHERE bill_id = ?";
		}	
		else{
			$sql = "UPDATE payment
				SET patient_kept = FALSE
				WHERE bill_id = ?";			
			
		}
		
	 //	$sql = "DELETE FROM payment
	 //		WHERE bill_id = ?";
	 	$query = $this->db->query($sql, $inputs[0]);
	 	
	 	if ($this->db->trans_status() === FALSE)
			return -1;
			
		return 0;
	 }	
}
/**@}*/
?>

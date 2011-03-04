<?php
/**
 * @file connections_model.php
 * @brief Model to give access to the two connection tables in the database
 *
 * @defgroup mdl Models
 * @ingroup mdl
 * @{
 */

class Connections_model extends Model {
	function __construct() {
		parent::Model();
		$this->load->database();
	}

	/**
	 *  Lists all of the patients the specified account has
	 * 
	 * @param $account_id
	 *   Is of the form: array(account_id)
	 * @return
	 *  -1 in case of error in a query
	 *   Array with all the patients a hcp has
	 *   empty array() if none
	 * */
	function list_patients_connected_with($account_id) {
		$sql = "SELECT P.*
			FROM connections D, patient_account P
			WHERE D.accepted = TRUE AND (
			      (D.sender_id = ? AND D.receiver_id = P.account_id)
			OR    (D.receiver_id = ? AND D.sender_id = P.account_id))";
		$query = $this->db->query($sql, array($account_id, $account_id));
		
		if ($this->db->trans_status() === FALSE)
			return -1;
			
		if ($query->num_rows() > 0)
			return $query->result_array();
		
		return array();
	}


	/**
	 *  Lists all of the hcp friends the specified account has
	 * 
	 * @param $inputs
	 *   Is of the form: array(account_id)
	 * @return
	 *  -1 in case of error in a query
	 *   Array with all the hcp friends
	 *   empty array() if none
	 * */
	function list_hcps_connected_with($account_id) {
		$sql = "SELECT H.*
			FROM connections D, hcp_account H
			WHERE D.accepted = TRUE AND (
			      (D.sender_id = ? AND D.receiver_id = H.account_id)
			OR    (D.receiver_id = ? AND D.sender_id = H.account_id))";
		$query = $this->db->query($sql, array($account_id, $account_id));
		
		if ($this->db->trans_status() === FALSE)
			return -1;
			
		if ($query->num_rows() > 0)	
			return $query->result_array();
			
		return array();
	}	


	/**
	 *  Lists all of the hcps a patient has
	 * 
	 * @param $inputs
	 *   Is of the form: array(account_id)
	 * @return
	 *  -1 in case of error in a query
	 *   Array of all the hcp friends
	 *   empty array() if none
	 * *
	function list_hcps_connected_with($inputs) {
		$sql = "SELECT H.*
			FROM connections D, hcp_account H
			WHERE D.accepted = TRUE AND D.sender_id =  ? AND H.account_id = D.receiver_id";
		$query = $this->db->query($sql, $inputs);
		
		if ($this->db->trans_status() === FALSE)
			return -1;
			
		if ($query->num_rows() > 0)	
			return $query->result_array();
		
		return array();
	}*/
	
	
	/**
	 * Lists all pending outgoing connections requests to hcps, made by the specified account
	 * 
	 * @param $inputs
	 *   Is of the form: array(account_id)
	 * @return	
	 *  -1 in case of error in a query
	 *   Array with hcps
	 *   empty array() if none
	 * */
	 function pending_outgoing_hcps($inputs)
	 {
		$sql = "SELECT H.*
	 		FROM connections C, hcp_account H
			WHERE C.accepted = FALSE AND C.sender_id = ? AND C.receiver_id = H.account_id";
 		$query = $this->db->query($sql, $inputs);
 		
 		if ($this->db->trans_status() === FALSE)
			return -1;
		
		if ($query->num_rows() > 0)
			return $query->result_array();
		
		return array();
	}
	
	/**
	 * Lists all pending outgoing connections requests to patients, made by the specified account
	 * 
	 * @param $inputs
	 *   Is of the form: array(account_id)
	 * @return	
	 *  -1 in case of error in a query
	 *   Array with hcps
	 *   empty array() if none
	 * */
	function pending_outgoing_patients($inputs)
	{
		$sql = "SELECT P.*
			FROM connections C, patient_account P
			WHERE C.accepted = FALSE AND C.sender_id = ? AND C.receiver_id = P.account_id";
 		$query = $this->db->query($sql, $inputs);
 		
 		if ($this->db->trans_status() === FALSE)
			return -1;
		
		if ($query->num_rows() > 0)
			return $query->result_array();
		
		return array();
	}
	
	/**
	 * List all pending incoming connections requests, coming from hcps,
	 * for the specified account
	 * 
	 * @param $inputs
	 *   Is of the form: array(account_id)
	 * @return
	 * 	 -1 in case of error in a query
	 *    Array with all pending requests OR 
	 *    empty array() if none
	 * */
	function pending_incoming_hcps($inputs)
	{
		$sql = "SELECT H.*
			FROM connections C, hcp_account H
			WHERE C.accepted = FALSE AND C.receiver_id = ? AND H.account_id = C.sender_id";
		$query = $this->db->query($sql, $inputs);

		if ($this->db->trans_status() === FALSE)
			return -1;

		if ($query->num_rows() > 0)
			return $query->result_array();

		return array();
	}
	
	
	/**
	 * List all pending incoming connections requests, coming from patients,
	 * for the specified account
	 * 
	 * @param $inputs
	 *   Is of the form: array(account_id)
	 * @return
	 *  -1 in case of error in a query
	 *   Array with all pending requests
	 *   empty array() if none
	 * */
	function pending_incoming_patients($inputs)
	{
		$sql = "SELECT P.*
	 		FROM connections C, patient_account P
			WHERE C.accepted = FALSE AND C.receiver_id = ? AND P.account_id = C.sender_id";
 		$query = $this->db->query($sql, $inputs);
	
		if ($this->db->trans_status() === FALSE)
			return -1;
		
		if ($query->num_rows() > 0)
			return $query->result_array();
		
		return array();
	 }
	 
	 
	
	 
	 
	 /**
	 * List all pending outgoing requests to hcps from a hcp
	 * 
	 * @param $inputs
	 *   Is of the form: array(account_id)
	 * @return
	 *  -1 in case of error in a query
	 *   Array with all pending requests
	 *   empty array() if none
	 * *
	 function pending_outgoing_hcps_4_a_hcp($inputs)
	 {
		$sql = "SELECT A.*
	 		FROM connections D, hcp_account A
			WHERE D.sender_id = ? AND D.accepted = FALSE AND A.account_id = D.receiver_id";
 		$query = $this->db->query($sql, $inputs);
	
		if ($this->db->trans_status() === FALSE)
			return -1;
		
		if ($query->num_rows() > 0)
			return $query->result_array();
		
		return array();
	 }*/
	
	/**
	 * List all a connection between two individuals
	 * 
	 * @param $inputs
	 *   Is of the form: array(account_id, account_id)
	 * @return
	 *  -1 in case of error in a query
	 *   Array with the connection
	 *   NULL if nothing
	 * */
	function get_connection($a_id, $b_id) {
		// Try to get the connection from connections table
		$sql = "SELECT *
				FROM connections C
				WHERE (C.sender_id = ? AND C.receiver_id = ?)
				OR    (C.receiver_id = ? AND C.sender_id = ?)";
		$query = $this->db->query($sql, array($a_id, $b_id, $a_id, $b_id));
		if ($this->db->trans_status() === FALSE)
			return -1;
		
		// If found, return it
		if ($query->num_rows() > 0) {
			$array = $query->result_array();
			return $array[0];
		}
		
		// Try to get the connection from hcp-hcp table
		//$sql = "SELECT *
		//		FROM connections C
		//		WHERE (C.sender_id = ? AND C.receiver_id = ?)
		//		OR    (C.receiver_id = ? AND C.sender_id = ?)";
		//$query = $this->db->query($sql, array($a_id, $b_id, $a_id, $b_id));
		//if ($this->db->trans_status() === FALSE)
		//	return -1;
		
		// If found, return it
		//if ($query->num_rows() > 0) {
		//	$array = $query->result_array();
		//	return $array[0];
		//}
		
		// If nothing found
		return NULL;
	}
	
	
	/**
	 * Tells whether an account is in connection with another account
	 * 
	 * @params $inputs
	 * 	 Is of the form: array(account_id, account_id)
	 * @return
	 *  -1 in case of error in a query
	 *   TRUE if $a_id is in connection with $b_id
	 *   FALSE otherwise
	 * */
	function is_connected_with($a_id, $b_id) {
		$sql = "(SELECT *
			FROM connections DD
			WHERE DD.accepted = true AND
				((DD.sender_id = ? AND DD.receiver_id = ?)
			OR   (DD.receiver_id = ? AND DD.sender_id = ?)))";
		
				//UNION
				//(SELECT *
				//FROM p_d_connection PD
				//WHERE PD.accepted = true AND
					//((PD.hcp_id = ? AND PD.patient_id = ?)
					//OR (PD.patient_id = ? AND PD.hcp_id = ?)));

		$query = $this->db->query($sql, array($a_id, $b_id, $a_id, $b_id));
											  
		if ($this->db->trans_status() === FALSE)
			return -1;
			
		return ($query->num_rows() > 0);
	}
	
	
	/**
	 * Tells whether an account is almost in connection with another account.
	 * In other words if they are connected but still pending.
	 * 
	 * @params $inputs
	 * 	 Is of the form: array(account_id)
	 * @return
	 *  -1 in case of error in a query
	 *   TRUE if $a_id is in connection with $b_id
	 *   FALSE otherwise
	 * */
	function is_pending_with($a_id, $b_id) {
		$sql = "(SELECT *
			FROM connections DD
			WHERE
				((DD.sender_id = ? AND DD.receiver_id = ?)
				OR (DD.receiver_id = ? AND DD.sender_id = ?)))";
				
				//UNION
				//(SELECT *
				//FROM p_d_connection PD
				//WHERE
				//	((PD.hcp_id = ? AND PD.patient_id = ?)
				//	OR (PD.patient_id = ? AND PD.hcp_id = ?)));
				
		$query = $this->db->query($sql, array($a_id, $b_id, $a_id, $b_id));
											  
		if ($this->db->trans_status() === FALSE)
			return -1;
			
		return ($query->num_rows() > 0);
	}
	
	
	/**
	 * Creates a new request for a connection. Creates a tuple in
	 * Connection setting 'accepted' to FALSE.
	 * 
	 * @param
	 *   $inputs of the form array(sender_id, receiver_id)
	 *
	 * @note
	 *   If account A request a connection with B, but B already
	 *   requested a connection to A, then this function will just
	 *   accept the connection between A and B
	 * 
	 * @return
	 *  -1 in case of error in a query
	 *  -3 if the connection already exists
	 *   0 if everything goes fine
	 * 
	 * @test Auto-acceptance works
	 * */
	function add_connection($inputs) {
		//testing to see if requestor is sending 2nd request
		$sql = "SELECT *
			FROM connections D
			WHERE (D.sender_id = ? AND D.receiver_id = ?)";
		$query = $this->db->query($sql, $inputs);
		
		// If the other part already requested me the connection,
		// This request will be considered as an acceptance
		if ($query->num_rows() > 0) {
			// This is an update to the original request
			return $this->accept_connection($inputs);
		}

		// Request has never been made in either direction
		$this->db->query("INSERT INTO connections (sender_id, receiver_id, date_connected)
			VALUES (?, ?, current_timestamp)", $inputs);
		
		if ($this->db->trans_status() === FALSE)
			return -1;
		
		return 0;
	}
	
	/**
	 * Creates a new request for a patient hcp connection.
	 * 
	 * @param
	 *   $inputs of the form (account_id of patient, account_id of HCP)
	 *
	 * @return
	 *  -1 in case of error in a query
	 *  -3 if the connection is pending or exists
	 *   0 if everything goes fine
	 * 
	 * *
	function add_patient_hcp($inputs){
	
		//test to see if connection already exists
		$sql = "SELECT *
				FROM connections D
				WHERE D.sender_id = ? AND D.receiver_id = ?";
		$query = $this->db->query($sql, $inputs);
		
		if ($this->db->trans_status() === FALSE) {
			return -1; 
		}
		
		if ($query->num_rows() > 0) {
			return -3;
		}
			
		$this->db->query("INSERT INTO connections (sender_id, receiver_id, date_connected)
			VALUES (? , ?, current_timestamp)", $inputs);
		
		if ($this->db->trans_status() === FALSE) {
			return -1; 
		}
		
		return 0;
	}*/

	/**
	 * Sets the connection hcp-hcp to TRUE only if it was not
	 * already accepted.
	 * 
	 * @param
	 *   $inputs of the form array(account_id of requester, account_id of accepter)
	 * 
	 * @return
	 *  -1 in case of error in a query
	 *  -2 if the connection does not exist
	 *  -3 if the connection was already accepted
	 *   0 if everything goes fine
	 * */
	function accept_connection($inputs) {
		
		//echo 'here1';
		
		$query = $this->db->query("SELECT * FROM connections
			WHERE sender_id = ? AND receiver_id = ?", $inputs);
		
		if ($this->db->trans_status() === FALSE) {
			return -1; // query error
		}
		
		if ($query->num_rows() < 1) {
			return -2; // connection does not exist
		}
		
		$res = $query->result();
		if ($res[0]->accepted == 't') {
			return -3; // connection alreaday accepted
		}
		
		// Accept connection
		$this->db->query("UPDATE connections SET accepted = TRUE
			WHERE sender_id = ? AND receiver_id = ?", $inputs);
		
		if ($this->db->trans_status() === FALSE) {
			return -1; // query error
		}
		
		// If the connection came via referal and level to that hcp was 1 or 3,
		// copy permissions from 'refering' hcp to the 'is_refered' hcp
		
		//get the referal that belongs to the patient and the is_refered hcp
		$sql = "SELECT * 
			FROM refers R
			WHERE R.patient_id = ? AND R.is_refered_id = ?";
		$result = $this->db->query($sql, $inputs);
		
		if ($this->db->trans_status() === FALSE) {
			return -1; /* query error */
		}
		if ($result->num_rows() < 1) {
			return 0; // It was a regular connection request
		}
		$result = $result->result_array();
		
		//copy if it came via referal
		foreach ($result as $value) {
			
			//echo 'here2';
			
			//for each referal, get the level of the connection between the refering hcp and the patient
			$sql = "SELECT *
				FROM connections C
				WHERE (C.sender_id = ? OR C.receiver_id = ?) AND (C.sender_id = ? OR C.receiver_id = ?)";
			$level = $this->db->query($sql, array($inputs[0], $inputs[0], $value['refering_id'], $value['refering_id']));
			if ($this->db->trans_status() === FALSE) {
				return -1; /* query error */
			}
			
			$level_res = $level->result_array();
			//echo ' ';
			//echo $level_res[0]['sender_level'];
			//echo ' ';
			
			if( $level_res[0]['sender_level'] === '1' OR $level_res[0]['sender_level'] === '3') {
				
				//echo 'here3';
				
				//get the medical records that the refering hcp has permission to view for the corresponding patient
				$sql = "SELECT *
					FROM permission P, medical_record M, patient_account PA
					WHERE PA.account_id = ? AND PA.account_id = M.patient_id AND
					      M.medical_rec_id = P.medical_rec_id AND P.account_id = ?";
				$query = $this->db->query($sql, array($inputs[0], $value['refering_id']));
				if ($this->db->trans_status() === FALSE) {
					return -1; /* query error */
				}
				
				$medical = $query->result_array();
				
				//MAGIC
				//for each medical record, allow is_refered hcp permission to view it
				foreach ($medical as $medical_rec) {
					
					//echo 'here4';
					
					$sql = "INSERT INTO permission (medical_rec_id, account_id, date_created)
						VALUES (?, ?, current_date)";
					$query = $this->db->query($sql, array($medical_rec['medical_rec_id'], $inputs[1]));
					if ($this->db->trans_status() === FALSE)
						return -1;
				}
			}
		}
		return 0;
	}


	/**
	 * Sets the connection patient-hcp to TRUE only if it was not
	 * already accepted.
	 * 
	 * @param
	 *   $inputs of the form array(account_id of requester, account_id of accepter)
	 * 
	 * @return
	 *  -1 in case of error in a query
	 *  -2 if the connection or referal does not exist
	 *  -3 if the connection was already accepted
	 *   0 if everything goes fine
	 * *
	function accept_patient_hcp($inputs) {
		$query = $this->db->query("SELECT * FROM connections
			WHERE sender_id = ? AND receiver_id = ?", $inputs);
		
		if ($this->db->trans_status() === FALSE) {
			return -1; /* query error *
		}
		
		if ($query->num_rows() < 1) {
			return -2; /* connection does not exist *
		}
		
		//fix later for adding additional permissions
		$res = $query->result();
		if ($res[0]->accepted == 't') {
			return -3; /* connection alreaday accepted *
		}
		
		// Accept connection
		$this->db->query("UPDATE connections SET accepted = TRUE
			WHERE sender_id = ? AND receiver_id = ?", $inputs);
		
		if ($this->db->trans_status() === FALSE) {
			return -1; /* query error *
		}
		
		//if the connection came via referal, copy permissions from refering hcp to the is_refered hcp
		$sql = "SELECT * 
			FROM refers R
			WHERE R.patient_id = ? AND R.is_refered_id = ?"
		$result = $this->db->query($sql, $inputs);
		
		if ($this->db->trans_status() === FALSE) {
			return -1; /* query error *
		}
		if ($result->num_rows() < 1) {
			return -2; /* connection does not exist *
		}
		$result = $result->result_array();
		
		
		//copy if it came via referal

		foreach ($result as $value){
			
			$sql = "SELECT *
				FROM permission P, medical_record M, patient_account PA
				WHERE PA.account_id = ? AND P.account_id = ? AND PA.account_id = M.patient_id AND P.medical_rec_id = M.medical_rec_id"
			$query = $this->db->query($sql, $value['patient_id'], $value['is_refered_id'])
			
			$sql = "INSERT INTO permission (medical_rec_id, account_id, date_created)
				VALUES (?, ?, current_date)";
			$query = $this->db->query($sql, $inputs);
			if ($this->db->trans_status() === FALSE)
				return -1;
		}
		
		return 0;
	}*/
	
	/**
	 * Removes a connection between any pair of account_ids.
	 * 
	 * @param $inputs
	 *   Is of the form: array(A_id, B_id)
	 * @return
	 *  -1 in case of error in a query
	 *  -2 if the connection does not exist
	 *   0 if everything goes fine and the connection from the P_D_Connection table is removed
	 * */
	function remove_connection($a_id, $b_id) {
		// Check if the connection exists
		$check = $this->is_connected_with($a_id, $b_id);
		if ($check === -1) return -1;
		if ($check === FALSE) return -2;
		
		//remove medical records that the the hcp had permission to view
		$sql = "SELECT  PR.permission_id 
			FROM patient_account P, medical_record M, permission PR, accounts A 
			WHERE (P.account_id = ? OR P.account_id = ?) AND P.account_id = M.patient_id AND 
				  M.medical_rec_id = PR.medical_rec_id AND PR.account_id = A.account_id AND 
				  (A.account_id = ? OR A.account_id = ?)";
		$query = $this->db->query($sql, array($a_id, $b_id, $a_id, $b_id));
		if ($this->db->trans_status() === FALSE)
			return -1;
			
		$medical_rec_ids = $query->result_array();	
		
		//delete from the permission table
		foreach ($medical_rec_ids AS $ID) {
			
			$sql = "DELETE FROM permission
				WHERE permission_id = ?";
			$query = $this->db->query($sql, $ID['permission_id']);
			if ($this->db->trans_status() === FALSE)
				return -1;
		}
			
		// Now, delete the connection
		$sql = "DELETE FROM connections
				WHERE (sender_id = ? AND receiver_id = ?) OR (receiver_id = ? AND sender_id = ?)";
		$query = $this->db->query($sql, array($a_id, $b_id, $a_id, $b_id));
				
		//$sql = "DELETE FROM p_d_connection
		//		WHERE (patient_id = ? AND hcp_id = ?) OR (hcp_id = ? AND patient_id = ?)";
		//$query = $this->db->query($sql, array($a_id, $b_id, $a_id, $b_id));
		
		if ($this->db->trans_status() === FALSE)
			return -1;
		
		return 0; // Success
	}
	
	/**
	 * Removes a pending connection between any pair of account_ids.
	 * 
	 * @param $inputs
	 *   Is of the form: array(A_id, B_id)
	 * @return
	 *  -1 in case of error in a query
	 *  -2 if the connection does not exist
	 *   0 if everything goes fine and the connection from the P_D_Connection table is removed
	 * */
	function remove_pending($a_id, $b_id) {
		// Check if the connection exists
		$check = $this->is_pending_with($a_id, $b_id);
		if ($check === -1) return -1;
		if ($check === FALSE) return -2;
		
		// Now, delete the connection
		$sql = "DELETE FROM connections
				WHERE (sender_id = ? AND receiver_id = ?) OR (receiver_id = ? AND sender_id = ?)";
		$query = $this->db->query($sql, array($a_id, $b_id, $a_id, $b_id));
		
		//$sql = "DELETE FROM p_d_connection
		//		WHERE (patient_id = ? AND hcp_id = ?) OR (hcp_id = ? AND patient_id = ?)";
		//$query = $this->db->query($sql, array($a_id, $b_id, $a_id, $b_id));

		if ($this->db->trans_status() === FALSE)
			return -1;
		
		return 0; // Success
	}
	
	/**
	 * Gives the connection level between a patient and a hcp
	 * 
	 * @param $inputs
	 *   Is of the form: array(sender account_id, accepter account_id)
	 * @return
	 *  -1 in case of error in a query
	 *  -2 if the connection does not exist
	 *   array with the connection level
	 * */
	 function get_level($inputs) {
		
		$sql = "SELECT C.sender_level
			FROM connections C
			WHERE C.sender_id = ? AND C.receiver_id = ?";
		$query = $this->db->query($sql, array($inputs[0], $inputs[1]));
		
		if ($this->db->trans_status() === FALSE)
			return -1;
		
		if ($query->num_rows() > 0){	
			$level = $query->result_array();
			return $level[0];
		}
	 }
	
	
	/**
	 * Removes a conection between a patient and a hcp
	 * 
	 * @param $inputs
	 *   Is of the form: array(A_id, B_id)
	 * @return
	 *  -1 in case of error in a query
	 *  -2 if the connection does not exist
	 *   0 if everything goes fine and the connection from the P_D_Connection table is removed
	 * *
	function remove_pd_connection($inputs){
		
		//test to see if connection exists
		$sql = "SELECT *
				FROM p_d_connection
				WHERE (patient_id = ? AND hcp_id = ?) OR (patient_id = ? AND hcp_id = ?)";
		$query = $this->db->query($sql, array($inputs[0], $inputs[1], $inputs[1], $inputs[0]));
		
		if ($this->db->trans_status() === FALSE)
			return -1;
			
		if ($query->num_rows() < 1)
			return -2;	
		
		$sql = "DELETE FROM p_d_connection
				WHERE (patient_id = ? AND hcp_id = ?) OR (patient_id = ? AND hcp_id = ?)";
		$query = $this->db->query($sql, array($inputs[0], $inputs[1], $inputs[1], $inputs[0]));
		
		if ($this->db->trans_status() === FALSE)
			return -1;
			
		return 0;
	}
	
	/**
	 * Removes a conection between a hcp and a hcp
	 * 
	 * @param $inputs
	 *   Is of the form: array(A_id, B_id)
	 * @return
	 *  -1 in case of error in a query
	 *  -2 if the connection does not exist
	 *   0 if everything goes fine and the connection from the D_D_Connection table is removed
	 * *
	function remove_dd_connection($inputs){
		
		//test to see if connection exists
		$sql = "SELECT *
				FROM d_d_connection
				WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)";
		$query = $this->db->query($sql, array($inputs[0], $inputs[1], $inputs[1], $inputs[0]));
		
		if ($this->db->trans_status() === FALSE)
			return -1;
			
		if ($query->num_rows() < 1)
			return -2;
		
		$sql = "DELETE FROM d_d_connection
				WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)";
		$query = $this->db->query($sql, array($inputs[0], $inputs[1], $inputs[1], $inputs[0]));
		
		if ($this->db->trans_status() === FALSE)
			return -1;	
			
		return 0;
	}*/
}
/**@}*/
?>

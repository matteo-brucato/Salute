<?php
class Messages_model extends Model{
	
	function __construct() {
		parent::Model();
		$this->load->database();
	}

	
	/**
	 * View all messages received
	 * 
	 * @param $inputs
	 *   Is of the form: array(account_id)
	 * @return
	 *   -1 if error in query
	 *   empty array if no messages
	 *   array of messges
	 * */
	function query_view_all($inputs){ 
	
		$sql = "SELECT M.message_id, A1.email, M.subject, M.content, M.datetime 
			FROM messages M, accounts A0, accounts A1
			WHERE A0.account_id = M.receiver_id AND M.receiver_id = ? AND M.sender_id = A1.account_id";
		$query = $this->db->query($sql, $inputs);
		$result = $query->result_array();
		if ($this->db->trans_status() === FALSE)
			return -1;	
		if( $query->num_rows() > 0){
			$result = $query->result_array();
			return $result;
		}
		return array();	
	}



	//query_view_sent
	//I assume inputs will be of the form (account_id)
	//returns an array of tuples or NULL	
	/**
	 * View all messages sent
	 * 
	 * @param $inputs
	 *   Is of the form: array(account_id)
	 * @return
	 *   -1 if error in query
	 *   empty array if no messages
	 *   array of messges
	 * */
	function query_view_sent($inputs){ 
		$sql = "SELECT M.message_id, A1.email, M.subject, M.content, M.datetime 
			FROM messages M, accounts A0, accounts A1
			WHERE A0.account_id = M.sender_id AND M.sender_id = ? AND M.receiver_id = A1.account_id";
		$query = $this->db->query($sql, $inputs);
		if ($this->db->trans_status() === FALSE)
			return -1;	
		if( $query->num_rows() > 0){
			$result = $query->result_array();
			return $result;
		}
		return array();		
	}//end function query_view_sent


	/**
	 * Create a new message
	 * 
	 * @param $inputs
	 *   Is of the form: array(account_id of sender, account_id of receiver, subject, content, timestamp)
	 * @return
	 *   -1 if error in insert
	 *   1 otherwise
	 * */
	function create_new($inputs){ 
		// INSERT INTO MESSAGES IFF THERE EXISTS 1 TUPLE WITH AT LEAST ONE ENTRY IN P_D_CONNECTION OR D_D_CONNECTION
		$sql = "INSERT INTO messages( sender_id, receiver_id, subject, content, datetime)
			VALUES ( ?, ?, ?, ?, ? )
			WHERE EXISTS (
					(SELECT * 
					FROM p_d_CONNECTION P
					WHERE ((P.patient_id = ? AND P.hcp_id = ?) OR (P.patient_id = ? AND P.hcp_id = ?)) AND P.accepted = TRUE)
					UNION
					(SELECT * 
					FROM d_d_CONNECTION D
					WHERE ((D.requester_id = ? AND D.requester_id = ?) OR (D.request_id = ? AND D.requester_id = ?)) AND P.accepted = TRUE)
			)";
		$query = $this->db->query($sql, array($inputs[0], $inputs[1], $inputs[2], $inputs[3], $inputs[4],$inputs[0],$inputs[1],$inputs[1],$inputs[0],$inputs[0],$inputs[1],$inputs[1],$inputs[0] ));
		if ($this->db->trans_status() === FALSE)
			return -1;
		return 1;
	}

	/**
	 * Delete a message
	 * 
	 * @param $inputs
	 *   Is of the form: array(message_id, account_id of deleter)
	 * @return
	 *   -1 if error in deletion
	 *   1 otherwise
	 * */
	function delete($inputs){
	}
	
}
?>



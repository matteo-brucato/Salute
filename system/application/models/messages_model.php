<?php
class Messages_model extends Model{
	
	function __construct() {
		parent::Model();
		$this->load->database();
	}

	//query_view_all
	//I assume inputs will be of the form (account_id)
	//returns an array of tuples or NULL	
	function query_view_all($inputs){ 
	
		$sql = "SELECT M.message_id, A1.email, M.subject, M.content, M.datetime 
			FROM Messages M, Accounts A0, Accounts A1
			WHERE A0.account_id = M.receiver_id AND M.receiver_id = ? AND M.sender_id = A1.account_id";
		$query = $this->db->query($sql, $inputs);
		$result = $query->result_array();
		if( count($result) > 0 )
			return $result;
		else
			return NULL;		
	}//end function query_view_all



	//query_view_sent
	//I assume inputs will be of the form (account_id)
	//returns an array of tuples or NULL	
	function query_view_all($inputs){ 
		$sql = "SELECT M.message_id, A1.email, M.subject, M.content, M.datetime 
			FROM Messages M, Accounts A0, Accounts A1
			WHERE A0.account_id = M.sender_id AND M.sender_id = ? AND M.receiver_id = A1.account_id";
		$query = $this->db->query($sql, $inputs);
		$result = $query->result_array();
		if( count($result) > 0 )
			return $result;
		else
			return NULL;		
	}//end function query_view_sent

	//I assume inputs will be of the form (account_id of sender, account_id of receiver, subject, content, timestamp)
	//return an array of error messages(receiver doesn't exist, message didn't send)
	function create_new($inputs){ 
		// INSERT INTO MESSAGES IFF THERE EXISTS 1 TUPLE WITH AT LEAST ONE ENTRY IN P_D_CONNECTION OR D_D_CONNECTION
		$sql = "INSERT INTO Messages( sender_id, receiver_id, subject, content, datetime)
			VALUES ( ?, ?, ?, ?, ? )
			WHERE EXISTS (
					(SELECT * 
					FROM P_D_CONNECTION P
					WHERE ((P.patient_id = ? AND P.hcp_id = ?) OR (P.patient_id = ? AND P.hcp_id = ?)) AND P.accepted = TRUE)
					UNION
					(SELECT * 
					FROM D_D_CONNECTION D
					WHERE ((D.requester_id = ? AND D.requester_id = ?) OR (D.request_id = ? AND D.requester_id = ?)) AND P.accepted = TRUE)
			)";
		$query = $this->db->query($sql, array($inputs[0], $inputs[1], $inputs[2], $inputs[3], $inputs[4],$inputs[0],$inputs[1],$inputs[1],$inputs[0],$inputs[0],$inputs[1],$inputs[1],$inputs[0] ));
		$result = $query->result_array();
				
		return array($result, NULL);

	}

	 //I assume inputs will be of the form( account_id, message_id )
	function delete($inputs){
	}
	
}
?>

SELECT M.message_id, H.first_name, M.subject, M.content, M.datetime FROM Messages M, Account A, HCP_Account H WHERE A.account_id = M.receiver_id AND M.receiver_id = ? AND M.sernder_id = H.account_id


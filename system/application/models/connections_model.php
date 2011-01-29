<?php
class Connections_model extends Model {
	function __construct() {
		parent::Model();
		$this->load->database();
	}

	//$inputs is of the form(account_id)
	//return array of all patients a doctor has
	function list_my_patients($inputs) {
		$sql = "SELECT *
			FROM p_d_connection D, patient_account P
			WHERE (D.patient_id =  P.account_id) AND D.hcp_id = ?";
		$query = $this->db->query($sql, $inputs);
		return $query->result_array();
		
	}

	//$inputs is of the form(account_id)
	//return array of all doctor friends a doctor has
	function list_my_colleagues($account_id) {
		$sql = "SELECT *
			FROM d_d_connection D, hcp_account H
			WHERE (D.requester_id = ? AND D.accepter_id = H.account_id)
			OR    (D.accepter_id = ? AND D.requester_id = H.account_id)";
		$query = $this->db->query($sql, array($account_id, $account_id));
		return $query->result_array();
	
	}	

	//$inputs is of the form(account_id)
	//returns array of all doctors a pattient has
	function list_my_doctors($inputs) {
		$sql = "SELECT *
			FROM p_d_connection D, hcp_account H
			WHERE (D.patient_id =  ?) AND H.account_id = D.hcp_id";
		$query = $this->db->query($sql, $inputs);
		return $query->result_array();
	}
	
	
	/**
	 * List all pending request for a patient account
	 * 
	 * @param $inputs
	 *   Is of the form: array(account_id)
	 * @return
	 *   Array with all pending requests, NULL otherwise
	 * */
	 function pending_pd($inputs)
	 {
		$sql = "SELECT H.first_name H.last_name
	 		FROM p_d_connection P, hcp_account H
			WHERE P.patient_id = ? AND AND P.accepted = FALSE AND P.hcp_id = H.account_id";
 		$query = $this->db->query($sql, $inputs);
 		$result = $query->result_array();
		if (count($result) >= 1)
			return $result;
		return NULL;

	 }
	 
	 
	 /**
	 * List all pending request for a doctor account
	 * 
	 * @param $inputs
	 *   Is of the form: array(account_id)
	 * @return
	 *   Array with all pending requests, NULL otherwise
	 * */
	 //NOT DONE
	 function pending_dd($inputs)
	 {
		$sql = "SELECT H.first_name H.last_name
	 		FROM d_d_connection D, hcp_account H
			WHERE (D.requester_id = ? OR D.accepter_id = ? )AND D.accepted = FALSE AND (P.requester_id = H.account_id) OR";
 		$query = $this->db->query($sql, $inputs);
 		$result = $query->result_array();
		if (count($result) >= 1)
			return $result;
		return NULL;

	 }


	/**
	 * Tells whether an account is in connection with another account
	 * @return
	 *   TRUE if $a_id is in connection with $b_id, FALSE otherwise
	 * */
	function is_connected_with($a_id, $b_id) {
		$sql = "(SELECT *
				FROM p_d_connection PD
				WHERE  (PD.hcp_id = ? AND PD.patient_id = ?)
					OR (PD.patient_id = ? AND PD.hcp_id = ?))
				UNION
				(SELECT *
				FROM d_d_connection DD
				WHERE  (DD.requester_id = ? AND DD.accepter_id = ?)
					OR (DD.accepter_id = ? AND DD.requester_id = ?))";
		$query = $this->db->query($sql, array($a_id, $b_id,
											  $a_id, $b_id,
											  $a_id, $b_id,
											  $a_id, $b_id,));
		return (count($query->result_array()) > 0);
	}
	
	/**
	 * Creates a new request for a connection.
	 * 
	 * @param
	 *   $inputs of the form array(requestor_id, requestee_id)
	 * 
	 * @todo This function should handle errors as accept_doctor_doctor
	 * does, that is, using the same error codes and error cases.
	 * 
	 * @test We still need to test the auto-acceptance if both doctors
	 * ask for the same connection.
	 * */
	function add_doctor_doctor($inputs) {
		//testing to see if requestor is sending 2nd request
		$sql = "SELECT *
			FROM d_d_connection D
			WHERE (D.requester_id = ? AND D.accepter_id = ?)";
		$query = $this->db->query($sql, $inputs);
		
		if ($query->num_rows() > 0) {
			return FALSE;
		}
		
		//testing to see if doctor_loggedin is sending request to doctor who
		//already sent doctor_loggedin a request
		$sql = "SELECT *
			FROM d_d_connection D
			WHERE (D.requester_id = ? AND D.accepter_id = ?)";
		$query = $this->db->query($sql, array($inputs[1], $inputs[0]));
		if ($query->num_rows() > 0) {
			//this is an update to the original request
			return accept_doctor_doctor($inputs); /** @todo Needs to be tested */
			//$data = array( 'accepted'=> TRUE );
			//$this->db->update('D_D_Connection', $data, array('requester_id' => $inputs[1], 'accepter_id' => $inputs[0]));
			//return TRUE;
		}

		//request has never been made in either direction
		//$data = array( 'requester_id' => $inputs[0], 
		//	'accepter_id' => $inputs[1],
		//	'date_connection'=> $inputs[2]
		//);
				
		//$this->db->insert('D_D_Connection', $data );
		
		$this->db->query("INSERT INTO d_d_connection (requester_id, accepter_id, date_connected)
			VALUES (?, ?, current_timestamp)", $inputs);
		return TRUE;
	}
	
	//$inputs of the form (account_id of patient, account_id of HCP)
	function add_patient_doctor($inputs){
		$sql = "SELECT *
			FROM p_d_connection D
			WHERE D.patient_id = ? AND D.hcp_id = ?";
		$query = $this->db->query($sql, $inputs);
		
		if ($query->num_rows() > 0) {
			return FALSE;
		}
		
		/*$data = array(
			'patient_id' => $inputs[0], 
			'hcp_id' => $inputs[1]
		);*/
		
		//$this->db->insert('P_D_Connection', $data);
		
		$this->db->query("INSERT INTO p_d_connection (patient_id, hcp_id, date_connected)
			VALUES (? , ?, current_timestamp)", $inputs);
		return TRUE;
	}

	/**
	 * Sets the connection doctor-doctor to TRUE only if it was not
	 * already accepted.
	 * 
	 * @param
	 *   $inputs of the form array(account_id of requester, account_id of accepter)
	 * 
	 * @return
	 *   -1 in case of error in a query
	 *   -2 if the connection does not exist
	 *   -3 if the connection was already accepted
	 *   0  if everything goes fine
	 * */
	function accept_doctor_doctor($inputs) {
		$query = $this->db->query("SELECT * FROM d_d_connection
			WHERE requester_id = ? AND accepter_id = ?", $inputs);
		
		if ($this->db->trans_status() === FALSE) {
			return -1; /* query error */
		}
		
		if ($query->num_rows() < 1) {
			return -2; /* connection does not exist */
		}
		
		$res = $query->result();
		if ($res[0]->accepted == 't') {
			return -3; /* connection alreaday accepted */
		}
		
		// Accept connection
		$this->db->query("UPDATE d_d_connection SET accepted = TRUE
			WHERE requester_id = ? AND accepter_id = ?", $inputs);
		
		if ($this->db->trans_status() === FALSE) {
			return -1; /* query error */
		}
		
		return 0;
	}

	/**
	 * Sets the connection patient-doctor to TRUE only if it was not
	 * already accepted.
	 * 
	 * @param
	 *   $inputs of the form array(account_id of requester, account_id of accepter)
	 * 
	 * @return
	 *   -1 in case of error in a query
	 *   -2 if the connection does not exist
	 *   -3 if the connection was already accepted
	 *   0  if everything goes fine
	 * */
	function accept_patient_doctor($inputs) {
		$query = $this->db->query("SELECT * FROM p_d_connection
			WHERE patient_id = ? AND hcp_id = ?", $inputs);
		
		if ($this->db->trans_status() === FALSE) {
			return -1; /* query error */
		}
		
		if ($query->num_rows() < 1) {
			return -2; /* connection does not exist */
		}
		
		$res = $query->result();
		if ($res[0]->accepted == 't') {
			return -3; /* connection alreaday accepted */
		}
		
		// Accept connection
		$this->db->query("UPDATE p_d_connection SET accepted = TRUE
			WHERE patient_id = ? AND hcp_id = ?", $inputs);
		
		if ($this->db->trans_status() === FALSE) {
			return -1; /* query error */
		}
		
		return 0;
	}
	
	
	/**
	 * Removes a conection between a patient and a doctor
	 * 
	 * @param $inputs
	 *   Is of the form: array(A_id, B_id)
	 * @return
	 *   Removes the connection from the P_D_Connection table
	 * */
	function remove_pd_connection($inputs){
		
		$sql = "DELETE FROM p_d_connection
			WHERE (patient_id = ? AND hcp_id = ?) OR (patient_id = ? AND hcp_id = ?)";
		$query = $this->db->query($sql, $inputs[0], $inputs[1], $inputs[1], $inputs[0]);
		
	}
	
	/**
	 * Removes a conection between a doctor and a doctor
	 * 
	 * @param $inputs
	 *   Is of the form: array(A_id, B_id)
	 * @return
	 *   Removes the connection from the D_D_Connection table
	 * */
	function remove_dd_connection($inputs){
	
		$sql = "DELETE FROM d_d_connection
			WHERE (requester_id = ? AND accepter_id = ?) OR (requester_id = ? AND accepter_id = ?)";
		$query = $this->db->query($sql, $inputs[0], $inputs[1], $inputs[1], $inputs[0]);
	}
}
?>

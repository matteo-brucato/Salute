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
			FROM P_D_Connection D, Patient_Account P
			WHERE (D.patient_id =  P.account_id) AND D.hcp_id = ?";
		$query = $this->db->query($sql, $inputs);
		return $query->result_array();
		
	}

	//$inputs is of the form(account_id)
	//return array of all doctor friends a doctor has
	function list_my_colleagues($account_id) {
		$sql = "SELECT *
			FROM D_D_Connection D, HCP_Account H
			WHERE (D.requester_id = ? AND D.accepter_id = H.account_id)
			OR    (D.accepter_id = ? AND D.requester_id = H.account_id)";
		$query = $this->db->query($sql, array($account_id, $account_id));
		return $query->result_array();
	
	}	

	//$inputs is of the form(account_id)
	//returns array of all doctors a pattient has
	function list_my_doctors($inputs) {
		$sql = "SELECT *
			FROM P_D_Connection D, HCP_Account H
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
	 		FROM P_D_Connection P, HCP_Account H
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
	 		FROM D_D_Connection D, HCP_Account H
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
				FROM P_D_Connection PD
				WHERE  (PD.hcp_id = ? AND PD.patient_id = ?)
					OR (PD.patient_id = ? AND PD.hcp_id = ?))
				UNION
				(SELECT *
				FROM D_D_Connection DD
				WHERE  (DD.requester_id = ? AND DD.accepter_id = ?)
					OR (DD.accepter_id = ? AND DD.requester_id = ?))";
		$query = $this->db->query($sql, array($a_id, $b_id,
											  $a_id, $b_id,
											  $a_id, $b_id,
											  $a_id, $b_id,));
		return (count($query->result_array()) > 0);
	}
	
	//$inputs of the form (requestor_id, requestee_id, timestamp)
	function add_doctor_doctor($inputs){
		//testing to see if requestor is sending 2nd request			
		$sql = "SELECT *
			FROM D_D_Connection D
			WHERE (D.requester_id = ? AND D.acceptor_id = ?)";
		$query = $this->db->query($sql, array($inputs[0], $inputs[1]));
		$result = $query->result_array();
		if( count($result) > 0 )
			return FALSE;
		
		//testing to see if doctor_loggedin is sending request to doctor who
		//already sent doctor_loggedin a request
		$sql = "SELECT *
			FROM D_D_Connection D
			WHERE (D.requester_id = ? AND D.acceptor_id = ?)";
		$query = $this->db->query($sql, array($inputs[1], $inputs[0]));
		$result = $query->result_array();
		if( count($result) > 0 ){
			//this is an update to the original request
			$data = array( 'accepted'=> TRUE );
			$this->db->update('D_D_Connection', $data, array('requester_id' => $inputs[1], 'accepter_id' => $inputs[0]));
			return TRUE;
		}

		//request has never been made in either direction
		$data = array( 'requester_id' => $inputs[0], 
				'accepter_id' => $inputs[1],
				'date_connection'=> $inputs[2]
				);				
				
		$this->db->insert('D_D_Connection', $data );
	}
	
	//$inputs of the form (account_id of patient, account_id of HCP, timestamp)
	function add_patient_doctor($inputs){
		$sql = "SELECT *
			FROM P_D_Connection D
			WHERE D.patient_id = ? AND D.hcp_id = ?";
		$query = $this->db->query($sql, array($inputs[0], $inputs[1]));
		$result = $query->result_array();
		if( count($result) > 0 )
			return FALSE;
		$data = array( 'patient_id' => (int)$inputs[0], 
				'hcp_id' => (int)$inputs[1]
				);				
				
		//$this->db->insert('P_D_Connection', $data);
		
		$this->db->query("INSERT INTO P_D_Connection (patient_id, hcp_id, date_connected)
			VALUES (? , ?, current_timestamp)", $data);
		return TRUE;
		 
	}	

	//$inputs of the form (account_id of doctor1, account_id of doctor2)
	function accept_doctor_doctor($inputs){
		
		$data = array( 'accepted'=> TRUE );
		$this->db->update('D_D_Connection', $data, array('requester_id' => $inputs[1], 'accepter_id' => $inputs[0]));
		
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
		$query = $this->db->query("SELECT * FROM P_D_Connection
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
		$this->db->query("UPDATE P_D_Connection SET accepted = TRUE
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
	 *   Is of the form: array(patient_id, hcp_id)
	 * @return
	 *   Removes the connection from the P_D_Connection table
	 * */
	function remove_pd_connection($inputs){
	
		$this->db->delete('P_D_Connection', array('patient_id' => $inputs[0], 'hcp_id' => $inputs[1]));
		
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
	
		$sql = "DELETE FROM D_D_Connection
			WHERE (requester_id = ? AND accepter_id = ?) OR (requester_id = ? AND accepter_id = ?)";
		$query = $this->db->query($sql, $inputs[0], $inputs[1], $inputs[1], $inputs[0]);
	}
}
?>

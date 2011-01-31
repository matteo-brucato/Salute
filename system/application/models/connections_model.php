<?php
/**
 * @file connections_model.php
 * @brief Model to give access to the two connection tables in the database
 *
 * @defgroup mdl Model
 * @ingroup mdl
 * @{
 */

class Connections_model extends Model {
	function __construct() {
		parent::Model();
		$this->load->database();
	}


	/**
	 *  Lists all of the patients a particular doctor has
	 * 
	 * @param $inputs
	 *   Is of the form: array(account_id)
	 * @return
	 *   -1 in case of error in a query
	 * 	  Array with all the patients a doctor has
	 * 	  NULL if none
	 * */
	function list_my_patients($inputs) {
		$sql = "SELECT *
			FROM p_d_connection D, patient_account P
			WHERE (D.patient_id =  P.account_id) AND D.hcp_id = ?";
		$query = $this->db->query($sql, $inputs);
		
		if ($this->db->trans_status() === FALSE)
			return -1;
			
		if ($query->num_rows() > 0)
			return $query->result_array();
		
		return NULL;
	}


	/**
	 *  Lists all of the doctor friends a doctor has
	 * 
	 * @param $inputs
	 *   Is of the form: array(account_id)
	 * @return
	 *   -1 in case of error in a query
	 * 	  Array with all the doctor friends
	 * 	  NULL if none
	 * */
	function list_my_colleagues($account_id) {
		$sql = "SELECT *
			FROM d_d_connection D, hcp_account H
			WHERE (D.requester_id = ? AND D.accepter_id = H.account_id)
			OR    (D.accepter_id = ? AND D.requester_id = H.account_id)";
		$query = $this->db->query($sql, array($account_id, $account_id));
		
		if ($this->db->trans_status() === FALSE)
			return -1;
			
		if ($query->num_rows() > 0)	
			return $query->result_array();
			
		return NULL;
	}	


	/**
	 *  Lists all of the doctors a patient has
	 * 
	 * @param $inputs
	 *   Is of the form: array(account_id)
	 * @return
	 *   -1 in case of error in a query
	 * 	  Array of all the doctor friends
	 *    NULL if none
	 * */
	function list_my_doctors($inputs) {
		$sql = "SELECT *
			FROM p_d_connection D, hcp_account H
			WHERE (D.patient_id =  ?) AND H.account_id = D.hcp_id";
		$query = $this->db->query($sql, $inputs);
		
		if ($this->db->trans_status() === FALSE)
			return -1;
			
		if ($query->num_rows() > 0)	
			return $query->result_array();
		
		return NULL;
	}
	
	
	/**
	 * Lists all pending requests to doctors from a patient
	 * 
	 * @param $inputs
	 *   Is of the form: array(account_id)
	 * @return	
	 *   -1 in case of error in a query
	 *    Array with all pending requests
	 * 	  NULL if none
	 * */
	 function pending_todoctors_frompatient($inputs)
	 {
		$sql = "SELECT H.first_name H.last_name
	 		FROM p_d_connection P, hcp_account H
			WHERE P.patient_id = ? AND AND P.accepted = FALSE AND P.hcp_id = H.account_id";
 		$query = $this->db->query($sql, $inputs);
 		
 		if ($this->db->trans_status() === FALSE)
			return -1;
		
		if ($query->num_rows() > 0)
			return $query->result_array();
		
		return NULL;

	 }
	 
	 
	 /**
	 * List all pending requests to a doctor from patients
	 * 
	 * @param $inputs
	 *   Is of the form: array(account_id)
	 * @return
	 *   -1 in case of error in a query
	 *    Array with all pending requests
	 * 	  NULL if none
	 * */
	 function pending_todoctor_frompatients($inputs)
	 {
		$sql = "SELECT A.first_name, A.last_name
	 		FROM p_d_connection P, patient_account A
			WHERE P.hcp_id = ? AND P.accepted = FALSE AND A.account_id = P.patient_id";
 		$query = $this->db->query($sql, $inputs);
 		$result = $query->result_array();
	
		if ($this->db->trans_status() === FALSE)
			return -1;
		
		if ($query->num_rows() > 0)
			return $query->result_array();
		
		return NULL;
	 }
	 
	 
	 /**
	 * List all pending requests to a doctor from doctors
	 * 
	 * @param $inputs
	 *   Is of the form: array(account_id)
	 * @return
	 * 	 -1 in case of error in a query
	 *    Array with all pending requests OR 
	 * 	  NULL if none
	 * */
	 function pending_todoctor_fromdoctors($inputs)
	 {
		$sql = "SELECT A.first_name, A.last_name
	 		FROM d_d_connection D, hcp_account A
			WHERE D.accepter_id = ? AND D.accepted = FALSE AND A.account_id = D.requester_id";
 		$query = $this->db->query($sql, $inputs);
 		$result = $query->result_array();
		
		if ($this->db->trans_status() === FALSE)
			return -1;
		
		if ($query->num_rows() > 0)
			return $query->result_array();
		
		return NULL;
	 }
	 
	 
	 /**
	 * List all pending requests to doctors from a doctor
	 * 
	 * @param $inputs
	 *   Is of the form: array(account_id)
	 * @return
	 * 	 -1 in case of error in a query
	 *    Array with all pending requests
	 * 	  NULL if none	 
	 * */
	 function pending_todoctors_fromdoctor($inputs)
	 {
		$sql = "SELECT A.first_name, A.last_name
	 		FROM d_d_connection D, hcp_account A
			WHERE D.requester_id = ? AND D.accepted = FALSE AND A.account_id = D.accepter_id";
 		$query = $this->db->query($sql, $inputs);
	
		if ($this->db->trans_status() === FALSE)
			return -1;
		
		if ($query->num_rows() > 0)
			return $query->result_array();
		
		return NULL;
	 }
	

	/**
	 * Tells whether an account is in connection with another account
	 * 
	 * @params $inputs
	 * 	 Is of the form: array(account_id)
	 * @return
	 * 	 -1 in case of error in a query
	 *    TRUE if $a_id is in connection with $b_id
	 *    FALSE otherwise
	 * */
	function is_connected_with($a_id, $b_id) {
		$sql = "(SELECT *
				FROM p_d_connection PD
				WHERE PD.accepted = true AND
					((PD.hcp_id = ? AND PD.patient_id = ?)
					OR (PD.patient_id = ? AND PD.hcp_id = ?)))
				UNION
				(SELECT *
				FROM d_d_connection DD
				WHERE DD.accepted = true AND
					((DD.requester_id = ? AND DD.accepter_id = ?)
					OR (DD.accepter_id = ? AND DD.requester_id = ?)))";
		$query = $this->db->query($sql, array($a_id, $b_id,
											  $a_id, $b_id,
											  $a_id, $b_id,
											  $a_id, $b_id,));
											  
		if ($this->db->trans_status() === FALSE)
			return -1;
			
		if ($query->num_rows() > 0)
			return ($query->num_rows() > 0);
		
		return FALSE;
	}
	
	
	/**
	 * Creates a new request for a connection.
	 * 
	 * @param
	 *   $inputs of the form array(requestor_id, requestee_id)
	 *
	 * @return
	 *   -1 in case of error in a query
	 *   -3 if the connection already exists
	 *    0 if everything goes fine
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
		
		if ($this->db->trans_status() === FALSE)
			return -1;
		
		if ($query->num_rows() > 0) {
			return -3;
		}
		
		//testing to see if doctor_loggedin is sending request to doctor who
		//already sent doctor_logged in a request
		$sql = "SELECT *
			FROM d_d_connection D
			WHERE (D.requester_id = ? AND D.accepter_id = ?)";
		$query = $this->db->query($sql, array($inputs[1], $inputs[0]));
		
		if( $this->db->trans_status() === FALSE )
			return -1;	
		
		if ($query->num_rows() > 0) {
			//this is an update to the original request
			return accept_doctor_doctor(array($inputs[1], $inputs[0])); /** @todo Needs to be tested */
			
			if( $this->db->trans_status() === FALSE )
				return -1;
				
			return 0;
			
			//$data = array( 'accepted'=> TRUE );
			//$this->db->update('D_D_Connection', $data, array('requester_id' => $inputs[1], 'accepter_id' => $inputs[0]));
			//return TRUE;
		}

		//request has never been made in either direction	
		$this->db->query("INSERT INTO d_d_connection (requester_id, accepter_id, date_connected)
				  VALUES (?, ?, current_timestamp)", $inputs);
		
		if( $this->db->trans_status() === FALSE )
			return -1;
			
		return 0;
	}
	
	/**
	 * Creates a new request for a patient doctor connection.
	 * 
	 * @param
	 *   $inputs of the form (account_id of patient, account_id of HCP)
	 *
	 * @return
	 *   -1 in case of error in a query
	 *   -3 if the connection was already accepted
	 *    0 if everything goes fine
	 * 
	 * */
	function add_patient_doctor($inputs){
	
		//test to see if connection already exists
		$sql = "SELECT *
				FROM p_d_connection D
				WHERE D.patient_id = ? AND D.hcp_id = ?";
		$query = $this->db->query($sql, $inputs);
		
		if ($this->db->trans_status() === FALSE) {
			return -1; 
		}
		
		if ($query->num_rows() > 0) {
			return -3;
		}
			
		$this->db->query("INSERT INTO p_d_connection (patient_id, hcp_id, date_connected)
			VALUES (? , ?, current_timestamp)", $inputs);
		
		if ($this->db->trans_status() === FALSE) {
			return -1; 
		}
		
		return 0;
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
	 *    0 if everything goes fine
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
	 *    0 if everything goes fine
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
	 *  -1 in case of error in a query
	 *  -2 if the connection does not exist
	 *   0 if everything goes fine and the connection from the P_D_Connection table is removed
	 * */
	function remove_pd_connection($inputs){
		
		//test to see if connection exists
		$sql = "SELECT *
				FROM p_d_connection
				WHERE (patient_id = ? AND hcp_id = ?) OR (patient_id = ? AND hcp_id = ?)";
		$query = $this->db->query($sql, $inputs[0], $inputs[1], $inputs[1], $inputs[0]);
		
		if ($this->db->trans_status() === FALSE)
			return -1;
			
		if ($query->num_rows() < 1)
			return -2;	
		
		$sql = "DELETE FROM p_d_connection
				WHERE (patient_id = ? AND hcp_id = ?) OR (patient_id = ? AND hcp_id = ?)";
		$query = $this->db->query($sql, $inputs[0], $inputs[1], $inputs[1], $inputs[0]);
		
		if ($this->db->trans_status() === FALSE)
			return -1;
			
		return 0;
	}
	
	/**
	 * Removes a conection between a doctor and a doctor
	 * 
	 * @param $inputs
	 *   Is of the form: array(A_id, B_id)
	 * @return
	 *  -1 in case of error in a query
	 *  -2 if the connection does not exist
	 *   0 if everything goes fine and the connection from the D_D_Connection table is removed
	 * */
	function remove_dd_connection($inputs){
		
		//test to see if connection exists
		$sql = "SELECT *
				FROM d_d_connection
				WHERE (requester_id = ? AND accepter_id = ?) OR (requester_id = ? AND accepter_id = ?)";
		$query = $this->db->query($sql, $inputs[0], $inputs[1], $inputs[1], $inputs[0]);
		
		if ($this->db->trans_status() === FALSE)
			return -1;
			
		if ($query->num_rows() < 1)
			return -2;
		
		$sql = "DELETE FROM d_d_connection
				WHERE (requester_id = ? AND accepter_id = ?) OR (requester_id = ? AND accepter_id = ?)";
		$query = $this->db->query($sql, $inputs[0], $inputs[1], $inputs[1], $inputs[0]);
		
		if ($this->db->trans_status() === FALSE)
			return -1;	
			
		return 0;
	}
}

?>

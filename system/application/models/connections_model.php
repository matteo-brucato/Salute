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
	function list_my_colleagues($inputs) {
		$sql = "SELECT *
			FROM D_D_Connection D, HCP_Account H
			WHERE (D.requester_id = H.account_id OR D.accepter_id = H.account_id) AND H.account_id = ?";
		$query = $this->db->query($sql, $inputs);
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

	//$inputs of the form (requestor_id, requestee_id, timestamp)
	function add_doctor_doctor($inputs){
		//testing to see if requestor is sending 2nd request			
		$sql = "SELECT *
			FROM D_D_Connection D
			WHERE (D.requester_id = ? AND D.acceptor_id = ?)";
		$query = $this->db->query($sql, array(inputs[0], inputs[1]));
		$result = $query->result_array();
		if( count($result) > 0 )
			return FALSE;
		
		//testing to see if doctor_loggedin is sending request to doctor who
		//already sent doctor_loggedin a request
		$sql = "SELECT *
			FROM D_D_Connection D
			WHERE (D.requester_id = ? AND D.acceptor_id = ?)";
		$query = $this->db->query($sql, array(inputs[1], inputs[0]));
		$result = $query->result_array();
		if( count($result) > 0 ){
			//this is an update to the original request
			$data = array( 'accepted'=> TRUE );
			this->db->update('D_D_Connection', $data, array(requester_id=>$inputs[1],accepter_id=>$inputs[0]));
			return TRUE;
		}

		//request has never been made in either direction
		$data = array( 'requester_id' => inputs[0], 
				'accepter_id' => inputs[1],
				'date_connection'=>inputs[2]
				);				
				
		$this->db->insert('D_D_Connection', $data );
		

	}
	//$inputs of the form (account_id of patient, account_id of HCP, timestamp)
	function add_patient_doctor($inputs){
		$sql = "SELECT *
			FROM P_D_Connection D
			WHERE D.patient_id = ? AND D.hcp_id = ?";
		$query = $this->db->query($sql, array(inputs[0], inputs[1]));
		$result = $query->result_array();
		if( count($result) > 0 )
			return FALSE;
		$data = array( 'patient_id' => inputs[0], 
				'hcp_id' => inputs[1],
				'date_connection'=>inputs[2]
				);				
				
		$this->db->insert('P_D_Connection', $data );
		return TRUE;
		 
	}	

	//$inputs of the form (account_id of doctor1, account_id of doctor2)
	function accept_doctor_doctor($inputs){
		
		$data = array( 'accepted'=> TRUE );
		this->db->update('D_D_Connection', $data, array(requester_id=>$inputs[1],accepter_id=>$inputs[0]));
		
	}

	//$inputs for the form (account_id of doctor, account_id of patient)
	function accept_patient_doctor($inputs)[
		$data = array( 'accepted'=> TRUE );
		this->db->update('P_D_Connection', $data, array('patient_id' => $inputs[1], 'hcp_id' =>$inputs[0]));
	}
	

}
?>



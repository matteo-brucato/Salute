<?php
class Bills_model extends Model {
	
	function __construct() {
		parent::Model();
		$this->load->database();
	}

	//view all bills a patient has received OR all bills a doctor has issued
	//I assume $inputs will be of the form (account_id, type of account(doctor or patient))
	//returns array with all bills OR NULL if there are no bills
	function view_all($inputs){
	
		//lists all bills a patient has received
		if( $inputs[1] == 'patient'){
			$sql = "Select B.bill_id, H2.first_name, H2.last_name, B.amount, B.descryption, B.due_date, B.cleared
				FROM Payment B, HCP_Account H, HCP_Account H2
				WHERE B.hcp_id = H.account_id AND B.patient_id = ? AND B.hcp_id == H2.account_id";
			$query = $this->db->query($sql, $inputs[0]);
			$result = $query->result_array();
			if( count($result) > 0 )
				return $result;

			return NULL;	
		}

		//lists all bills a doctor has issued
		$sql = "Select B.bill_id, P2.first_name, P2.last_name, B.amount, B.descryption, B.due_date, B.cleared
			FROM Payment B, Patient_Account P, Patient_Account P2
			WHERE B.patient_id = P.account_id AND B.hcp_id = ? AND B.patient_id == P2.account_id";
		$query = $this->db->query($sql, $inputs[0]);
		$result = $query->result_array();
		if( count($result) > 0 )
			return $result;

		return NULL;	
			
	}

	//view all CURRENT bills a patient has received OR all CURRENT bills a doctor has issued
	//I assume $inputs will be of the form (account_id, type of account(doctor or patient))
	//returns array with all bills OR NULL if there are no bills
	function view_current($inputs){

		//lists all current bills a patient has received
		if( $inputs[1] == 'patient'){
			$sql = "Select B.bill_id, H2.first_name, H2.last_name, B.amount, B.descryption, B.due_date, B.cleared
				FROM Payment B, HCP_Account H, HCP_Account H2
				WHERE B.hcp_id = H.account_id AND B.patient_id = ? AND B.hcp_id == H2.account_id AND B.due_date >= curdate()";
			$query = $this->db->query($sql, $inputs[0]);
			$result = $query->result_array();
			if( count($result) > 0 )
				return $result;
			
			return NULL;	
		}

		//list all current bills a doctor has issued
		$sql = "Select B.bill_id, P2.first_name, P2.last_name, B.amount, B.descryption, B.due_date, B.cleared
			FROM Payment B, Patient_Account P, Patient_Account P2
			WHERE B.patient_id = P.account_id AND B.hcp_id = ? AND B.patient_id == P2.account_id AND B.due_date >= curdate()";
		$query = $this->db->query($sql, $inputs[0]);
		$result = $query->result_array();
		if( count($result) > 0 )
			return $result;
			
		return NULL;	
	}


	//view all PAST bills a patient has received OR all PAST bills a doctor has issued
	//I assume $inputs will be of the form (account_id, type of account(doctor or patient))
	//returns array with all bills OR NULL if there are no bills
	function view_past($inputs){

		//lists all past bills a patient has received
		if( $inputs[1] == 'patient'){
			$sql = "Select B.bill_id, H2.first_name, H2.last_name, B.amount, B.descryption, B.due_date, B.cleared
				FROM Payment B, HCP_Account H, HCP_Account H2
				WHERE B.hcp_id = H.account_id AND B.patient_id = ? AND B.hcp_id == H2.account_id AND B.due_date < curdate()";
			$query = $this->db->query($sql, $inputs[0]);
			$result = $query->result_array();
			if( count($result) > 0 )
				return $result;
			
			return NULL;	
		}

		//list all past bills a doctor has issued
		$sql = "Select B.bill_id, P2.first_name, P2.last_name, B.amount, B.descryption, B.due_date, B.cleared
			FROM Payment B, Patient_Account P, Patient_Account P2
			WHERE B.patient_id = P.account_id AND B.hcp_id = ? AND B.patient_id == P2.account_id AND B.due_date < curdate()";
		$query = $this->db->query($sql, $inputs[0]);
		$result = $query->result_array();
		if( count($result) > 0 )
			return $result;
			
		return NULL;
	}

	//view all PAST bills a patient has received OR all PAST bills a doctor has issued THAT HAVE NOT CLEARED
	//I assume $inputs will be of the form (account_id, type of account(doctor or patient))
	//returns array with all bills OR NULL if there are no bills
	function view_past_not_cleared($inputs){

		//lists all past bills a patient has received that are NOT CLEARED
		if( $inputs[1] == 'patient'){
			$sql = "Select B.bill_id, H2.first_name, H2.last_name, B.amount, B.descryption, B.due_date, B.cleared
				FROM Payment B, HCP_Account H, HCP_Account H2
				WHERE B.hcp_id = H.account_id AND B.patient_id = ? AND B.hcp_id == H2.account_id AND B.due_date < curdate() AND B.cleared == FALSE";
			$query = $this->db->query($sql, $inputs[0]);
			$result = $query->result_array();
			if( count($result) > 0 )
				return $result;
			
			return NULL;	
		}

		//list all past bills a doctor has issued that are NOT CLEARED
		$sql = "Select B.bill_id, P2.first_name, P2.last_name, B.amount, B.descryption, B.due_date, B.cleared
			FROM Payment B, Patient_Account P, Patient_Account P2
			WHERE B.patient_id = P.account_id AND B.hcp_id = ? AND B.patient_id == P2.account_id AND B.due_date < curdate() AND B.cleared == FALSE";
		$query = $this->db->query($sql, $inputs[0]);
		$result = $query->result_array();
		if( count($result) > 0 )
			return $result;
			
		return NULL;
	}


	//doctor issues a new bill to patient
	//I assume $inputs will be of the form (patient_id, hcp_id, amount, descryption, due_date)
	//inserts the new bill in the Payments table
	//NOTE: NEED TO ADD ATTRIUTE TO THE DATABASE TO BE ABLE TO ADD ITEMIZED RECEIPT
	function issue_bill($inputs){

		$data = array( 'patient_id' => $inputs[0], 'hcp_id' => $inputs[1], 'amount' => $inputs[2], 'descryption' => $inputs[3], 'due_date' => $inputs[4]);
		$this->db->insert('Payment', $data);
	}

	//patient pays a bill
	//I assume $inputs will be of the form (bill_id)
	//changes the cleared status of a bill to TRUE
	function pay_bill($inputs){
	
		$data = array('cleared' => TRUE);
		$this->db->update('Payment', $data, array('bill_id' => $inputs[0]));
	}

	
}
?>

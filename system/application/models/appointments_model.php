<?php
class Appointments_model extends Model {
	
	function __construct() {
		parent::Model();
		$this->load->database();
	}


	//patient requests an appointment with a doctor
	//I assume $inputs will be of the form (patient_id, doctor_id, descryption, date_time (YY-MM-DD HH-MM-SS))
	//inserts an entry into the Appointments table
	//NOTE: AS OF RIGHT NOW IT DOES NOT HANDLE ERROR CHECKING TO SEE IF THE date_time SPECIFIED CONFLICTS WITH ANOTHER APPOINTMENT
	function request($inputs){
	
		/*FOR LATER, WHEN WE HAVE TO MANAGE TIMES, NOT COMPLETE
		$sql = "SELECT date_time + cast('30 minute' as interval)";
		$uper_bound = $this->db->query
		
		$sql = "SELECT *
			FROM Appointment A, HCP_Account H
			WHERE A.hcp_id = ? AND A.hcp_id == H.account_id
			AND ( EXTRACT(?) BETWEEN H.open AND H.close) AND ( ? NOT BETWEEN A.date_time AND )";
		*/

		$data = array( 'patient_id' => $inputs[0], 'hcp_id' => $inputs[1], 'descryption' => $inputs[2], 'date_time' => $inputs[3]);
		this->db->insert('Appointments', $data);
	}
	
	//view all appointments a patient has ever had OR all appointments a doctor has ever issued (approved as well as not approved)
	//I assume $inputs will be of the form (account_id, type of account(doctor or patient))
	//returns array with all appointments OR NULL if there are no appointments
	function view_all($inputs){
	
		//lists all appointments a patient has ever had
		if( $inputs[1] == 'patient'){
			$sql = "Select A.appointment_id, H2.first_name, H2.last_name, A.amount, A.descryption, A.date_time, A.cleared
				FROM Appointments A, HCP_Account H, HCP_Account H2
				WHERE A.hcp_id = H.account_id AND A.patient_id = ? AND A.hcp_id == H2.account_id";
			$query = $this->db->query($sql, $inputs[0]);
			$result = $query->result_array();
			if( count($result) > 0 )
				return $result;

			return NULL;	
		}

		//lists all appointments a doctor has issued
		$sql = "Select A.appointment_id, P2.first_name, P2.last_name, A.amount, A.descryption, A.date_time, A.cleared
			FROM Appointments A, Patient_Account P, Patient_Account P2
			WHERE A.patient_id = P.account_id AND A.hcp_id = ? AND A.patient_id == P2.account_id";
		$query = $this->db->query($sql, $inputs[0]);
		$result = $query->result_array();
		if( count($result) > 0 )
			return $result;

		return NULL;			
	}
	
	//view all upcoming appointments a patient has OR all upcoming appointments a doctor has (approved as well as not approved)
	//I assume $inputs will be of the form (account_id, type of account(doctor or patient))
	//returns array with all upcoming appointments OR NULL if there are no upcoming appointments
	//NOTE: I DETERMINE WHAT IS UPCOMING IF THE APPOINTMENT date_time ATTRIBURE >= NOW() (NOW RETURNTS CURRENT DATE AND TIME YY-MM-DD HH:MM:SS)
	function view_upcoming($inputs){
		
		//lists all upcoming appointments a patient has
		if( $inputs[1] == 'patient'){
			$sql = "Select A.appointment_id, H2.first_name, H2.last_name, A.amount, A.descryption, A.date_time, A.cleared
				FROM Appointments A, HCP_Account H, HCP_Account H2
				WHERE A.hcp_id = H.account_id AND A.patient_id = ? AND A.hcp_id == H2.account_id AND A.date_time >= NOW()";
			$query = $this->db->query($sql, $inputs[0]);
			$result = $query->result_array();
			if( count($result) > 0 )
				return $result;

			return NULL;	
		}

		//lists all upcoming appointments a doctor has
		$sql = "Select A.appointment_id, P2.first_name, P2.last_name, A.amount, A.descryption, A.date_time, A.cleared
			FROM Appointments A, Patient_Account P, Patient_Account P2
			WHERE A.patient_id = P.account_id AND A.hcp_id = ? AND A.patient_id == P2.account_id and A.date_time >= NOW()";
		$query = $this->db->query($sql, $inputs[0]);
		$result = $query->result_array();
		if( count($result) > 0 )
			return $result;

		return NULL;			
	}
	
	//view all past appointments a patient has had OR all past appointments a doctor has had (approved as well as not approved)
	//I assume $inputs will be of the form (account_id, type of account(doctor or patient))
	//returns array with all past appointments OR NULL if there are no past appointments
	//NOTE: I DETERMINE WHAT HAS PAST IF THE APPOINTMENT date_time ATTRIBURE < NOW() (NOW RETURNTS CURRENT DATE AND TIME YY-MM-DD HH:MM:SS)
	function view_past($inputs){
			
		//lists all past appointments a patient has had 
		if( $inputs[1] == 'patient'){
			$sql = "Select A.appointment_id, H2.first_name, H2.last_name, A.amount, A.descryption, A.date_time, A.cleared
				FROM Appointments A, HCP_Account H, HCP_Account H2
				WHERE A.hcp_id = H.account_id AND A.patient_id = ? AND A.hcp_id == H2.account_id AND A.date_time < NOW()";
			$query = $this->db->query($sql, $inputs[0]);
			$result = $query->result_array();
			if( count($result) > 0 )
				return $result;

			return NULL;	
		}

		//lists all past appointments a doctor has had
		$sql = "Select A.appointment_id, P2.first_name, P2.last_name, A.amount, A.descryption, A.date_time, A.cleared
			FROM Appointments A, Patient_Account P, Patient_Account P2
			WHERE A.patient_id = P.account_id AND A.hcp_id = ? AND A.patient_id == P2.account_id and A.date_time < NOW()";
		$query = $this->db->query($sql, $inputs[0]);
		$result = $query->result_array();
		if( count($result) > 0 )
			return $result;

		return NULL;
	}
	
	//doctor approves an appointment request
	//I assume $inputs will be of the form (appointment_id)
	//updates appointment approved status to TRUE
	function approve($inputs){
		
		$data = array('approved' => TRUE);
		$this->db->update('Appointments', $data, array('appointment_id' => $inputs));
	}
	
	//patient OR doctor cancels appointment
	//I assume $inputs will be of the form (appointment_id)
	//deletes the apointment fromt the Appointments table
	function cancel($inputs){
		
		$this->db->delete('Appointments', array('appointment_id' == $inputs));
	}
	
	//patient reschedules appointment with the doctor
	//I assume $inputs will be of the form (appointment_id, date_time (YY-MM-DD HH-MM-SS))
	//updates date_time to new date and time
	//NOTE: AS OF RIGHT NOW IT DOES NOT HANDLE ERROR CHECKING TO SEE IF THE date_time SPECIFIED CONFLICTS WITH ANOTHER APPOINTMENT
	function reschedule($inputs){
	
		$data = array('date_time' => $inputs[1]);
		this->db-update('Appointments', $data, array('appointment_id' => $inputs));
	}


}
?>
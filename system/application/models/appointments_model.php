l<?php
class Appointments_model extends Model {
	
	function __construct() {
		parent::Model();
		$this->load->database();
	}

	/**
	 * States wheather an appointment belongs to a patient or doctor
	 * 
	 * @param $inputs
	 *   Is of the form: array(account_id, appointment_id)
	 * @return
	 *  -1 in case of error in a query
	 *  -5 appointment id does not exist
	 *   TRUE if it is
	 *   FALSE otherwise
	 * */
	 function is_myappointment($inputs){
		 
		//test to see if the appointment_id exists
		$sql = "SELECT *
			FROM appointments A
			WHERE A.appointment_id = ?";
		$query = $this->db->query($sql, array($inputs[1]));
		
		if ($this->db->trans_status() === FALSE)
			return -1;			
		if ($query->num_rows() < 1)
			return -5;

	 	$sql = "SELECT *
			FROM appointments A
			WHERE (A.patient_id = ? OR A.hcp_id = ?) AND A.appointment_id = ?";
	 	$query = $this->db->query($sql, array($inputs[0], $inputs[0], $inputs[1]));
	 	
	 	if ($this->db->trans_status() === FALSE)
			return -1;	
	 	
		if ($query->num_rows() > 0)
			return TRUE;

	 	return FALSE;
	 }


	/**
	 * Gets all information regarding an appointment
	 * 
	 * @param $inputs
	 *   Is of the form: array(appointment_id)
	 * @return
	 *  -1 in case of error in a query
	 *  -5 appointment id does not exist
	 *   Array with all the whole tuple from Appointments table, plus patient first_name and last_name, hcp first_name and last_name 
	 *   emtpy array() if no tuples
	 * */
	 function get_appointment($inputs){

		$sql = "SELECT *
			FROM appointments A
			WHERE A.appointment_id = ?";
		$query = $this->db->query($sql, $inputs);
		
		if ($this->db->trans_status() === FALSE)
			return -1;		
		if ($query->num_rows() < 1)
			return -5;
	 
	 	$sql = "SELECT A.*, P.first_name AS pat_first_name, P.last_name AS pat_last_name, 
	 			    H.first_name AS hcp_first_name, H.last_name AS hcp_last_name 
			FROM appointments A, hcp_account H, patient_account P 
			WHERE A.appointment_id = ? AND A.patient_id = P.account_id AND A.hcp_id = H.account_id";
	 	$query = $this->db->query($sql, $inputs);
	 	
		if ($this->db->trans_status() === FALSE)
			return -1;		
		if ($query->num_rows() < 1)
			return array();
		
	 	return $query->result_array();
	 }
	 
	 
	/**
	 * Patient requests an appointment with a hcp
	 * 
	 * @param $inputs
	 *   Is of the form: array(patient_id, hcp_id, descryption, date_time (YY-MM-DD HH-MM-SS))
	 * @return
	 *  -1 in case of error in a query
	 *   0 if everything goes fine and an entry into the appointments table is made
	 * @note
	 *   AS OF RIGHT NOW IT DOES NOT HANDLE ERROR CHECKING TO SEE IF THE date_time SPECIFIED CONFLICTS WITH ANOTHER APPOINTMENT
	 * */
	function request($inputs){
	
		/*FOR LATER, WHEN WE HAVE TO MANAGE TIMES, NOT COMPLETE
		$sql = "SELECT date_time + cast('30 minute' as interval)";
		$uper_bound = $this->db->query
		
		$sql = "SELECT *
			FROM Appointment A, HCP_Account H
			WHERE A.hcp_id = ? AND A.hcp_id == H.account_id
			AND ( EXTRACT(?) BETWEEN H.open AND H.close) AND ( ? NOT BETWEEN A.date_time AND )";
		*/

		//$data = array( 'patient_id' => $inputs[0], 'hcp_id' => $inputs[1], 'descryption' => $inputs[2], 'date_time' => $inputs[3]);
		//this->db->insert('Appointments', $data);
		
		$sql = "INSERT INTO appointments (patient_id, hcp_id, descryption, date_time)
			VALUES (?, ?, ?, ?)";
		$query = $this->db->query($sql, $inputs);
		
		if ($this->db->trans_status() === FALSE)
			return -1;
			
		return 0;
	}
	

	/**
	 * 	View all appointments a patient has ever had OR all appointments a hcp has ever issued (approved as well as not approved)
	 * 
	 * @param $inputs
	 *   Is of the form: array(account_id, type of account(hcp or patient))
	 * @return
	 *  -1 in case of error in a query
	 *   Array with all appointments
	 *   empty array() if there are no appointments
	 * */
	function view_all($inputs){
	
		//lists all appointments a patient has ever had
		if( $inputs['type'] === 'patient'){
			$sql = "Select A.appointment_id, H2.first_name, H2.last_name, A.descryption, A.date_time, A.approved
				FROM appointments A, hcp_account H, hcp_account H2
				WHERE A.hcp_id = H.account_id AND A.patient_id = ? AND A.hcp_id = H2.account_id";
			$query = $this->db->query($sql, array($inputs['account_id']));
			
			if ($this->db->trans_status() === FALSE)
				return -1;
			
			if ($query->num_rows() > 0)
				return $query->result_array();

			return array();	
		}

		//lists all appointments a hcp has issued
		$sql = "Select A.appointment_id, P2.first_name, P2.last_name, A.descryption, A.date_time, A.approved
			FROM appointments A, patient_account P, patient_account P2
			WHERE A.patient_id = P.account_id AND A.hcp_id = ? AND A.patient_id = P2.account_id";
		$query = $this->db->query($sql, array($inputs['account_id']));
		
		if ($this->db->trans_status() === FALSE)
			return -1;			
		
		if ($query->num_rows() > 0)
			return $query->result_array();
			
		return array();			
	}
	
	
	/**
	 * View all upcoming appointments a patient has OR all upcoming appointments a hcp has (approved as well as not approved)
	 * 
	 * @param $inputs
	 *   Is of the form: array(account_id, type of account(hcp or patient))
	 * @return
	 *  -1 in case of error in a query
	 *   Array with all upcoming appointments
	 *   empty array() if there are no upcoming appointments
	 * @note
	 *   I DETERMINE WHAT IS UPCOMING IF THE APPOINTMENT date_time ATTRIBURE >= NOW() (NOW RETURNTS CURRENT DATE AND TIME YY-MM-DD HH:MM:SS)	
	 * */
	function view_upcoming($inputs){
		
		//lists all upcoming appointments a patient has
		if( $inputs['type'] == 'patient'){
			$sql = "Select A.appointment_id, H2.first_name, H2.last_name, A.descryption, A.date_time, A.approved
				FROM appointments A, hcp_account H, hcp_account H2
				WHERE A.hcp_id = H.account_id AND A.patient_id = ? AND A.hcp_id = H2.account_id AND A.date_time >= NOW()";
			$query = $this->db->query($sql, $inputs['account_id']);
			
			if ($this->db->trans_status() === FALSE)
				return -1;
			
			if ($query->num_rows() > 0)
				return $query->result_array();

			return array();	
		}

		//lists all upcoming appointments a hcp has
		$sql = "Select A.appointment_id, P2.first_name, P2.last_name, A.descryption, A.date_time, A.approved
			FROM appointments A, patient_account P, patient_account P2
			WHERE A.patient_id = P.account_id AND A.hcp_id = ? AND A.patient_id = P2.account_id and A.date_time >= NOW()";
		$query = $this->db->query($sql, $inputs['account_id']);
		
		if ($this->db->trans_status() === FALSE)
				return -1;
			
		if ($query->num_rows() > 0)
			return $query->result_array();

		return array();			
	}
	

	/**
	 * View all past appointments a patient has had OR all past appointments a hcp has had (approved as well as not approved)
	 * 
	 * @param $inputs
	 *   Is of the form: array(account_id, type of account(hcp or patient))
	 * @return
	 *  -1 in case of error in a query
	 *   Array with all past appointments
	 *   empty array() if there are no past appointments
	 * @note
	 *   I DETERMINE WHAT IS PAST IF THE APPOINTMENT date_time ATTRIBURE < NOW() (NOW RETURNTS CURRENT DATE AND TIME YY-MM-DD HH:MM:SS)	
	 * */
	function view_past($inputs){
			
		//lists all past appointments a patient has had 
		if( $inputs['type'] == 'patient'){
			$sql = "Select A.appointment_id, H2.first_name, H2.last_name, A.descryption, A.date_time, A.approved
				FROM appointments A, hcp_account H, hcp_account H2
				WHERE A.hcp_id = H.account_id AND A.patient_id = ? AND A.hcp_id = H2.account_id AND A.date_time < NOW()";
			$query = $this->db->query($sql, $inputs['account_id']);
			
			if ($this->db->trans_status() === FALSE)
				return -1;
			
			if ($query->num_rows() > 0)
				return $query->result_array();

			return array();	
		}

		//lists all past appointments a hcp has had
		$sql = "Select A.appointment_id, P2.first_name, P2.last_name, A.descryption, A.date_time, A.approved
			FROM appointments A, patient_account P, patient_account P2
			WHERE A.patient_id = P.account_id AND A.hcp_id = ? AND A.patient_id = P2.account_id and A.date_time < NOW()";
		$query = $this->db->query($sql, $inputs['account_id']);
		
		if ($this->db->trans_status() === FALSE)
				return -1;
			
		if ($query->num_rows() > 0)
			return $query->result_array();
		
		return array();
	}
	
	
	/**
	 * hcp approves an appointment request
	 * 
	 * @param $inputs
	 *   Is of the form: array(appointment_id)
	 * @return
	 *  -1 in case of error in a query
	 *  -5 if appointment_id does not exist
	 *   0 if everything goes fine approved status is changed to TRUE
	 * */
	function approve($inputs){
		
		//test to see if the appointment_id exists
		$sql = "SELECT *
			FROM appointments A
			WHERE A.appointment_id = ?";
		$query = $this->db->query($sql, $inputs);
		
		if ($this->db->trans_status() === FALSE)
			return -1;		
		if ($query->num_rows() < 1)
			return -5;
		
		//$data = array('approved' => TRUE);
		//$this->db->update('Appointments', $data, array('appointment_id' => $inputs));
		
		$sql = "UPDATE appointments
			SET approved = TRUE
			WHERE appointment_id = ?";
		$query = $this->db->query($sql, $inputs);
		
		if ($this->db->trans_status() === FALSE)
			return -1;
		
		return 0;
	}
	

	/**
	 * Patient OR hcp cancels appointment
	 * 
	 * @param $inputs
	 *   Is of the form: array(appointment_id)
	 * @return
	 *  -1 in case of error in a query
	 *  -5 if appointment_id does not exist
	 *   0 if everything goes fine and the appointment is deleted from the appointments table
	 * */
	function cancel($inputs){
		
		//test to see if the appointment_id exists
		$sql = "SELECT *
			FROM appointments A
			WHERE A.appointment_id = ?";
		$query = $this->db->query($sql, $inputs);
		
		if ($this->db->trans_status() === FALSE)
			return -1;		
		if ($query->num_rows() < 1)
			return -5;
		
		//$this->db->delete('Appointments', array('appointment_id' => $inputs));
		
		$sql = "DELETE FROM appointments
				WHERE appointment_id = ?";
		$query = $this->db->query($sql, array($inputs[0]));
		
		if ($this->db->trans_status() === FALSE)
			return -1;
		
		return 0;
	}

	
	/**
	 * Patient reschedules appointment with the hcp
	 * 
	 * @param $inputs
	 *   Is of the form: array(appointment_id, date_time (YY-MM-DD HH-MM-SS))
	 * @return
	 *  -1 in case of error in a query
	 *  -5 if appointment_id does not exist
	 *   0 if everything goes fine and the appointment date_time is updated in the appointments table
	 * @note
	 *   AS OF RIGHT NOW IT DOES NOT HANDLE ERROR CHECKING TO SEE IF THE date_time SPECIFIED CONFLICTS WITH ANOTHER APPOINTMENT	
	 * */
	function reschedule($inputs){
	
		//test to see if the appointment_id exists
		$sql = "SELECT *
			FROM appointments A
			WHERE A.appointment_id = ?";
		$query = $this->db->query($sql, array($inputs[0]));
		
		if ($this->db->trans_status() === FALSE)
			return -1;	
		if ($query->num_rows() < 1)
			return -5;
	
		//$data = array('date_time' => $inputs[1]);
		//this->db-update('Appointments', $data, array('appointment_id' => $inputs));
		
		$sql = "UPDATE appointments
				SET date_time = ?
				WHERE appointment_id = ?";
		$query = $this->db->query($sql, array($inputs[1], $inputs[0]));
		
		if ($this->db->trans_status() === FALSE)
			return -1;
		
		return 0;
	}
}
?>

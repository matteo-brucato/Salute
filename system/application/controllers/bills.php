<?php
/**
 * @file bills.php
 * @brief Controller to handle bills
 *
 * @defgroup ctr Controllers
 * @ingroup ctr
 * @{
 */

class Bills extends Controller {

	function __construct(){
		parent::Controller();
		$this->load->library('ajax');	
		$this->load->library('auth');
	}

	/*
	 * Default function call
	 * @return  redirects to all() function
	 * */ 
	function index(){
		$this->auth->check_logged_in();
		$this->all();
	}
  	
	/**
	 * Lists all bills of the current patient/hcp
	 * @return
	 *		if successful, hcp-list all bills you have issued to your patients, set side panel to hcp view
	 * 		if successful, patient- list all the bills HCPs have issued to you, set side panel to patient view
	 * 		if neither patient nor hcp, show error message
	 * 		if any database query errors, show error message
	**/
	function all()	{
		
		$this->auth->check_logged_in();
		$this->load->model('bills_model');

		if($this->auth->get_type() === 'patient'){
			$results = $this->bills_model->view_all(array($this->auth->get_account_id(),$this->auth->get_type()));
			$sidepane = 'sidepane/patient-profile';
		}		
		else if($this->auth->get_type() === 'hcp'){
			$results = $this->bills_model->view_all(array($this->auth->get_account_id(),$this->auth->get_type()));
			$sidepane = 'sidepane/hcp-profile';
		} 
		else{
			show_error('Server Error', 500);
			return;		
		}
		switch ($results) {
			case -1:
				$mainview = 'Query error!';			
				break;
			default:
				$mainview = $this->load->view('mainpane/list_bills',
					array('list_name' => 'My Bills', 'list' => $results) , TRUE);
				break;
		}
		$sideview = $this->load->view($sidepane, '', TRUE);
		$this->ajax->view(array($mainview,$sideview));
	}
	
	/**
	 * Lists current bills of the current patient/hcp
	 * @return
	 *		if successful, hcp-list current bills you have issued to your patients, set side panel to hcp view
	 * 		if successful, patient- list current the bills HCPs have issued to you, set side panel to patient view
	 * 		if neither patient nor hcp, show error message
	 * 		if any database query errors, show error message
	**/
	function current(){
		$this->auth->check_logged_in();
		$this->load->model('bills_model');
		
		if($this->auth->get_type() === 'patient'){
			$results = $this->bills_model->view_current(array($this->auth->get_account_id(),$this->auth->get_type()));
			$sidepane = 'sidepane/patient-profile';
		} 
		else if($this->auth->get_type() === 'hcp'){
			$results = $this->bills_model->view_current(array($this->auth->get_account_id(),$this->auth->get_type()));
			$sidepane = 'sidepane/hcp-profile';
		} 
		else{
			show_error('Server Error', 500);
			return;		
		}
		switch ($results) {
			case -1:
				$mainview = 'Query error!';
				break;
			default:
				$mainview = $this->load->view('mainpane/list_bills', array('list_name' => 'My Current Bills', 'list' => $results) , TRUE);
				break;
		}
		$sideview = $this->load->view($sidepane, '', TRUE);
		$this->ajax->view(array($mainview,$sideview));
	}

	/**
	 * Lists past bills of the current patient/hcp
	 * @return
	 *		if successful, hcp-list past bills you have issued to your patients, set side panel to hcp view
	 * 		if successful, patient- list past  the bills HCPs have issued to you, set side panel to patient view
	 * 		if neither patient nor hcp, show error message
	 * 		if any database query errors, show error message
	**/
	function past() {
		$this->auth->check_logged_in();
		$this->load->model('bills_model');
		
		if($this->auth->get_type() === 'patient'){
			$results = $this->bills_model->view_past(array($this->auth->get_account_id(),$this->auth->get_type()));
			$sidepane = 'sidepane/patient-profile';
		} 
		else if($this->auth->get_type() === 'hcp'){
			$results = $this->bills_model->view_past(array($this->auth->get_account_id(),$this->auth->get_type()));
			$sidepane = 'sidepane/hcp-profile';
		} 
		else{
			show_error('Server Error', 500);
			return;		
		}
		switch ($results) {
			case -1:
				$mainview = 'Query error!';
				break;
			default:
				$mainview = $this->load->view('mainpane/list_bills', array('list_name' => 'My Past Bills', 'list' => $results) , TRUE);
				break;
		}
		$sideview = $this->load->view($sidepane, '', TRUE);
		$this->ajax->view(array($mainview,$sideview));
	}

	/**
	 * Allows HCPs to bill their patients
	 * @attention
	 * 		only HCPs can issue a bill
	 * 		only patient can receive a bill
	 * 		HCP must have connection to a patient to issue bill* 		
	 * @return
	 *		error if non-HCP is calling function
	 * 		error if non-patient is being issued bill or if patient doesnt exist
	 * 		error if HCP is not connected to patient
	 * 		error upon database query errors
	 * 		success: redirect to a form page for HCP to input bill data
	**/
	function issue($patient_id){
		$this->auth->check_logged_in();
		$this->load->model('patient_model');
		$this->load->model('connections_model');
		if( $this->auth->get_type() === 'hcp' ){
			$results = $this->patient_model->get_patient(array($patient_id));	
			$sidepane = 'sidepane/hcp-profile';
			switch( $results ) {
				case -1:
					$mainview = 'Query error. Could not check if patient.!';
					break;
				default:
					if( sizeof($results) < 1 ){
						$mainview = 'Error: this patient does not exist!';
					}
					else{
						$info = $this->connections_model->is_connected_with( $patient_id, $this->auth->get_account_id() );
						if( $info === -1 ){
							$mainview = 'Query error: could not check if you are connected with this patient!';
						}
						else if( $info === FALSE ){
							$mainview = 'You are not connected with this patient';
						}
						else{
							$mainview = $this->load->view('mainpane/issue_bill', array('results' => $results), TRUE);
						}
					}				
			}						
		}	
		else{
			show_error('Server Error', 500);
			return;		
		}
		$sideview = $this->load->view($sidepane, '', TRUE);	
		$this->ajax->view(array($mainview,$sideview));
	}
	
	/**
	 * allows bill info to be inputted into DB
	 * @attention
	 * 		only HCPs can issue a bill
	 * 		only patient can receive a bill
	 * 		HCP must have connection to a patient to issue bill		
	 * @return
	 *		error if non-HCP is calling function
	 * 		error if non-patient is being issued bill or if patient doesnt exist
	 * 		error if HCP is not connected to patient
	 * 		error upon database query errors
	 * 		success: redirect to a form page for HCP to input bill data
	 * @bug
	 * 		incorrent input types will produce DB error
	 * @todo
	 * 		uploading of a receipt file
	 * 		parsing of input, input validation
	**/
	function issue_new_bill() {
		$this->auth->check_logged_in();
		$this->load->model('bills_model');
		$this->load->model('connections_model');
		$this->load->model('patient_model');
		if( $this->auth->get_type() === 'hcp' ){
			$results = $this->patient_model->is_patient(array($this->input->post('patient_id')));	
			$sidepane = 'sidepane/hcp-profile';
			if( $results === -1 ){
				$mainview = 'Query error: could not check if patient is valid!';
			}
			if( $results === FALSE ){
				$mainview = 'Error: Patient does not exist!';
			}		
			else{			
				$info = $this->connections_model->is_connected_with( $this->input->post('patient_id'), $this->auth->get_account_id() );
				if( $info === -1 ){
					$mainview = 'Query error: could not check if you are connected with this patient!';
				}
				else if( $info === FALSE ){
					$mainview = 'Error:You are not connected with this patient';
				}
				else{
					$patient_id = $this->input->post('patient_id');
					$amount = $this->input->post('amount');
					$description = $this->input->post('descryption');
					$due_date = $this->input->post('due_date');
					$results = $this->bills_model->issue_bill(array($patient_id,$this->auth->get_account_id(),$amount,$description,$due_date));
					if( $results === -1 ){
						$mainview = 'Query error: could not issue bill to this patient. Make sure values are of correct form.';
					}
					else{
						$mainview = 'Successfully issues the bill.';
					}
				}
			}
		}										
		else{
			show_error('Server Error', 500);
			return;
		}
		$sideview = $this->load->view($sidepane, '', TRUE);	
		$this->ajax->view(array($mainview,$sideview));

	}
	
	/**
	 * allows HCP to delete bill
	 * @attention
	 * 		only HCPs can delete a bill
	 * @return
	 *		error if non-HCP is calling function
	 * 		error upon database query errors
	 * 		success: redirect to a success page message
	**/
	function delete($bill_id) {
		$this->auth->check_logged_in();
		$this->load->model('bills_model');
		if( $this->auth->get_type() === 'hcp' ){
			$sidepane = 'sidepane/hcp-profile';
			$results = $this->bills_model->is_mybill( array($this->auth->get_account_id(),$bill_id) );
			if( $results === -1 ){
				$mainview = 'Query error: could not check if that is your bill';
			}
			else if( $results === FALSE ){
				$mainview = 'Error: cannot perform delete on this bill. It might not exist, or you are not the HCP.';
			}
			else if( $results === TRUE){
				$results = $this->bills_model->delete_bill( $bill_id );
				if( $results === 0 ){
					$mainview = 'Successfully deleted the bill.';
				}
				else{
					$mainview = 'Query Error: could not delete bill.';
				}
			}
		}
		else{
			show_error('Server Error', 500);
			return;
		}
		$sideview = $this->load->view($sidepane, '', TRUE);
		$this->ajax->view(array($mainview,$sideview));
	}
	
	/**
	 * allows patient to pay bill
	 * @attention
	 * 		only patient can pay a bill
	 * 		no actual method to pay a bill has been implemented
	 * @return
	 *		error if non-patient is calling function
	 * 		error upon database query errors
	 * 		success: changes attribute 'cleared' of type bool from false to true
	**/
	function pay($bill_id) {
		$this->auth->check_logged_in();
		$this->load->model('bills_model');
		if( $this->auth->get_type() === 'patient' ){
			$sidepane = 'sidepane/patient-profile';
			$results = $this->bills_model->is_mybill( array($this->auth->get_account_id(),$bill_id) );
			if( $results === -1 ){
				$mainview = 'Query error: could not check if that is your bill';
			}
			else if( $results === FALSE ){
				$mainview = 'Error: cannot perform pay on this bill. It might not exist, or you are not the patient.';
			}
			else if( $results === TRUE){
				$results = $this->bills_model->pay_bill( $bill_id );
				if( $results === 0 ){
					$mainview = 'Successfully paid the bill.';
				}
				else{
					$mainview = 'Query Error: could not pay bill.';
				}
			}
		}
		else{
			show_error('Server Error', 500);
			return;
		}
		$sideview = $this->load->view($sidepane, '', TRUE);
		$this->ajax->view(array($mainview,$sideview));
	}
	
	

}
/** @} */
?>

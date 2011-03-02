<?php
/**
 * @file refers.php
 * @brief Controller to handle referals
 *
 * @defgroup ctr Controllers
 * @ingroup ctr
 * @{
 */
 
 class Refers extends Controller {
	 
	 function __construct() {
			parent::Controller();
			$this->load->library('ui');
			$this->load->library('auth');
			$this->load->model('referal_model');
			$this->load->model('connections_model');
			$this->load->model('patient_model');
	  }
		
	
	/**
	 * fn index -- default
	 * redirects to my_referals
	 * */
	function index(){
		$check = $this->auth->check(array(auth::CurrLOG));
		if ($check !== TRUE) return;
		
		$this->my_referals();
	}
	
	/**
	 * Lists all of the referals a patient has received or that a doctor has issued
	 * @param
	 * @return
	 * 	view to load all referrals
	 * */
	function my_referals(){
		
		$check = $this->auth->check(array(auth::CurrLOG));
		if ($check !== TRUE) return;

		if ($this->auth->get_type() === 'patient'){
			$results = $this->referal_model->view_referals(array('account_id' => $this->auth->get_account_id(),
																 'type' => $this->auth->get_type()));
		} else if ($this->auth->get_type() === 'hcp'){
			$results = $this->referal_model->view_referals(array('account_id' => $this->auth->get_account_id(),
																 'type' => $this->auth->get_type()));
		} else {
			$this->ui->set_error('Internal server logic error.', 'server');
			return;
		}
		
		if ($results === -1){
			$this->ui->set_query_error();
			return;
		}
		$mainview = $this->load->view('mainpane/lists/referals',
					array('list_name' => 'My Referrals', 'list' => $results), TRUE);
				
		// Give results to the client
		$this->ui->set(array($mainview));
	}
	
	
	/**
	 * This is the main function that starts the creation of a referral
	 * @param
	 * @return
	 * 	load view to chosse a hcp
	 * */
	function create_referral() {
		
		$check = $this->auth->check(array(
			auth::CurrLOG,
			auth::CurrHCP));
		if ($check !== TRUE) return;
		
		$results = $this->connections_model->list_hcps_connected_with($this->auth->get_account_id()); 
		
		if ($results === -1) {
			$this->ui->set_query_error();
			return;
		}
		
		//load a view to display and choose a colleague
		$this->ui->set(array(
			$this->load->view('mainpane/forms/pick_hcp',
			array(
				'list_name' => 'My Colleagues',
				'list' => $results,
				'form_action' => '/refers/create_referral_do1'), TRUE)
		));
	}
	
	
	/**
	 * Allows hcp to choose a patient for the referral
	 * @param
	 * @return
	 * 	load view to chosse a patient
	 * */
	function create_referral_do1() {
		$check = $this->auth->check(array(
			auth::CurrLOG,
			auth::CurrHCP));
		if ($check !== TRUE) return;
		
		$hcp_id = $this->input->post('hcp_id');

		if ($hcp_id == NULL) {
			$this->ui->set_error('Select an hcp.','Missing Arguments'); 
			return;
		}
			
		$check = $this->auth->check(array(
			auth::HCP, $hcp_id,
			auth::CurrCONN, $hcp_id));
		if ($check !== TRUE) return;
		
		//get all of the patients
		$results = $this->connections_model->list_patients_connected_with($this->auth->get_account_id());
		
		if ($results === -1) {
			$this->ui->set_query_error();
			return;
		}
		
		//loads the view to display and choose a patient
		$this->ui->set(array(
			$this->load->view('mainpane/forms/pick_patient',
			array(
				'list_name' => 'My Patients',
				'list' => $results, 'status' => 'connected',
				'form_action' => '/refers/create_referral_do2/'.$hcp_id), TRUE)
		));
	}
	
	
	/**
	 * This is the final function of the referral process
	 * Takes the patient id and the hcp being referred id and creates the referral
	 * 
	 * @param
	 * @return
	 * 	successfully create the referral and give success message
	 * */
	function create_referral_do2($hcp_id = NULL) {
		$check = $this->auth->check(array(
			auth::CurrLOG,
			auth::CurrHCP,
			auth::HCP, $hcp_id,
			auth::CurrCONN, $hcp_id));
		if ($check !== TRUE) return;
		
		//get input from the form
		$patient_id = $this->input->post('patient_id');
		//$hcp_id = $this->input->post('hcp_id'); /** @note Now, it takes hcp_id from the function parameters! */
		
		if ($patient_id == NULL) {
			$this->ui->set_error('Select a patient.','Missing Arguments'); 
			return;
		}
		
		// Current must also be connected to patient id
		$check = $this->auth->check(array(
			auth::PAT, $patient_id,
			auth::CurrCONN, $patient_id));
		if ($check !== TRUE) return;
		
		$this->db->trans_start();
		$referal_id = $this->referal_model->create_referal(array($this->auth->get_account_id(), $hcp_id, $patient_id));
		$this->db->trans_complete();
		
		if ($referal_id === -1) {
			$this->ui->set_query_error();
			return;
		}
		elseif ($referal_id === -2){
			$this->ui->set_error('Referal ID does not exist');
			return;
		}
		elseif ($referal_id == -3){
			$this->ui->set_error('You have already created this referral', 'Notice');
			return;
		}
		elseif ($referal_id == -4){
			$this->ui->set_error('The doctor being referred is already friends with that patient', 'Notice');
			return;
		}
			
		//check level. if its 2 or 3 patient accepts connection automatically
		$level = $this->connections_model->get_level(array($patient_id, $this->auth->get_account_id()));
		switch ($level) {
			case -1:
				$this->ui->set_query_error();
				return;
			case -2:
				$this->ui->set_error('Connection does not exist!', 'Permission Denied');
				return;
			default:
				if ($level[0]['sender_level'] === '2' or $level[0]['sender_level'] === '3'){
					
					$approve = $this->referal_model->approve(array($referal_id));
					switch ($approve) {
						case -1:
							$this->ui->set_query_error();
							return;
						case -2:
							$this->ui->set_error('Referal ID does not exist');
							return;
						default:
							//create connection
							$res = $this->connections_model->add_connection(array($patient_id, $hcp_id));
							switch ($res) {
								case -1:
									$this->ui->set_query_error();
									return;
								case -3:
									$this->ui->set_error('This connection has been already requested');
									return;
								default:
									//send email
									break;
							}
					}
				}
		}
		
		$this->ui->set_message('Your referral was successfully created!', 'Confirmation');
	}
	
	 
	 /**
	  * Accepts the referal
	  * @param
	  * 	referal_id
	  * @return
	  * 	successfully accepted and sent the email to the doctor
	  * */
	  function accept_referal($referal_id = NULL){
			
		  $check = $this->auth->check(array(
			auth::CurrLOG,
			auth::CurrPAT,
			auth::CurrREFOWN, $referal_id));
		  if ($check !== TRUE) return;
		  
		  //set referal status to true
		  $res = $this->referal_model->approve(array($referal_id));
		  switch ($res) {
			  case -1:
		          $this->ui->set_query_error();
				  return;
			  case -2:
				  $this->ui->set_error('Referal ID does not exist');
				  return;
			  default:
				  break;
		  }
		  
		  //get all of the info regarding the referal
		  $referal_info = $this->referal_model->get_referal(array($referal_id));
		  switch ($referal_info) {
			  case -1:
		          $this->ui->set_query_error();
				  return;
			  case -2:
				  $this->ui->set_error('Referal ID does not exist');
				  return;
			  default:
				  break;
		  }
		  
		  //create connection
		  $res = $this->connections_model->add_connection(array($this->auth->get_account_id(), $referal_info[0]['is_refered_id']));
			switch ($res) {
				case -1:
				    $this->ui->set_query_error();
					return;
				case -3:
					$this->ui->set_error('This connection has been already requested');
					return;
				default:
					$this->load->library('email');
					$config['mailtype'] = 'html';
					$this->email->initialize($config);
					//$this->email->from($this->auth->get_email());
					$this->email->from('salute-noreply@salute.com');
					//$this->email->to($results['email']);
					$this->email->to('mattfeel@gmail.com');
					$this->email->subject('New Connection Request');
					
					//get the name of the patient to put in the email
					$patient_name = $this->patient_model->get_patient($referal_info[0]['patient_id']);
					if ($patient_name  === -1){
						$this->ui->set_query_error();
						return -1;
					}
					elseif ( count($patient_name) <= 0 ){
						$this->ui->set_error('Patient does not exist');
						return;
					}
					else{					
						$this->email->message(
							'You have a new connection request from '.
							$patient_name[0]['first_name'].' '.$patient_name[0]['last_name'].
							'. Click <a href="https://'.$_SERVER['SERVER_NAME'].'/connections/accept/'.$referal_info[0]['patient_id'].'/'.$referal_info[0]['is_refered_id'].'">here</a> to accept.');
						
						$this->email->send();
						break;
					}
			}
			
			$this->ui->set_message('Your request has been submitted','Confirmation');
	 }
	
	
	/**
	 * Allows a patient or hcp to delete a referal
	 * @param referal_id, the referal id number to delete from database
	 * @return successfully delete referal || error(not their referal)
	 * 
	 * */
	 function delete_referal($ref_id = NULL){
		
		$check = $this->auth->check(array(
			auth::CurrLOG,
			auth::CurrREFOWN, $ref_id));
		//echo $check;
		if ($check !== TRUE) return;
		
		$results = $this->referal_model->delete(array($ref_id));
							
		switch ($results) {
			case -1:
				$this->ui->set_query_error();
				return;
			case -2:
				$this->ui->set_error('Referal does not exist!');
				return;
			default:
				$this->ui->set_message('The referal was successfully deleted.','Confirmation');
				return;
		}
	}	 
 }
 /** @} */
?>

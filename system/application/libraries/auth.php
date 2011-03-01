<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * CodeIgniter Auth Class
 *
 * Allows controllers to check for authorization
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		Matteo Brucato
 * 
 * @defgroup lib Libraries
 * @ingroup lib
 */

class Auth {
	
	private $account_id;
	private $type;
	private $email;
	private $first_name;
	private $last_name;
	private $CI;
	
	const CurrLOG		= 0;	// no params: tests if the current user is logged in
	const CurrPAT		= 1;	// no params: tests if the current user is a patient
	const CurrHCP		= 2;	// no params: tests if the current user is an hcp
	const CurrCONN		= 3;	// requires one account id: tests if the current is connected with the id provided
	const CurrCONN_SND	= 4;	// requires one account id: tests if the current is the sender of a connection to the account id
	const CurrCONN_RECV	= 5;	// requires one account id: tests if the current is the receiver of a connection to the account id
	const CurrGRPMEM	= 6;	// requires one groupid: tests if the current is a member of the groupid
	const CurrREFOWN	= 7;	// requires one referral id: tests if referral id is owned by the current user
	const CurrIS_or_CONN = 222; // requires one account id: tests if current is account provided or at least connected with it
	
	const ACCOUNT		= 8;	// requires one id: tests if it's an account id
	const PAT			= 9;	// requires one id: tests if it's a patient id
	const HCP			= 10;	// requires one id: tests if it's a hcp id
	const GRP			= 11;	// requires one id: tests if it's a group_id
	
	const APPT_EXST		= 12;	// requires one id: tests if the appointment id exists
	const APPT_MINE		= 13;	// requires one id: tests if it's your appointment id
	
	const BILL_DELC		= 14;	// requires one id: tests if bill id is deletable
	const BILL_PAYC		= 15;	// requires one id: tests if bill id is inactive=payable
	
	const CurrMED_OWN	= 16;	// requires one id: tests if current is the owner of the medical record specified
	
	
	
	//const REF_MINE		= 13;   // requires one id: tests if it's your referral id
	
	function __construct() {
		$CI =& get_instance();
		$this->account_id	= $CI->session->userdata('account_id');
		$this->type			= $CI->session->userdata('type');
		$this->email		= $CI->session->userdata('email');
		$this->first_name	= $CI->session->userdata('first_name');
		$this->last_name	= $CI->session->userdata('last_name');
		$this->CI = $CI;
		$this->CI->load->library('ui');
	}
	
	function is_logged_in() {
		return (
			$this->account_id != FALSE &&
			$this->type != FALSE &&
			$this->email != FALSE &&
			$this->first_name != FALSE &&
			$this->last_name != FALSE
		);
	}
	
	/**
	 * Checks if ALL the restrictions are satisfied. If one of them is not,
	 * it will return the check number not satisfied. If all of them are
	 * satisfied it returs TRUE
	 * 
	 * @return TRUE if all checks are satisfied. Otherwise it returns
	 * the restriction number not satisfied.
	 * */
	function check($a = array()) {
		for ($i = 0; $i < count($a); $i++) {
			switch ($a[$i]) {
				case auth::CurrLOG:
					if (!$this->is_logged_in()) {
						$this->CI->ui->set(array(NULL, $this->CI->load->view('sidepane/forms/login', '', TRUE)));
						$this->CI->ui->set_error($this->CI->load->view('errors/not_logged_in', '', TRUE), 'authorization');
						return auth::CurrLOG;
					}
					break;

				case auth::CurrPAT:
					if ($this->type !== 'patient') {
						$this->CI->ui->set_error($this->CI->load->view('errors/not_patient', '', TRUE), 'Permission Denied');
						return auth::CurrPAT;
					}
					break;
					
				case auth::CurrHCP:
					if ($this->type !== 'hcp') {
						$this->CI->ui->set_error($this->CI->load->view('errors/not_hcp', '', TRUE), 'Permission Denied');
						return auth::CurrHCP;
					}
					break;
				
				// current user must be the requester of the connection to the other account
				case auth::CurrCONN_SND:
					if ($a[$i+1] === NULL) {
						$this->CI->ui->set_error('No input provided');
						return auth::CurrCONN_SND;
					}
					if (! is_numeric($a[$i+1])) {
						$this->CI->ui->set_error('Not numeric');
						return auth::CurrCONN_SND;
					}
					$this->CI->load->model('connections_model');
					$conn = $this->CI->connections_model->get_connection($this->CI->auth->get_account_id(), $a[$i+1]);
					if ($conn === -1) {
						$this->CI->ui->set_query_error();
						return auth::CurrCONN_SND;
					}
					if ($conn === NULL) {
						$this->CI->ui->set_error('No connection exists between this two accounts', 'Notice');
						return auth::CurrCONN_SND;
					}
					if ($conn['sender_id'] != $this->CI->auth->get_account_id()) {
						$this->CI->ui->set_error('This connection request has not been initiated by you.', 'Notice');
						return auth::CurrCONN_SND;
					}
					$i++;
					break;

				// current user must be the accepter of the connection to the other account
				case auth::CurrCONN_RECV:
					if ($a[$i+1] === NULL) {
						$this->CI->ui->set_error('No input provided');
						return auth::CurrCONN_RECV;
					}
					if (! is_numeric($a[$i+1])) {
						$this->CI->ui->set_error('Not numeric');
						return auth::CurrCONN_RECV;
					}
					$this->CI->load->model('connections_model');
					$conn = $this->CI->connections_model->get_connection($this->CI->auth->get_account_id(), $a[$i+1]);
					if ($conn === -1) {
						$this->CI->ui->set_query_error();
						return auth::CurrCONN_RECV;
					}
					if ($conn === NULL) {
						$this->CI->ui->set_error('No connection exists between this two accounts', 'Notice');
						return auth::CurrCONN_RECV;
					}
					if ($conn['receiver_id'] != $this->CI->auth->get_account_id()) {
						$this->CI->ui->set_error('You are not the receiver of this connection', 'Notice');
						return auth::CurrCONN_RECV;
					}
					$i++;
					break;
				
				// current user must be either the account specified or connected with it
				case auth::CurrIS_or_CONN:
					if ($a[$i+1] === NULL) {
						$this->CI->ui->set_error('No input provided');
						return auth::CurrIS_or_CONN;
					}
					if (! is_numeric($a[$i+1])) {
						$this->CI->ui->set_error('Not numeric');
						return auth::CurrIS_or_CONN;
					}
					if ($a[$i+1] == $this->account_id) {
						$i++;
						break;
					}
					$this->CI->load->model('connections_model');
					$c = $this->CI->connections_model->is_connected_with(array($a[$i+1]), $this->account_id);
					if ($c === -1) {
						$this->CI->ui->set_query_error();
						return auth::CurrIS_or_CONN;
					}
					else if ($c === FALSE) {
						$this->CI->ui->set_error('You are not the owner or not connected with this account','Permission Denied');
						return auth::CurrIS_or_CONN;
					}
					$i++;
					break;
				
				case auth::ACCOUNT:
					if ($a[$i+1] === NULL) {
						$this->CI->ui->set_error('No input provided');
						return auth::ACCOUNT;
					}
					if (! is_numeric($a[$i+1])) {
						$this->CI->ui->set_error('Not numeric');
						return auth::ACCOUNT;
					}
					$this->CI->load->model('account_model');
					$acc = $this->CI->account_model->is_account(array($a[$i+1]));
					if ($acc === -1) {
						$this->CI->ui->set_query_error();
						return auth::ACCOUNT;
					}
					else if($acc === FALSE){
						$this->CI->ui->set_error('This is not an account.');
						return auth::ACCOUNT;
					}
					$i++;
					break;
					
				case auth::PAT:
					if ($a[$i+1] === NULL) {
						$this->CI->ui->set_error('No input provided');
						return auth::PAT;
					}
					if (! is_numeric($a[$i+1])) {
						$this->CI->ui->set_error('Not numeric');
						return auth::PAT;
					}
					$this->CI->load->model('patient_model');
					$pat = $this->CI->patient_model->get_patient(array($a[$i+1]));
					if ($pat === -1) {
						$this->CI->ui->set_query_error();
						return auth::PAT;
					} else if (count($pat) <= 0) {
						$this->CI->ui->set_error('The id does not refer to any patient');
						return auth::PAT;
					}
					$i++;
					break;
					
				case auth::HCP:
					if ($a[$i+1] === NULL) {
						$this->CI->ui->set_error('No input provided');
						return auth::HCP;
					}
					if (! is_numeric($a[$i+1])) {
						$this->CI->ui->set_error('Not numeric');
						return auth::HCP;
					}
					$this->CI->load->model('hcp_model');
					$hcp = $this->CI->hcp_model->get_hcp(array($a[$i+1]));
					if ($hcp === -1) {
						$this->CI->ui->set_query_error();
						return auth::HCP;
					} else if (count($hcp) <= 0) {
						$this->CI->ui->set_error('The id does not refer to any HCP');
						return auth::HCP;
					}
					$i++;
					break;
					
				case auth::CurrCONN:
					if ($a[$i+1] === NULL) {
						$this->CI->ui->set_error('No input provided');
						return auth::CurrCONN;
					}
					if (! is_numeric($a[$i+1])) {
						$this->CI->ui->set_error('Not numeric');
						return auth::CurrCONN;
					}
					$this->CI->load->model('connections_model');
					$check = $this->CI->connections_model->is_connected_with($this->account_id, $a[$i+1]);
					if ($check === -1) {
						$this->CI->ui->set_query_error();
						return auth::CurrCONN;
					}
					else if ($check === FALSE) {
						$this->CI->ui->set_error('You are not connected with this account','Permission Denied');
						return auth::CurrCONN;
					}
					$i++;
					break;
				
				case auth::GRP:
					if ($a[$i+1] === NULL) {
						$this->CI->ui->set_error('No input provided');
						return auth::GRP;
					}
					if (! is_numeric($a[$i+1])) {
						$this->CI->ui->set_error('Not numeric');
						return auth::GRP;
					}
					$this->CI->load->model('groups_model');
					$check = $this->CI->groups_model->get_group($a[$i+1]);
					if ($check === -1) {
						$this->CI->ui->set_query_error();
						return auth::GRP;
					}
					else if ($check === NULL) {
						$this->CI->ui->set_error('This id does not refer to any group');
						return auth::GRP;
					}
					$i++;
					break;

				case auth::CurrGRPMEM:
					if ($a[$i+1] === NULL) {
						$this->CI->ui->set_error('No input provided');
						return auth::CurrGRPMEM;
					}
					if (! is_numeric($a[$i+1])) {
						$this->CI->ui->set_error('Not numeric');
						return auth::CurrGRPMEM;
					}
					$this->CI->load->model('groups_model');
					$check = $this->CI->groups_model->get_member($this->account_id, $a[$i+1]);
					if ($check === -1) {
						$this->CI->ui->set_query_error();
						return auth::CurrGRPMEM;
					}
					else if ($check === NULL) {
						$this->CI->ui->set_error('You are not member of this group');
						return auth::CurrGRPMEM;
					}
					$i++;
					break;
				
				case auth::APPT_EXST:
					return auth::APPT_EXST;
					$i++;
					break;
					
				case auth::APPT_MINE:
					if ($a[$i+1] === NULL) {
						$this->CI->ui->set_error('No input provided');
						return auth::APPT_MINE;
					}
					if (! is_numeric($a[$i+1])) {
						$this->CI->ui->set_error('Not numeric');
						return auth::APPT_MINE;
					}
					$this->CI->load->model('appointments_model');
					$result = $this->CI->appointments_model->is_myappointment(array($this->account_id, $a[$i+1]));
					if ($result === -1) {
						$this->CI->ui->set_query_error();
						return auth::APPT_MINE;
					} elseif ($result === -5) {
						$this->CI->ui->set_error('Appointment ID does not exist!');
						return auth::APPT_MINE;
					} elseif ($result !== TRUE) {
						$this->CI->ui->set_error('This is not your appointment', 'permission denied');
						return auth::APPT_MINE;
					}
					$i++;
					break;

				case auth::CurrREFOWN:
					if ($a[$i+1] === NULL) {
						$this->CI->ui->set_error('No input provided');
						return auth::CurrREFOWN;
					}
					if (! is_numeric($a[$i+1])) {
						$this->CI->ui->set_error('Not numeric');
						return auth::CurrREFOWN;
					}
					$this->CI->load->model('referal_model');
					$result = $this->CI->referal_model->is_myreferal(array($this->account_id, $a[$i+1]));
					if ( $result === -1 ){
						$this->CI->ui->set_query_error();
						return auth::CurrREFOWN;
					} elseif ( $result === -2 ){
						$this->CI->ui->set_error('Referal ID does not exist!');
						return auth::CurrREFOWN;
					} elseif ($result !== TRUE) {
						$this->CI->ui->set_error('This is not your referal.', 'permission denied');
						return auth::CurrREFOWN;
					}
					$i++;
					break;

				case auth::BILL_DELC:
					if ($a[$i+1] === NULL) {
						$this->CI->ui->set_error('No input provided');
						return auth::BILL_DELC;
					}
					if (! is_numeric($a[$i+1])) {
						$this->CI->ui->set_error('Not numeric');
						return auth::BILL_DELC;
					}
					$this->CI->load->model('bills_model');
					$results = $this->CI->bills_model->get_bill($a[$i+1]);
					if( $results === -1 ){
						$this->CI->ui->set_query_error(); 
						return auth::BILL_DELC;
					}
					
					if( count($results) < 1 ){
						$this->CI->ui->set_error('This bill does not exist.');
						return auth::BILL_DELC;
					}
					if( $this->type === 'patient' ){
						if( $results[0]['patient_id'] !== $this->account_id ){
							$this->CI->ui->set_error('This is not your bill.', 'permission denied');
							return auth::BILL_DELC;
						}
						if( $results[0]['patient_kept'] === 'f' ){
							$this->CI->ui->set_error('This bill has already been deleted.');
							return auth::BILL_DELC;
						}
						if( $results[0]['cleared'] === 'f' && $results[0]['hcp_kept'] === 't' ){
							$this->CI->ui->set_error('Cannot delete active bills.');
							return auth::BILL_DELC;	
						}
					}
					else{
						if( $results[0]['hcp_id'] !== $this->account_id ){
							$this->CI->ui->set_error('This is not your bill.', 'permission denied');
							return auth::BILL_DELC;
						}
						if( $results[0]['hcp_kept'] === 'f' ){
							$this->CI->ui->set_error('This bill has already been deleted.');
							return auth::BILL_DELC;						
						}
					}			
					$i++;
					break;				
				
				case auth::BILL_PAYC:
					if ($a[$i+1] === NULL) {
						$this->CI->ui->set_error('No input provided');
						return auth::BILL_PAYC;
					}
					if (! is_numeric($a[$i+1])) {
						$this->CI->ui->set_error('Not numeric');
						return auth::BILL_PAYC;
					}
					$this->CI->load->model('bills_model');
					$results = $this->CI->bills_model->get_bill($a[$i+1]);
					if( $results === -1 ){
						$this->ui->set_query_error(); 
						return auth::BILL_PAYC;
					}
					if( count($results) < 1 ){
						$this->CI->ui->set_error('This bill does not exist.');
						return auth::BILL_PAYC;
					}
					if( $results[0]['patient_id'] !== $this->account_id ){
						$this->CI->ui->set_error('This is not your bill.', 'permission denied');
						return auth::BILL_PAYC;
					}
					if( $results[0]['hcp_kept'] === 'f' ){
						$this->CI->ui->set_error('This bill is inactive because it was cancelled by the Healthcare Provider.');
						return auth::BILL_PAYC;
					}			
					if( $results[0]['cleared'] === 't' ){
						$this->CI->ui->set_error('This bill his inactive because it has already been paid.');
						return auth::BILL_PAYC;				
					}
					$i++;
					break;
				
				case auth::CurrMED_OWN:
					if ($a[$i+1] === NULL) {
						$this->CI->ui->set_error('Missing medical record id','Missing Arguments');
						return auth::CurrMED_OWN;
					}
					if (! is_numeric($a[$i+1])) {
						$this->CI->ui->set_error('Not numeric');
						return auth::CurrMED_OWN;
					}
					$get = $this->CI->medical_records_model->get_medicalrecord(array($a[$i+1]));
					if ($get === -1) {
						$this->CI->ui->set_query_error(); 
						return auth::CurrMED_OWN;
					}
					else if (sizeof($get) == 0) {
						$this->CI->ui->set_error('Specified medical record does not exist');
						return auth::CurrMED_OWN;
					}
					else if ($get[0]['patient_id'] != $this->account_id) {
						$this->CI->ui->set_error('Only the owner can modify this medical record','Permission Denied');
						return auth::CurrMED_OWN;
					}
					$i++;
					break;
			}
		}
		return TRUE;
	}
	
	/**
	 * Automatically dislplays an error message if not logged in.
	 * Prevent further actions.
	 * 
	 * @deprecated
	 * */
	function check_logged_in() {
		if (!$this->is_logged_in()) {
			$error_view = $this->CI->load->view('errors/not_logged_in', '', TRUE);
			//show_error($error_view);
			$this->CI->ui->set_error($error_view, 'authorization');
			exit;
		}
	}
	
	/**
	 * @return FALSE if not logged in, otherwise a string 'patient' or
	 * 'doctor'.
	 * */
	function get_type() {
		return $this->type;
	}
	
	function get_account_id() {
		return $this->account_id;
	}
	
	function get_email() {
		return $this->email;
	}
	
	function get_first_name() {
		return $this->first_name;
	}
	
	function get_last_name() {
		return $this->last_name;
	}
}
?>

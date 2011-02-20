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
	
	const CurrLOG		= 0;	// current user: no other params
	const CurrPAT		= 1;	// current user: no other params
	const CurrHCP		= 2;	// current user: no other params
	const CurrCONN		= 3;	// requires one id, tests if the current is connected with the id provided
	const PAT			= 4;	// requires one id, tests if it's a patient id
	const HCP			= 5;	// requires one id, tests if it's a hcp id
	
	const APPT_EXST		= 6;
	const APPT_MINE		= 7;	// requires one id, tests if it's your appointment id
	
	const BILL_DELC		= 8;
	const BILL_PAYC		= 9;		
	
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
					$this->CI->ui->set_error($this->CI->load->view('errors/not_logged_in', '', TRUE), 'authorization');
					$this->CI->ui->set(array(NULL, $this->CI->load->view('sidepane/forms/login', '', TRUE)));
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
				
			case auth::PAT:
				if ($a[$i+1] === NULL) {
					$this->CI->ui->set_error('No input provided');
					return  auth::PAT;
				}
				if (! is_numeric($a[$i+1])) {
					$this->CI->ui->set_error('Not numeric');
					return  auth::PAT;
				}
				$this->CI->load->model('patient_model');
				$hcp = $this->CI->hcp_model->get_patient(array($a[$i+1]));
				if ($hcp === -1) {
					$this->CI->ui->set_query_error();
					return auth::PAT;
				} else if (count($hcp) <= 0) {
					$this->CI->ui->set_error('The id does not refer to any patient');
					return auth::PAT;
				}
				$i++;
				break;
				
			case auth::HCP:
				if ($a[$i+1] === NULL) {
					$this->CI->ui->set_error('No input provided');
					return  auth::HCP;
				}
				if (! is_numeric($a[$i+1])) {
					$this->CI->ui->set_error('Not numeric');
					return  auth::HCP;
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
					return  auth::CurrCONN;
				}
				if (! is_numeric($a[$i+1])) {
					$this->CI->ui->set_error('Not numeric');
					return  auth::CurrCONN;
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
				
			case auth::APPT_EXST:
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
					return  auth::APPT_MINE;
				} elseif ($result === -5) {
					$this->CI->ui->set_error('Appointment ID does not exist!');
					return  auth::APPT_MINE;
				} elseif ($result !== TRUE) {
					$this->CI->ui->set_error('This is not your appointment', 'permission denied');
					return  auth::APPT_MINE;
				}
				$i++;
				break;

			case auth::BILL_DELC:
				if ($perm[$i+1] === NULL) {
					$this->CI->ui->set_error('No input provided');
					return auth::BILL_DELC;
				}
				if (! is_numeric($perm[$i+1])) {
					$this->CI->ui->set_error('Not numeric');
					return auth::BILL_DELC;
				}
				$this->CI->load->model('bills_model');
				$results = $this->CI->bills_model->get_bill($perm[$i+1]);
				if( $results === -1 ){
					$this->ui->set_query_error(); 
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
				if ($perm[$i+1] === NULL) {
					$this->CI->ui->set_error('No input provided');
					return auth::BILL_PAYC;
				}
				if (! is_numeric($perm[$i+1])) {
					$this->CI->ui->set_error('Not numeric');
					return auth::BILL_PAYC;
				}
				$this->CI->load->model('bills_model');
				$results = $this->CI->bills_model->get_bill($perm[$i+1]);
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

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
	
	const CurrIsLoggedin	= 0;	// current user: no other params
	const CurrIsPatient		= 1;	// current user: no other params
	const CurrIsHcp			= 2;	// current user: no other params
	const AreConnected		= 3;	// requires two id's
	const IsPatient			= 4;	// requires one id
	const IsHcp				= 5;	// requires one id
	
	function __construct() {
		$CI =& get_instance();
		$this->account_id	= $CI->session->userdata('account_id');
		$this->type			= $CI->session->userdata('type');
		$this->email		= $CI->session->userdata('email');
		$this->first_name	= $CI->session->userdata('first_name');
		$this->last_name	= $CI->session->userdata('last_name');
		$this->CI = $CI;
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
	 * @return TRUE if all checks are satisfied. Otherwise it returns
	 * the restriction not satisfied.
	 * */
	function check($perm = array()) {
		for ($i = 0; $i < count($perm); $i++) {
			switch ($perm[$i]) {
				case CurrIsLoggedin:
					if (!$this->is_logged_in()) return CurrIsLoggedin;
					break;
				case CurrIsPatient:
					if (!$this->is_patient()) return CurrIsPatient;
					break;
				case CurrIsHcp:
					if (!$this->is_hcp()) return CurrIsHcp;
					break;
				case IsPatient:
					/** @todo */
					$i++;
					break;
				case IsHcp:
					/** @todo */
					$i++;
					break;
				case AreConnected:
					/** @todo */
					$i += 2;
					break;
			}
		}
		return TRUE;
	}
	
	/**
	 * Automatically dislplays an error message if not logged in.
	 * Prevent further actions.
	 * */
	function check_logged_in() {
		if (!$this->is_logged_in()) {
			$error_view = $this->CI->load->view('errors/not_logged_in', '', TRUE);
			show_error($error_view);
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

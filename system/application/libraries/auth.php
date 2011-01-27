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
 */

class Auth {
	
	private $account_id;
	private $type;
	private $email;
	private $first_name;
	private $last_name;
	
	function __construct() {
		$CI =& get_instance();
		$this->account_id	= $CI->session->userdata('account_id');
		$this->type			= $CI->session->userdata('type');
		$this->email		= $CI->session->userdata('email');
		$this->first_name	= $CI->session->userdata('first_name');
		$this->last_name	= $CI->session->userdata('last_name');
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
	 * Automatically dislplays an error message if not logged in.
	 * Prevent further actions.
	 * */
	function check_logged_in() {
		if (!$this->is_logged_in()) {
			show_error('You are not logged in');
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

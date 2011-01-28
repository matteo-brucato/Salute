<?php
class Account_model extends Model {
	
	function __construct() {
		parent::Model();
		$this->load->database();
	}
	
	
	//deactivate account
	//$inputs is of the form( account_id)
	//deactivates the account
	function deactivate($inputs){
		
		$data = array('active' => FALSE);
		$this->db->update('Accounts', $data, 'account_id' => $inputs);
	}
	
	//adds an account
	//$inputs is of the form( email, password)
	//inserts new account into the Accounts table
	function add_account($inputs){
	
		$data = array( 'email' => $inputs[0], 'password' => $inputs[1]);
		$this->db->insert( 'Accounts', $data);
	}
	
	
	//update account information (email and password)
	//$inputs is fo the form (account_id, email, password)
	//updates the Accounts table
	function update_account($inputs){
	
		$data = array( 'email' => $inputs[1], 'password' => $inputs[2]);
		this->db->update( 'Accounts', $data, array('account_id' => $inputs[0]);
	}
?>

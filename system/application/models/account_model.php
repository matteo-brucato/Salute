<?php
class Account_model extends Model {
	
	function __construct() {
		parent::Model();
		$this->load->database();
	}


	/**
	 * Deactivates an account
	 * 
	 * @params $inputs
	 *  Is of the form: array(account_id)
	 * @return
	 * -1 in case of error in a query
	 * -4 if account doesnt exist
	 *  0 if everything goes fine and the account is deactivated
	 * */
	function deactivate($inputs){
		
		//test to see if the account exists
		$sql = "SELECT *
			FROM accounts
			WHERE account_id = ?";
		$query = $this->db->query($sql, $inputs);
		
		if ($this->db->trans_status() === FALSE)
			return -1;
		if( $query->num_rows() < 1)
			return -4;
		
		//$data = array('active' => FALSE);
		//$this->db->update('Accounts', $data, 'account_id' => $inputs);
		
		$sql = "UPDATE accounts
			SET active = FALSE
			WHERE account_id = ?";
		$query = $this->db->query($sql, $inputs);
		
		if ($this->db->trans_status() === FALSE)
			return -1;
			
		return 0;
	}
	
	
	/**
	 * Create an account
	 * 
	 * @params $inputs
	 * 	 Is of the form: array(account_id)
	 * @return
	 * -1 in case of error in a query
	 *  Inserts new account into the accounts table
	 *  Array with the account_id of the newly created account
	 * */
	function add_account($inputs){
	
		//$data = array( 'email' => $inputs[0], 'password' => $inputs[1]);
		//$this->db->insert( 'Accounts', $data);
		
		$sql = "INSERT INTO accounts (email, password)
			VALUES (?, ?)";
		$query = $this->db->query($sql, $inputs);
		
		if ($this->db->trans_status() === FALSE)
			return -1;
		
		$sql = "SELECT account_id
			FROM accounts
			WHERE email = ?";
		$query = $this->db->query($sql, array($inputs[0]));
		
		if ($this->db->trans_status() === FALSE)
			return -1;
			
		return $this->result_array();	
	}
	
	/**
	 * Update account information (email and password)
	 * 
	 * @params $inputs
	 * 	 Is of the form: array(account_id, email, password)
	 * @return
	 * -1 in case of error in a query
	 * -4 if account doesnt exist
	 *  0 if everything goes fine and the account information is updated
	 * */	
	function update_account($inputs){
		
		//test to see if the account exists
		$sql = "SELECT *
			FROM accounts
			WHERE account_id = ?";
		$query = $this->db->query($sql, array($inputs[0]));
		
		if ($this->db->trans_status() === FALSE)
			return -1;		
		if( $query->num_rows() < 1)
			return -4;
	
		//$data = array( 'email' => $inputs[1], 'password' => $inputs[2]);
		//this->db->update( 'Accounts', $data, array('account_id' => $inputs[0]);
		
		$sql = "UPDATE accounts
			SET email = ?, password = ?
			WHERE account_id = ?";
		$query = $this->db->query($sql, array($inputs[1], $inputs[2], $inputs[0]));
		
		if ($this->db->trans_status() === FALSE)
			return -1;
			
		return 0;
	}
	
	
	/**
	 * Gets the password of the account
	 * 
	 * @param $inputs
	 *   Is of the form: array(email)
	 * @return
	 *  -1 in case of error in a query
	 *   Array with the passowrd
	 *   empty array() if the email does not exist
	 * */
	 function get_password($inputs){
	 
	 	$sql = "SELECT A.password
			FROM account A
			WHERE A.email = ?";
	 	$query = $this->db->query($sql, $inputs);
		
		if ($this->db->trans_status() === FALSE)
			return -1;	
		if( $query->num_rows() < 1)
			return array();	 	
	 	
	 	return $query->result_array();
	 }
}
?>

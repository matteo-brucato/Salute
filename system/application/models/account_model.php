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
		
		//$data = array('active' => FALSE);
		//$this->db->update('Accounts', $data, 'account_id' => $inputs);
		
		$sql = "UPDATE Accounts
			SET active = FALSE
			WHERE account_id = ?";
		$query = $this->db->query($sql, $inputs);
		
	}
	
	//adds an account
	//$inputs is of the form( email, password)
	//inserts new account into the Accounts table
	//returns the account_id of the account just created
	function add_account($inputs){
	
		//$data = array( 'email' => $inputs[0], 'password' => $inputs[1]);
		//$this->db->insert( 'Accounts', $data);
		
		$sql = "INSERT INTO Accounts (email, password)
			VALUES (?, ?)";
		$query = $this->db->query($sql, $inputs);
		
		$sql = "SELECT account_id
			FROM Accounts
			WHERE email = ?"
		$query = $this->db->query($sql, array($inputs[0]));
		$result = $this->result_array();
		return $result;
		
	}
	
	
	//update account information (email and password)
	//$inputs is fo the form (account_id, email, password)
	//updates the Accounts table
	function update_account($inputs){
	
		//$data = array( 'email' => $inputs[1], 'password' => $inputs[2]);
		//this->db->update( 'Accounts', $data, array('account_id' => $inputs[0]);
		
		$sql = "UPDATE Accounts
			SET email = ?, password = ?
			WHERE account_id = ?"
		$query = $this->db->query($sql, array($inputs[1], $inputs[2], $inputs[0]));
	}
	
	
	/**
	 * Gets the password of the account
	 * 
	 * @param $inputs
	 *   Is of the form: array(email)
	 * @return
	 *   passowrd or NULL if the email does not exist
	 * */
	 function get_password($inputs){
	 
	 	$sql = "SELECT A.password
	 		FROM Account A
	 		WHERE A.email = ?"
	 	$query = $this->db->query($sql, $inputs);
	 	$result = $this->result_array();
	 	if( count($result) == 1)
	 		return $result;
	 	return FALSE;
	 
	 }
?>

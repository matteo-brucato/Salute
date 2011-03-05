<?php
/**
 * @file account_model.php
 * @brief Model to give access to the account table in the database
 *
 * @defgroup mdl Models
 * @ingroup mdl
 * @{
 */

class Account_model extends Model {
	
	function __construct() {
		parent::Model();
		$this->load->database();
	}

	/**
	 * Checks if an account is active
	 * 
	 * @params $inputs
	 *  Is of the form: array(account_id)
	 * @return
	 * -1 in case of error in a query
	 * FALSE if not active
	 * TRUE if active
	 * */
	function is_active($account_id){
		$sql = "SELECT *
				FROM accounts a
				WHERE a.active = 't' AND a.account_id = ?";
		$query = $this->db->query($sql, $account_id);
											  
		if ($this->db->trans_status() === FALSE)
			return -1;
			
		return ($query->num_rows() > 0);
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
	 * Activates an account
	 * 
	 * @params $inputs
	 *  Is of the form: array(account_id)
	 * @return
	 * -1 in case of error in a query
	 * -4 if account doesnt exist
	 *  0 if everything goes fine and the account is deactivated
	 * */
	function activate($inputs){
		
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
			SET active = TRUE
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
		$query = $this->db->query($sql, $inputs['email']);
		
		if ($this->db->trans_status() === FALSE)
			return -1;
			
		return $query->result_array();	
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
	 * Checks if id is an account
	 * 
	 * @param $inputs
	 *   Is of the form: array(account_id)
	 * @return
	 *  -1 in case of error in a query
	 *   FALSE in case of no match
	 *   TRUE otherwise
	 * */
	function is_account($inputs){
	
		$sql = "SELECT *
			FROM accounts A
			WHERE A.account_id = ?";
		$query = $this->db->query($sql, $inputs);
		
		if ($this->db->trans_status() === FALSE)
			return -1;	
		if( $query->num_rows() < 1)
			return FALSE;

		return TRUE;
	}
	
	/**
	 * Checks if id is a public account
	 * 
	 * @param $inputs
	 *   Is of the form: array(account_id)
	 * @return
	 *  -1 in case of error in a query
	 *   NULL in case of no such account
	 *   TRUE if it's public, FALSE otherwise
	 * */
	function is_public($inputs){
	
		$sql = "SELECT *
			FROM accounts A
			WHERE A.account_id = ?";
		$query = $this->db->query($sql, $inputs);
		
		if ($this->db->trans_status() === FALSE)
			return -1;
		if ($query->num_rows() < 1)
			return NULL;
		
		$res = $query->result_array();
		return ($res[0]['private'] == 'f');
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
	 function get_account($inputs){
	 
	 	$sql = "SELECT *
			FROM accounts A
			WHERE A.email = ?";
	 	$query = $this->db->query($sql, $inputs);
		
		if ($this->db->trans_status() === FALSE)
			return -1;	
		if( $query->num_rows() < 1)
			return array();	 	

		return $query->result_array();
	 }
	 
	/**
	 * Gets the email of the account
	 * 
	 * @param $inputs
	 *   Is of the form: array($account_id)
	 * @return
	 *  -1 in case of error in a query
	 *   Array with the email
	 *   empty array() if the email does not exist
	 * */
	 function get_account_email($inputs){
	 
	 	$sql = "SELECT *
			FROM accounts A
			WHERE A.account_id = ?";
	 	$query = $this->db->query($sql, $inputs);
		
		if ($this->db->trans_status() === FALSE)
			return -1;	
		if( $query->num_rows() < 1)
			return array();	 	

		return $query->result_array();
	 }
	 
	/**
	 * Updates the privacy level of an account
	 * 
	 * @param $inputs
	 *   Is of the form: array($account_id, privacy level)
	 * @return
	 *  -1 in case of error in a query
	 *   0 if everything goes fine
	 * */
	 function update_privacy($inputs) {
		 
		 $sql = "UPDATE accounts
				SET private = ?
				WHERE account_id = ?";
		$query = $this->db->query($sql, array($inputs[1], $inputs[0]));
		if ($this->db->trans_status() === FALSE)
			return -1;
		
		return 0; 
	 }
}
/** @} */
?>

<?php
/**
 * @file settings.php
 * @brief Controller to manage user settings
 *
 * @defgroup ctr Controllers
 * @ingroup ctr
 * @{
 */

class Settings extends Controller {

	function __construct(){
		parent::Controller();
		$this->load->library('ui');
		$this->load->library('auth');
		$this->load->model('account_model');
	}

	/**
	 * Default Settings View: lists possible actions:
	 * 		Change Password
	 * 		Change Email
	 * 		Deactivate
	 * */
	function index(){
//		$this->auth->check_logged_in();
		if ($this->auth->check(array(auth::CurrLOG)) !== TRUE) {
			return;
		}
		$this->ui->set(array(
				$this->load->view('mainpane/settings', '', TRUE)
			));
	}
	
	/**
	 * Loads form  to let user change their password
	 * */
	function change_password(){
		//$this->auth->check_logged_in();	
		if ($this->auth->check(array(auth::CurrLOG)) !== TRUE) {
			return;
		}
		$this->ui->set(array($this->load->view('mainpane/forms/change_password', '', TRUE)));
		
	}

	/**
	 * Changes user password
	 * @input new password
	 * @return error || email confirmation + success message
	 * */
	function change_password_do(){
		//$this->auth->check_logged_in();	
		if ($this->auth->check(array(auth::CurrLOG)) !== TRUE) {
			return;
		}	
		
		$password = $this->input->post('password');
		if ($password == NULL){
			$this->ui->set_error('Password Invalid','Missing Argument');
			return;
		}
		$check = $this->account_model->update_account(array($this->auth->get_account_id(),$this->auth->get_email(),$password));
		if ($check === -1){
			$this->ui->set_query_error();
			return;
		} else if ($check === -4){
			$this->ui->set_error('Account does not exist!');
			return;
		}
		
		$this->load->helper('email');
		send_email(
			'salute-noreply@salute.com',
			$this->auth->get_email(),
			'Your password has been changed',
			'Your password has been successfully changed. It is now: '.$password.'. '.
				'Click <a href="https://'.$_SERVER['SERVER_NAME'].'/">here</a> to login.'
		);
		
		$msg = 'Your password has been changed. A confirmation email has been sent for your records.';
		$this->ui->set_message($msg,'Confirmation');
	}

	/**
	 * Loads form for user to change their email
	 * */
	function change_email(){
	//	$this->auth->check_logged_in();	
		if ($this->auth->check(array(auth::CurrLOG)) !== TRUE) {
			return;
		}	
	
		$this->ui->set(array($this->load->view('mainpane/forms/change_email', '', TRUE)));
	}

	/**
	 * Changes user's email
	 * @param new email address
	 * @return error || confirmation email + success message
	 * */
	function change_email_do(){
	//	$this->auth->check_logged_in();	
		if ($this->auth->check(array(auth::CurrLOG)) !== TRUE) {
			return;
		}	
			
		$email = $this->input->post('email');
		if ($email == NULL){
			$this->ui->set_error('Email Invalid','Missing Arguments');
			return;
		}	
			
		$password = $this->account_model->get_account($this->auth->get_email());
		if ($password == NULL){
			$this->ui->set_error('Failed to retrieve password.');
			return;
		} else if( $password === -1 ){
			$this->ui->set_query_error();
			return;
		}
			
		$check = $this->account_model->update_account(array($this->auth->get_account_id(),$email,$password[0]['password']));
			
		if ($check === -1){
			$this->ui->set_query_error();
			return;
		} else if ($check === -4){
			$this->ui->set_error('Account does not exist!');
			return;
		}
		
		$this->load->helper('email');
		send_email(
			'salute-noreply@salute.com',
			$email,
			'Your email has been changed.',
			'Your email has been successfully changed. It is now: '.$email.'. '.
				'Click <a href="https://'.$_SERVER['SERVER_NAME'].'/">here</a> to login.'
		);
		
		// Update session cookie
		$this->session->set_userdata(array('email' => $email));
		
		$this->ui->set_message('Your email has been changed. A confirmation has been sent to your email.','Confirmation');
		
	}
	
	/** 
	 * Deactivate Account
	 * @return Deactivation Confirmation + Email. || error
	 * @todo popup: are you sure?
	 **/ 
	function deactivate() {
//		$this->auth->check_logged_in();
		if ($this->auth->check(array(auth::CurrLOG)) !== TRUE) {
			return;
		}	
				
		$check = $this->account_model->deactivate($this->auth->get_account_id());
		if ($check === -1){
			$this->ui->set_query_error();
			return;
		} else if ($check === -4){
			$this->ui->set_error('Account does not exist!');
			return;
		}
		
		$this->load->helper('email');
		send_email(
			'salute-noreply@salute.com',
			$this->auth->get_email(),
			'Account Deactivated.',
			'Your Account has been deactivated.'
		);
		
		/*$this->load->library('email');
		$config['mailtype'] = 'html';
		$this->email->initialize($config);
		$this->email->from('salute-noreply@salute.com');
		$this->email->to($this->auth->get_email());
		$this->email->subject('Account Deactivated.');
		$this->email->message('Your Account has been deactivated.');
		$this->email->send();*/
		
		$this->ui->set_message('Your account has been deactivated.','Confirmation');
		$this->session->sess_destroy();
	}
	
	/**
	 * Activate Account Prompt 
	 * loads a statement that user is deactive. Link to reactivate
	 * @param account_id -- the account_id of the user who tried to login but is deactive
	 * @todo popup: are you sure?
	 **
	function activate($account_id = NULL){
		if ($this->auth->check(array(auth::ACCOUNT,$account_id)) !== TRUE) {
			return;
		}	
	
		$msg = 'Your Account is de-active. '.
		'Click <a href="/settings/activate_do/'.$account_id.'">here</a> to reactivate.';
		$this->ui->set_message($msg);
	}
	
	/** 
	 * Activate Account
	 * @param account_id -- the account_id of the user who tried to login but is deactive
	 * @return error || Confirmation statement + Link to login. 
	 **
	function activate_do($account_id = NULL){
		$this->ui->set_message('k','Confirmation');
		return;
		if ($this->auth->check(array(auth::ACCOUNT,$account_id)) !== TRUE) {
			return;
		}
		$results = $this->account_model->activate(array($account_id));
		if ($results === -1){
			$this->ui->set_query_error();
			return;
		}
		$msg = 'Your Account has been reactivated. Click <a href="/home">here</a> to login.';
		$this->ui->set_message($msg,'Confirmation');
	}*/
	
	
	/**
	 * Loads the view to change the privacy level of an account
	 * 
	 * @param $inputs
	 *   Is of the form:
	 * @return
	 *	 Loads the view
	 * */
	function change_privacy() {
			
		if ($this->auth->check(array(auth::CurrLOG, auth::CurrPAT)) !== TRUE) {
			return;
		}
		
		$privacy = $this->account_model->is_public($this->auth->get_account_id());
		if ($privacy === -1){
			$this->ui->set_query_error();
			return;
		} elseif ( $privacy === NULL ){
			$this->ui->set_message('Account does not exist.','Error');
		}
		else
			$this->ui->set(array($this->load->view('mainpane/forms/change_privacy', array( 'privacy' => $privacy), TRUE)));		
	}
	
	/**
	 * Loads the view to change the picture of an account
	 * 
	 * @param $inputs
	 *   Is of the form:
	 * @return
	 *	 Loads the view
	 * */
	function change_picture() {
		
		if ($this->auth->check(array(auth::CurrLOG)) !== TRUE) {
			return;
		}
		
		$this->ui->set(array($this->load->view('mainpane/forms/change_picture', '', TRUE)));
	}
	
	/**
	 * Loads the view to change the picture of an account
	 * 
	 * @param $inputs
	 *   Is of the form:
	 * @return
	 *	 Loads the view
	 * */
	function remove_picture($aid = NULL) {
		
		if ($this->auth->check(array(
			auth::CurrLOG,
			auth::ACCOUNT, $aid)) !== TRUE) {
			return;
		}
		
		if ($aid !== $this->auth->get_account_id()) {
			$this->ui->set_error('Only the owner can remove his/her account picture');
			return;
		}
		
		$filepath = './resources/images/account_pictures/'.$aid.'.jpg';
		
		if (! is_file($filepath)) {
			$this->ui->set_error('There is no picture available to remove', 'Error');
			return;
		}
		
		$unlink = unlink($filepath);
		if ($unlink === FALSE) {
			$this->ui->set_error('Cannot remove the picture', 'Server error');
			return;
		}
		
		$this->ui->set_message('Picture successfully removed', 'Confirmation');
	}
	
	/**
	 * Changes the privacy level of an account with another account
	 * 
	 * @param $inputs
	 *   
	 * @return
	 *	 Display message
	 * */
	function change_privacy_do(){
		
		if ($this->auth->check(array(auth::CurrLOG, auth::CurrPAT)) !== TRUE) {
			return;
		}
		
		$new_level = $this->input->post('level');
		if ($new_level == NULL) {
			$this->ui->set_error('Select a level.','Missing Arguments'); 
			return;
		}
		
		$change = $this->account_model->update_privacy(array($this->auth->get_account_id(), $new_level));
		if ($change === -1){
			$this->ui->set_query_error();
			return;
		}elseif ($change === 0)
			$this->ui->set_message('Privacy level updated.','Confirmation');
	}
}
/** @} */
?>

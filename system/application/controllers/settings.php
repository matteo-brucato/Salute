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
		$this->load->library('ajax');	
		$this->load->library('auth');
	}

	// Default
	function index(){
		$this->auth->check_logged_in();
		// load view that provides the following links: 
		//	deactivate account(link to fn below)
		//	edit my info(link to profile controller fn called 'edit_info')
		// 	edit permissions(link to list of med recs)
	}
	
	function change_password(){
		$this->auth->check_logged_in();	
		
		$this->ajax->view(array('Change Password',''));
		
	}

	function change_password_do(){
			$this->auth->check_logged_in();	
			$this->load->model('account_model');		
			$password = $this->input->post('password');
			if ($password == NULL){
				show_error('Password Invalid',500);
				return;
			}
			$check = $this->account_model->update_account($this->auth->get_account_id(),$this->auth->get_email(),$password);
			if ($check === -1){
				$this->ajax->view(array('Query Error!',''));
			}
			else if ($check === -4){
					$this->ajax->view(array('Account does not exist!',''));
			}
			
			$this->load->library('email');
			$config['mailtype'] = 'html';
			$this->email->initialize($config);
			$this->email->from('salute-noreply@salute.com');
			$this->email->to($email);
			$this->email->subject('Your password has been changed.');
			$this->email->message(
				'Your password has been successfully changed. It is now: '.$password.'. '.
				'Click <a href="https://'.$_SERVER['SERVER_NAME'].'/">here</a> to login.');

			$this->email->send();
									
			$view = 'Your password has been changed. A confirmation email has been sent for your records.';
			$this->ajax->view(array($view,''));	
	}

	function change_email(){
		$this->auth->check_logged_in();	
		
		$this->ajax->view(array('Change Email',''));
	}

	function change_email_do(){
			$this->auth->check_logged_in();	
			$this->load->model('account_model');		
			
			$email = $this->input->post('email');
			if ($email == NULL){
				show_error('Email Invalid',500);
				return;
			}	
			$password = $this->account_model->get_password($this->auth->get_email());
			if ($password == NULL){
				show_error('Failed to retrieve password.',500);
				return;
			} else if( $password === -1 ){
				show_error('Query Error.',500);
				return;
			}
			
			$check = $this->account_model->update_account($this->auth->get_account_id(),$email,$this->auth->get_password());
			if ($check === -1){
				$this->ajax->view(array('Query Error!',''));
				return;
			} else if ($check === -4){
					$this->ajax->view(array('Account does not exist!',''));
					return;
			}
			
			$this->load->library('email');
			$config['mailtype'] = 'html';
			$this->email->initialize($config);
			$this->email->from('salute-noreply@salute.com');
			$this->email->to($email);
			$this->email->subject('Your password has been changed.');
			$this->email->message(
				'Your email has been successfully changed. It is now: '.$email.'. '.
				'Click <a href="https://'.$_SERVER['SERVER_NAME'].'/">here</a> to login.');

			$this->email->send();
			
			$this->ajax->view(array('Your email has been changed. A confirmation has been sent to your email.',''));
			
	}
	
	// Deactivate Account
	// @todo: popup: are you sure?
	// @attention: need a reactivate function
	function deactivate() {
		$this->auth->check_logged_in();
				
		$this->load->model('account_model');
		$check = $this->account_model->deactivate($this->auth->get_account_id());
		if ($check === -1){
			$this->ajax->view(array('Query Error!',''));
			return;
		} else if ($check === -4){
			$this->ajax->view(array('Account does not exist!',''));
			return;
		}
		
		// Confirmation email: should have a link to reactivate? 
		$this->load->library('email');
		$config['mailtype'] = 'html';
		$this->email->initialize($config);
		$this->email->from('salute-noreply@salute.com');
		$this->email->to($this->auth->get_email());
		$this->email->subject('Account Deactivated.');
		$this->email->message('Your Account has been deactivated.');
		$this->email->send();
		
		$this->ajax->view(array('Your account has been deactivated.',''));
	}
	
	function activate(){
		
	}
}
/** @} */
?>

<?php
/**
 * @file messages.php
 * @brief Controller to send/receive/view messages
 *
 * @defgroup ctr Controllers
 * @ingroup ctr
 * @{
 */

class Messages extends Controller {

// 	private $type;

	function __construct(){
		parent::Controller();
		$this->load->library('ajax');	
		$this->load->library('auth');
		// $this->type = $this->session->userdata('type');	
	}

	// Default: call inbox function
	function index(){
		$this->auth->check_logged_in();
	}

  	/* List all messages in Inbox */      	   
	function inbox() { 
		$this->auth->check_logged_in();
	}

	/*View only sent messages*/
	function sent() {
		$this->auth->check_logged_in();
	}

	/*View only messages saved as a draft*/	
	function drafts(){
		$this->auth->check_logged_in();
	}
	
	/*
	 * Load Form to Compose an Email
	 * @param the account_id of the recipient
	 * */
	function compose($account_id) {
		$this->auth->check_logged_in();
		$this->load->model('account_model');
		$this->load->model('patient_model');
		$this->load->model('hcp_model');
		$is_patient= $this->patient_model->is_patient(array($account_id));
		$is_hcp = $this->hcp_model->is_hcp(array($account_id));
		
		if($is_patient === -1 || $is_hcp === -1){
			show_error('Query Error!',500);
			return;
		}
		if($is_patient === TRUE)
			$res = $this->patient_model->get_patient(array($account_id));	
		else if ($is_hcp === TRUE)
			$res = $this->hcp_model->get_hcp(array($account_id));			
		else{
			show_error('Internal Server Error',500);
			return;
		}
		$acc = $this->account_model->get_account_email(array($account_id));
		
		if($res === -1 || $acc === -1){
			show_error('Query Error!',500);
			return;
		}
		$this->ajax->view(array($this->load->view('mainpane/send_message', array('info'=> $res[0],'acc'=>$acc[0]), TRUE),''));
	}
	
	/**
	 * Sends the email (called by compose function)
	 * @input email address(to), subject, the message(body)
	 * @return error || confirmation 
	**/
	function send($to) {
		$this->auth->check_logged_in();
		$this->load->model('account_model');
		
		$subject = $this->input->post('subject');
		$body = $this->input->post('body');
		
		$this->load->library('email');
		$config['mailtype'] = 'html';
		$this->email->initialize($config);
		$this->email->from($this->auth->get_email());
		$this->email->to($to);
		$this->email->subject($subject);
		$this->email->message($body);

		$this->email->send();
		$this->ajax->view(array('Your password has been sent.',''));
	}	

	// Delete an email
	function delete() {
		$this->auth->check_logged_in();
	}
}
/** @} */
?>

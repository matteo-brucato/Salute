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
		$this->load->library('ui');	
		$this->load->library('auth');
		$this->load->model('account_model');
		$this->load->model('patient_model');
		$this->load->model('hcp_model');
		// $this->type = $this->session->userdata('type');	
	}

	/**
	 * Default method
	 * 
	 * @todo Not implemented yet
	 * */
	function index(){
		$this->auth->check_logged_in();
	}

  	/**
	 * Call inbox function
	 * 
	 * @todo Not implemented yet
	 * */
	function inbox() { 
		$this->auth->check_logged_in();
	}

	/**
	 * View only sent messages
	 * 
	 * @todo Not implemented yet
	 * */
	function sent() {
		$this->auth->check_logged_in();
	}

	/**
	 * View only messages saved as a draft
	 * 
	 * @todo Not implemented yet
	 * */
	function drafts(){
		$this->auth->check_logged_in();
	}
	
	/**
	 * Load Form to Compose an Email
	 * @param the account_id of the recipient
	 * */
	function compose($account_id) {
		if ($this->auth->check(array(
			auth::CurrLOG,
			auth::CurrCONN, $account_id
		)) !== TRUE) return;
		
		$is_patient= $this->patient_model->is_patient(array($account_id));
		$is_hcp = $this->hcp_model->is_hcp(array($account_id));
		
		if($is_patient === -1 || $is_hcp === -1){
			$this->ui->set_query_error();
			return;
		}
		if($is_patient === TRUE)
			$res = $this->patient_model->get_patient(array($account_id));	
		else if ($is_hcp === TRUE)
			$res = $this->hcp_model->get_hcp(array($account_id));			
		else{
			$this->ui->set_error('Internal Server Error','server');
			return;
		}
		$acc = $this->account_model->get_account_email(array($account_id));
		
		if($res === -1 || $acc === -1){
			$this->ui->set_query_error();
			return;
		}
		$this->ui->set(array($this->load->view('mainpane/forms/send_message', array('info'=> $res[0],'acc'=>$acc[0]), TRUE)));
	}
	
	/**
	 * Sends the email (called by compose function)
	 * @input email address(to), subject, the message(body)
	 * @return error || confirmation 
	**/
	function send($to) {
		if ($this->auth->check(array(
			auth::CurrLOG,
			auth::CurrCONN, $account_id
		)) !== TRUE) return;
		
		$acc = $this->account_model->get_account_email(array($to));
		if($acc === -1){
			$this->ui->set_query_error();
			return;
		}
		$to_email = $acc[0]['email'];
		
		$subject = $this->input->post('subject');
		$body = $this->input->post('body');
	
		$this->load->library('email');
		$config['mailtype'] = 'html';
		$this->email->initialize($config);
		$this->email->from($this->auth->get_email());
		$this->email->to($to_email);
		$this->email->subject($subject);
		$this->email->message($body);

		$this->email->send();
		$this->ui->set_message('Your message has been sent.','Confirmation');
	}	

	/**
	 * Delete an email
	 * 
	 * @todo Not implemented yet
	 * */
	function delete() {
		$this->auth->check_logged_in();
	}
}
/** @} */
?>

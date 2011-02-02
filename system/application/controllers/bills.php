<?php
/**
 * @file bills.php
 * @brief Controller to handle bills
 *
 * @defgroup ctr Controllers
 * @ingroup ctr
 * @{
 */

class Bills extends Controller {

	function __construct(){
		parent::Controller();
		$this->load->library('ajax');	
		$this->load->library('auth');
	}

	/* Default: call all function */
	function index(){
		//$this->auth->check_logged_in();
		//$this->ajax->redirect('/bills/all');
		$this->all();
	}
  	
	/* List all bills  */      	   
	// @todo: patient main panel view : to list bills : ( Date, Title, Doctor Name, Amount, Status(unpaid/paid/pending), Actions (Pay Now, View Receipt)  
	// @todo: doctor main panel view : to list bills : ( Date, Title, Doctor Name, Amount, Status(unpaid/paid/pending), Actions (Delete,View Receipt)  
	function all()	{
		$this->auth->check_logged_in();
		$this->load->model('bills_model');
		if($this->auth->get_type() === 'patient'){
			$results = $this->bills_model->view_all(array($this->auth->get_account_id(),$this->auth->get_type()));
			$this->ajax->view(array($this->load->view('mainpane/____________', $results, TRUE),''));
		} 
		else if($this->auth->get_type() === 'doctor'){
			$results = $this->bills_model->view_all(array($this->auth->get_account_id(),$this->auth->get_type()));
			$this->ajax->view(array($this->load->view('mainpane/____________', $results, TRUE),''));
		} 
		else{
			show_error('Error: unable to list your bills.', 500);
			return;		
		}
	}

	/* List Current Bills */
	function current(){
		$this->auth->check_logged_in();
		$this->load->model('bills_model');
		if($this->auth->get_type() === 'patient'){
			$results = $this->bills_model->view_current(array($this->auth->get_account_id(),$this->auth->get_type()));
			$this->ajax->view(array($this->load->view('mainpane/____________', $results, TRUE),''));
		} 
		else if($this->auth->get_type() === 'doctor'){
			$results = $this->bills_model->view_current(array($this->auth->get_account_id(),$this->auth->get_type()));
			$this->ajax->view(array($this->load->view('mainpane/____________', $results, TRUE),''));
		} 
		else{
			show_error('Error: unable to list your bills.', 500);
			return;		
		}
	}

	/* Lists past bills */	
	function past() {
		$this->auth->check_logged_in();
		$this->load->model('bills_model');
		if($this->auth->get_type() === 'patient'){
			$results = $this->bills_model->view_past(array($this->auth->get_account_id(),$this->auth->get_type()));
			$this->ajax->view(array($this->load->view('mainpane/____________', $results, TRUE),''));
		} 
		else if($this->auth->get_type() === 'doctor'){
			$results = $this->bills_model->view_past(array($this->auth->get_account_id(),$this->auth->get_type()));
			$this->ajax->view(array($this->load->view('mainpane/____________', $results, TRUE),''));
		} 
		else{
			show_error('Error: unable to list your bills.', 500);
			return;		
		}
	}

	// load form , charge patient an amount for an procedure/appointment/test, upload itemized receipt	
	// update database
	// Only available for doctors
	function issue_new_bill() {
		$this->auth->check_logged_in();
		$this->load->model('bills_model');
		if($this->auth->get_type() === 'doctor'){
			$patient_id = $this->input->post('patient_id');
			$amount = $this->input->post('amount');
			$description = $this->input->post('description');
			$due_date = $this->input->post('due_date');
			$this->bills_model->issue_bill(array($patient_id,$this->auth->get_account_id(),$amount,$description,$due_date));
		}
		else{
			show_error('Error: only doctors can issue bills.', 500);
			return;		
		}
		$this->ajax->redirect('/bills');

	}

	// only available to patient: pay a bill
	function pay($bill_id) {
		$this->auth->check_logged_in();
		$this->load->model('bills_model');
		if($this->auth->get_type() === 'patient' && $this->bills_model->is_mybill(array($this->auth->get_account_id(),$bill_id)) ){
			$this->bills_model->pay_bill(array($this->auth->get_account_id(),$amount));
		}
		else{
			show_error('Error: only patients can pay bills.', 500);
			return;
		}
		$this->ajax->redirect('/bills');
	}

}
/** @} */
?>

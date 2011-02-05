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
		//echo 'asd';
		$this->all();
	}
  	
	/* List all bills  */      	   
	// @todo: patient main panel view : to list bills : ( Date, Title, hcp Name_first,hcp Name_last, Amount, Status(unpaid/paid/pending), Actions (Pay Now, View Receipt)  
	// @todo: hcp main panel view : to list bills : ( Date, Title, patient name first, patient name last, Amount, Status(unpaid/paid/pending), Actions (Delete,View Receipt)  
	// array $results sent to view in order:  bill_id, first_name(hcp or Patient), last_name(hcp or patient), B.amount, B.descryption, B.due_date, B.cleared
	function all()	{
		
		$this->auth->check_logged_in();
		$this->load->model('bills_model');

		if($this->auth->get_type() === 'patient'){
			$results = $this->bills_model->view_all(array($this->auth->get_account_id(),$this->auth->get_type()));
			$sidepane = 'sidepane/patient-profile';
		}		
		else if($this->auth->get_type() === 'hcp'){
			$results = $this->bills_model->view_all(array($this->auth->get_account_id(),$this->auth->get_type()));
			$sidepane = 'sidepane/hcp-profile';
		} 
		else{
			show_error('Error: unable to list your bills.', 500);
			return;		
		}
		switch ($results) {
			case -1:
				$mainview = 'Query error!';
				$sideview = '';
				break;
			default:
				$mainview = $this->load->view('mainpane/list_bills',
					array('list_name' => 'My Bills', 'list' => $results) , TRUE);
				$sideview = $this->load->view($sidepane, '', TRUE);
				break;
		}
	
		// Give results to the client
		$this->ajax->view(array($mainview,$sideview));
	}
	/* List Current Bills */
	function current(){
		$this->auth->check_logged_in();
		$this->load->model('bills_model');
		if($this->auth->get_type() === 'patient'){
			$results = $this->bills_model->view_current(array($this->auth->get_account_id(),$this->auth->get_type()));
			$sidepane = 'sidepane/patient-profile';
			//$this->ajax->view(array($this->load->view('mainpane/____________', $results, TRUE),''));
		} 
		else if($this->auth->get_type() === 'hcp'){
			$results = $this->bills_model->view_current(array($this->auth->get_account_id(),$this->auth->get_type()));
			$sidepane = 'sidepane/hcp-profile';
			//$this->ajax->view(array($this->load->view('mainpane/____________', $results, TRUE),''));
		} 
		else{
			show_error('Error: unable to list your bills.', 500);
			return;		
		}
		switch ($results) {
			case -1:
				$mainview = 'Query error!';
				$sideview = '';
				break;
			default:
				$mainview = $this->load->view('mainpane/list_bills',
					array('list_name' => 'My Current Bills', 'list' => $results) , TRUE);
				$sideview = $this->load->view($sidepane, '', TRUE);
				break;
		}
		$this->ajax->view(array($mainview,$sideview));
	}

	/* Lists past bills */	
	function past() {
		$this->auth->check_logged_in();
		$this->load->model('bills_model');
		if($this->auth->get_type() === 'patient'){
			$results = $this->bills_model->view_past(array($this->auth->get_account_id(),$this->auth->get_type()));
			$sidepane = 'sidepane/patient-profile';
		} 
		else if($this->auth->get_type() === 'hcp'){
			$results = $this->bills_model->view_past(array($this->auth->get_account_id(),$this->auth->get_type()));
			$sidepane = 'sidepane/hcp-profile';
		} 
		else{
			show_error('Error: unable to list your bills.', 500);
			return;		
		}
		switch ($results) {
			case -1:
				$mainview = 'Query error!';
				$sideview = '';
				break;
			default:
				$mainview = $this->load->view('mainpane/list_bills',
					array('list_name' => 'My Past Bills', 'list' => $results) , TRUE);
				$sideview = $this->load->view($sidepane, '', TRUE);
				break;
		}
		$this->ajax->view(array($mainview,$sideview));
	}

	function issue($patient_id){
		echo 'start';

		$this->auth->check_logged_in();
		$this->load->model('patient_model');
		if( $this->auth->get_type() === 'hcp' ){
			echo 'before get patient';
			$results = $this->patient_model->get_patient(array($patient_id));	
			echo 'after get patient';
			switch( $results ) {
				case -1:
					$mainview = 'Query error!';
					$sideview = '';
					break;
				default:
					if( sizeof($results) < 1 ){
						show_error('Error: unable to create a bill.', 500);
						return;	
					}
					else{
						echo 'before calling view';
						$this->ajax->view(array($this->load->view('mainpane/issue_bill', array('results'=>$results), TRUE), $this->load->view('sidepane/hcp-profile', '', TRUE)));
					}						
			}
			
			
		}
		//check if patient first
		//get full tuple patient
		//if patient, provide form

	}
	
	// load form , charge patient an amount for an procedure/appointment/test, upload itemized receipt	
	// update database
	// Only available for hcps
	function issue_new_bill() {
		//check patient and doctor
		$this->auth->check_logged_in();
		$this->load->model('bills_model');
		if($this->auth->get_type() === 'hcp'){
			$patient_id = $this->input->post('patient_id');
			$amount = $this->input->post('amount');
			$description = $this->input->post('descryption');
			$due_date = $this->input->post('due_date');
			$this->bills_model->issue_bill(array($patient_id,$this->auth->get_account_id(),$amount,$description,$due_date));
		}
		else{
			show_error('Error: only hcps can issue bills.', 500);
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

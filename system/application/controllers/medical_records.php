<?php
/**
 * @file medical_records.php
 * @brief Controller to handle medical records
 *
 * @defgroup ctr Controllers
 * @ingroup ctr
 * @{
 */

class Medical_records extends Controller {

	function __construct(){
		parent::Controller();
		$this->load->library('ui');
		$this->load->library('auth');
		$this->load->helper(array('form', 'url'));
		$this->load->model('medical_records_model');
		$this->load->model('hcp_model');
		$this->load->model('connections_model');

	}

	/*
	 * Default function call
	 * @return  redirects to list_my_med_recs function
	 */ 
	function index()
	{
		//$this->auth->check_logged_in();
		//$this->ui->set_redirect('/medical_records/list_med_recs');
		$this->myrecs();
	}

	/*
	 * Lists all medical records of the current patient
	 * 
	 * @return
	 *		if patient-list your records
	 * 		if hcp, redirect to list of his/her patients
	 * 
	 * @attention Only a patient can view this list
	 * 
	 * @bug The medical records model has a problem giving back
	 * information of records created by patients
	 * 
	 * @test Tested
	*/
	function myrecs()
	{
		

	
		if (DEBUG) $this->output->enable_profiler(TRUE);
//		$this->auth->check_logged_in();
		if ($this->auth->check(array(auth::CurrLOG)) !== TRUE) {
			return;
		}

		// if patient, show all their med recs
		if ($this->auth->get_type() === 'patient') {
			$res = $this->medical_records_model->list_my_records(
				array($this->auth->get_account_id())
			); 
		}
		// if hcp, redirect to My Patients search List
		else if ( $this->auth->get_type() === 'hcp') {
			$this->ui->set_redirect('/connections/mypatients');
			return;
		} else {
			$this->ui->set_error('Server Error.', 'server');
			return;
		}
		if ($res === -1) {
			$this->ui->query_error();
			return;
		}
		
		// All ok
		$this->ui->set(array(
			$this->load->view('mainpane/lists/medical_records',
				array('list_name' => 'My Medical Records', 'list' => $res) , TRUE)
		));
	}
	
	/**
	 * Shows all medical records that a certain patient is sharing with
	 * me.
	 * 
	 * @attention Called only by hpcs
	 * @attention Must access table Permissions to list ONLY records that
	 * have been shared with the current hcp
	 * 
	 * @tested
	 * */
	function patient($pid = NULL) {
		if (DEBUG) $this->output->enable_profiler(TRUE);
//		$this->auth->check_logged_in();
		if ($this->auth->check(array(auth::CurrLOG,auth::CurrCONN,$pid)) !== TRUE) {
			return;
		}

		/*
		if ($patient_id == NULL) {
			$this->ui->set_error('No patient id specified','Missing Arguments');
			return;
		}
		*/
		/** Patients can now see other patient's medical records, if they are connected with high level of trust
		// Current user must be an hcp
		if ($this->auth->get_type() != 'hcp') {
			$this->ui->set_error('Only HCPs have access to this funtionality.<br />
			To see your medical records go <a href="/medical_records">here</a>', 'Permission Denied');
			return;
		}
		* */
		/*		
		// Current user must be connected with the patient
		$check = $this->connections_model->is_connected_with(
			$this->auth->get_account_id(), $patient_id
		);
		if ($check === -1){ 
			$this->ui->set_query_error(); 
			return;
		}
		else if ($check == FALSE) {
			$this->ui->set_error('You are not connected with this patient','Permission Denied');
		} else {
		*/
		// Get the list of medical records from the model
		$recs = $this->medical_records_model->get_patient_records(
			array($pid, $this->auth->get_account_id()
			));
		if ($recs === -1){
			$this->ui->set_query_error(); 
			return;
		} else {
			// View the list
			$this->ui->set(array(
				$this->load->view('mainpane/lists/medical_records',
					array('list_name' => 'Medical Records', 'list' => $recs) , TRUE)
			));
		}
		//}
	}
	
	/**
	 * Loads view that allows you to upload a medical record
	 * 
	 * @test Tested
	 * */
	function upload($pid = NULL) {
		if ($this->auth->check(array(
			auth::CurrLOG
		)) !== TRUE) return;
	
		// $pid may be not specified in case a patient is executing
		if ($this->auth->get_type() === 'patient') {
			$pid = $this->auth->get_account_id();
		}
		else if ($this->auth->check(array(
			auth::PAT, $pid,			// $pid must refer to a patient
			auth::CurrIS_or_CONN, $pid	// current must be either the patient $pid or connected with $pid
		)) !== TRUE) return;
		
		$this->ui->set(array(
			$this->load->view('mainpane/forms/upload_medrec',
				array('patient_id' => $pid) , TRUE)
		));
	}
	
	/*
	 * Add the new medical record
	 * @attention should the patient approve this first before having it added to their list of records?
	 *
	function upload_do()
	{
		$this->auth->check_logged_in();
		$this->load->model('medical_records_model');

		$file = $this->input->post('filepath');
		
		// Patient adding their own file
		if ($this->auth->get_type() === 'patient') {
			$results = $this->medical_record_model->add_med_record(array(
																			'patient_id' => $this->auth->get_account_id(), 
																			'account_id' => $this->auth->get_account_id(), 
																			'file_path' => $file,
																	)); 
		}

		// hcp adds medical record of a specific patient 
		else if ($this->auth->get_type() === 'hcp') {
			$patient_id = $this->input->post('patient_id');
			$results = $this->medical_record_model->add_med_record(array(
																			'patient_id' => $this->auth->get_account_id(),
																			'account_id' => $this->auth->get_account_id(), 
																			'file_path' => $file,
																	)); 
		}
		else {		
			$this->ui->error('Internal Logic Error.', 500);
			return;
		}
		
		switch ($results) {
			case -1:
				$this->ui->set(array('Query error!',''));
				break;
			default:
				$this->ui->set_redirect('/medical_records/list_med_recs');
				break; 
		}	
	}*/

	/**
	 * Gets called when an individual medical record is selected to be viewed
	 * loads a view that prints Name, Expanded Info, Date, ...etc
	 * should list all hcps who have permission to see it
	 * should have a button that lets you give another hcp permission , or remove permission 
	 * should have a button to delete a medical record
	 * @todo Implement this function and then make it 'public' (remove _ at
	 * the beginning of the name)
	 * @todo: need a view that lists medical records: Name, Description, link to file
	 * *
	function _view($med_rec_id) {
		$this->auth->check_logged_in();
		$this->load->model('medical_records_model');
		
		// Check if the medical record belongs to user
		if($this->auth->get_type() === 'patient'){
			$result = $this->medical_records_model->is_myrecord(array($this->auth->get_account_id(), $med_rec_id));
		}
		// If hcp: check if he/she has permission to see it
		else if($this->auth->get_type() === 'hcp'){
			$result = $this->permissions_model->____(array($this->auth->get_account_id(), $med_rec_id)); /*@todo: update fn call*
		}
		else {
				$this->ui->error('Internal Logic Error.',500);
				return;
		}
		
		switch ($result) {
			case -1:
				$mainview = 'Query error!';
				$sideview = '';
				$error = TRUE;
				break;
			case FALSE:
				$mainview = 'You do not have permission to see this medical record.';
				$sideview = '';
				$error = TRUE;
			default:
				$error = FALSE;
				break;
		}
		
		if($error){
			$this->ui->set(array($mainview,$sideview));
			return;
		}
				
		$res = $this->medical_records_model->get_medicalrecord(array($med_rec_id));
		
		$this->ui->set(array($this->load->view('mainpane/______', $res, TRUE),''));	
	}*/
	
	/**
	 * Shows a view with all the medical records that you are sharing
	 * and not sharing with a specific account $aid (connected with you)
	 * */
	function change_permissions($aid = NULL) {
		if ($this->auth->check(array(
			auth::CurrLOG,
			auth::CurrPAT,
			auth::CurrCONN, $aid
		)) !== TRUE) return;
		
		// First, get all the records that $aid can see
		$allowedrecs = $this->medical_records_model->get_patient_records(array($this->auth->get_account_id(), $aid));
		
		// Then, get all the records of the current user (must be a patient)
		$allrecs = $this->medical_records_model->list_my_records(array($this->auth->get_account_id()));
		//$hcpsrecs = $this->medical_records_model->list_my_records(array($this->auth->get_account_id()));
		// All ok
		$this->ui->set(array(
			$this->load->view('mainpane/forms/medical_records_permissions',
				array(
				'list_name' => 'Select medical records to share',
				'list' => $allrecs,
				'list2'=>$allowedrecs,
				'aid'=>$aid), TRUE)
		));
	}
			//	echo $allrecs[$i]['medical_rec_id'];
			//check if that medical record was marked
				//if it is marked, check if hcp is already allowed
					//if already allowed, do nothing
					//if not allowed, allow access
				//if it is not marked, check if hcp is already allowed
					//if allowed, disallow permission
					//if not allowed, do nothing
	function do_change_permissions($aid = NULL) {
		if ($this->auth->check(array(
			auth::CurrLOG,
			auth::CurrPAT,
			auth::CurrCONN, $aid
		)) !== TRUE) return;
		
		//get hcp_id from view
		//$hcp_id = $this->input->post('hcp_id');
		
		//get array of medical_rec_ids that were selected, empty array if none are selected
		if (isset($_POST["box"]) && is_array($_POST["box"]) && count($_POST["box"]) > 0) {
			$box = $_POST['box'];
		}
		else{
			$box = array();
		}
		
		//get a list of all my medical records
		$allrecs = $this->medical_records_model->list_my_records(array($this->auth->get_account_id()));
		//error checking needed		
		echo count($box);
		echo '  ';
		echo count($allrecs);
		
		for($i = 0; $i < count($allrecs); $i++ ){
			$temp = $allrecs[$i]['medical_rec_id'] ;
			$isit = FALSE;

		
			//check if that medical record was marked
			for( $j = 0; $j < count($box); $j++ )
				if( $box[$j] == $temp )
					$isit = TRUE;
			//if marked
			if( $isit === TRUE ){
				//check if allowed
				$res = $this->medical_records_model->is_account_allowed(array($temp,$aid));
				if( $res === -1 ){
					$this->ui->set(array('Query error in is_allowed !',''));
					return;								
				}
				if( $res === FALSE ){
					$result = $this->medical_records_model->allow_permission(array($temp,$aid));
					if( $result === -1 ){
						$this->ui->set(array('Query error! in allow_permission',''));
						return;
					}
				}
				/*
				switch($res){
					case -1:
						echo 'error';
						$this->ui->set(array('Query error in is_allowed !',''));
						return;
					//if not allowed
					case FALSE:
						//add permission
						$result = allow_permission(array($temp,$hcp_id));
						switch($result){
							case -1:
								$this->ui->set(array('Query error! in allow_permission',''));
								return;
							case -2:
								$this->ui->set_error('Server Error', 'server');
								return;
							default:
								break;								
						}	
					default:
						break;
				}	*/			
			}
			//if not marked
			else if( $isit === FALSE ){
				//check if allowed
				$res = $this->medical_records_model->is_account_allowed(array($temp,$aid));
				if( $res === -1 ){
					$this->ui->set(array('Query error! in is _allowed',''));
					return;
				}
				if( $res === TRUE ){
					$result = $this->medical_records_model->delete_permission(array($temp,$aid));
					if( $result === -1 ){
						$this->ui->set(array('Query error! in delete',''));
						return;
					}
					
				}
				/*
				switch($res){
					case -1:
						echo 'error';
						$this->ui->set(array('Query error! in is _allowed',''));
						return;
					//if allowed
					case TRUE:
						//delete permission
						$result = delete_permission(array($temp,$hcp_id));		
						switch($result){
							case -1:
								$this->ui->set(array('Query error! in delete',''));
								return;
							default:
								break;
						}
					default:
						break;
				}*/	
			}		
		}
		
		$this->ui->set_message('Successfully changed Permissions.', 'Confirmation'); 
	}
	
	/**
	 * Show a form to ask for an hcp to remove permission to
	 * 
	 * @test Tested
	 * *
	function remove_permission($mrec_id) {
		//$this->auth->check_logged_in();
		if ($this->auth->check(array(auth::CurrLOG)) !== TRUE) {
			return;
		}
		$this->ui->set(array(
			$this->load->view('mainpane/forms/remove_permission', array('medrec_id' => $mrec_id), TRUE)
		));
	}*/
	
	/**
	 * Displays a list of all HCPs that have access to a particular
	 * medical record
	 * 
	 * @attention only accessible to patients
	 * @todo type check $mid
	 * */
	function see_permissions($mid = NULL) {
		if ($this->auth->check(array(
			auth::CurrLOG,
			auth::CurrMED_OWN, $mid
		)) !== TRUE) return;
		
		// Try to list permissions
		$res1 = $this->medical_records_model->get_medrec_allowed_hcps(array($mid));
		$res2 = $this->medical_records_model->get_medrec_allowed_patients(array($mid));
		//$this->load->model('hcp_model');
		//$res = $this->hcp_model->get_hcps(array($mid));
		
		if ($res1 === -1 || $res2 === -1) {
			$this->ui->set_query_error(); 
			return;
		}
		
		$mainpane = $this->load->view('mainpane/lists/medrec_allowed_hcps',
			array('list_name' => 'Allowed HCPs '.$mid, 'list' => $res1), TRUE);
		
		$mainpane .= $this->load->view('mainpane/lists/medrec_allowed_patients',
			array('list_name' => 'Allowed patients '.$mid, 'list' => $res2), TRUE);
		
		$this->ui->set(array($mainpane));
	}
	
	/**
	 * Set medical record to hidden: not visible to your specified hcp
	 * 
	 * @test Tested
	 * */
	function remove_permission($mid = NULL, $aid = NULL) {
		if ($this->auth->check(array(
			auth::CurrLOG,
			auth::CurrMED_OWN, $mid,
			auth::ACCOUNT, $aid
		)) !== TRUE) return;
		
		/* the account that will lose permission
		$account_id = $this->input->post('account_id');
		if ($account_id == '' || $account_id == FALSE) {
			$this->ui->set_error('No account id specified');
			return;
		}*/
		
		/* Get tuple for this medical record
		$get = $this->medical_records_model->get_medicalrecord(array($mid));
		if ($get === -1) {
			$this->ui->set_query_error(); 
			return;
		} else if (sizeof($get) == 0) {
			$this->ui->set_error('Specified medical record does not exist');
			return;
		} else if ($get[0]['patient_id'] != $this->auth->get_account_id()) {
			$this->ui->set_error('Only the owner can modify this medical record','Permission Denied');
			return;
		}*/
		
		// Try to change permission
		// Check if its already allowed to be seen by hcp
		$isalready = $this->medical_records_model->is_account_allowed(array($mid, $aid));
		if ($isalready === -1) {
			$this->ui->set_query_error(); 
			return;
		}
		if (! $isalready) {
			$this->ui->set_message('This record is already forbidden to this hcp.','Notice');
			return;
		}
		$res = $this->medical_records_model->delete_permission(array($mid, $aid));
		
		if($res === -1) {
			$this->ui->set_query_error(); 
			return;
		}
		
		$this->ui->set_message('This record is now forbidden to that HCP', 'Confirmation');
	}
	
	/**
	 * Show a form to ask for an hcp to add permission to
	 * 
	 * @attention Accessible only to patients
	 * 
	 * @test Tested
	 * */
	function add_permission($mid = NULL) {
		//$this->auth->check_logged_in();
		if ($this->auth->check(array(
			auth::CurrLOG,
			auth::CurrMED_OWN, $mid
		)) !== TRUE) return;
		
		//$this->ui->set(array(
		//	$this->load->view('mainpane/forms/add_permission', array('medrec_id' => $mrec_id), TRUE)
		//));
		
		
		$results = $this->connections_model->list_hcps_connected_with($this->auth->get_account_id()); 
		if ($results === -1) {
			$this->ui->set_query_error();
			return;
		}
		$main = $this->load->view('mainpane/forms/pick_hcp',
			array(
				'list_name' => 'My HCPs',
				'list' => $results,
				'form_action' => '/medical_records/add_permission_do/'.$mid
			), TRUE
		);
		
		$results = $this->connections_model->list_patients_connected_with($this->auth->get_account_id()); 
		if ($results === -1) {
			$this->ui->set_query_error();
			return;
		}
		$main .= $this->load->view('mainpane/forms/pick_patient',
			array(
				'list_name' => 'My Patient Friends',
				'list' => $results,
				'form_action' => '/medical_records/add_permission_do/'.$mid
			), TRUE
		);
		
		
		$this->ui->set(array($main));
	}
	
	/**
	 * Add permission to see the specified medical record to the 
	 * specified hcp.
	 * 
	 * @attention Cannot add a permission to another user you are
	 * not connected with
	 * 
	 * @test Tested
	 * */
	function add_permission_do($mid = NULL) {
		$this->auth->check(array(
			auth::CurrLOG,
			auth::CurrMED_OWN, $mid
		));
		
		// Get from POST the account that will have permission
		$hid = $this->input->post('hcp_id');
		$pid = $this->input->post('patient_id');
		if ($hid !== FALSE) {
			$account_id = $hid;
		}
		else if ($pid !== FALSE) {
			$account_id = $pid;
		}
		else {
			$this->ui->set_error('No account id specified','Missing Arguments');
			return;
		}
		
		// You must be connected with that account
		if ($this->auth->check(array(auth::CurrCONN, $account_id)) !== TRUE) return;
		
		// Check if its already allowed to be seen by hcp
		$isalready = $this->medical_records_model->is_account_allowed(array($mid, $account_id));
		if ($isalready === -1) {
			$this->ui->set_query_error(); 
			return;
		}
		if ($isalready === TRUE) {
			$this->ui->set_message('This record is already allowed to this hcp.','Notice');
			return;
		}
		
		// Try to change permission
		$res = $this->medical_records_model->allow_permission(array($mid, $account_id));
		switch ($res) {
			case -1:
				$this->ui->set_query_error(); 
				return;
			case -2:
				$this->ui->set_error('The account specified does not exist or you
				are trying to give an authorize account access');
				return;
			default:
				$this->ui->set_message('This record is now public to that account', 'Confirmation');
				return;
		}
	}

	/**
	 * Delete medical record 
	 * 
	 * @attention ONLY patient should be able to do this
	 * 
	 * @test Tested
	 * */
	function delete($mid = FALSE) {
		$this->auth->check(array(
			auth::CurrLOG,
			auth::CurrMED_OWN, $mid
		));
		
		// Get medical record tuple to retrieve filename
		$get = $this->medical_records_model->get_medicalrecord($mid);
		if ($get === -1) {
			$this->ui->set_query_error(); 
			return;
		} else if (sizeof($get) == 0) {
			$this->ui->set_error('Medical record does not exist');
			return;
		}
		$filename = $get[0]['file_name'];
		
		// Delete record
		$del = $this->medical_records_model->delete_medical_record(array($mid));
		if ($del === -1) {
			$this->ui->set_query_error(); 
			return;
		} else {
			// Delete file from file system
			$filepath = 'resources/medical_records/'.$this->auth->get_account_id().'/'.$filename;
			if (! is_file($filepath)) {
				$error = 'Medical record entry deleted, but file does not exist!';
				$type = 'Notice';
			}
			else if (! unlink($filepath)) {
				$error = 'Medical record entry deleted, but file still in the server!';
				$type = 'Notice';
			} else {
				$this->ui->set_message('Medical record deleted successfully!','Confirmation');
				return;
			}
		}
		
		$this->ui->set_error($error, $type);
	}
}
/** @} */
?>

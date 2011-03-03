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
	 * Loads view that allows you to upload a medical record to patient $pid
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
			$this->load->view('mainpane/forms/account_permissions',
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
		//echo count($box);
		//echo '  ';
		//echo count($allrecs);
		
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
		
		$this->db->trans_start();
		
		// Try to change permission
		$res = $this->medical_records_model->delete_permission(array($mid, $aid));
		
		if($res === -1) {
			$this->ui->set_query_error(); 
			return;
		}
		if ($res === -2) {
			$this->ui->set_message('This record is already forbidden to this hcp.','Notice');
			return;
		}
		
		$this->db->trans_complete();
		$this->ui->set_message('This record is now forbidden to that HCP', 'Confirmation');
	}
	
	/**
	 * Show a form to ask for an hcp to add permission to
	 * 
	 * @attention Accessible only to patients
	 * 
	 * @test Tested
	 * */
	function permissions($mid = NULL) {
		//$this->auth->check_logged_in();
		if ($this->auth->check(array(
			auth::CurrLOG,
			auth::CurrMED_OWN, $mid
		)) !== TRUE) return;
		
		//$this->ui->set(array(
		//	$this->load->view('mainpane/forms/add_permission', array('medrec_id' => $mrec_id), TRUE)
		//));
		
		// Take all permissions for $mid
		$perm1 = $this->medical_records_model->get_medrec_allowed_hcps(array($mid));
		$perm2 = $this->medical_records_model->get_medrec_allowed_patients(array($mid));
		
		// Get complete list of hcps and patients
		$res1 = $this->connections_model->list_hcps_connected_with($this->auth->get_account_id()); 
		$res2 = $this->connections_model->list_patients_connected_with($this->auth->get_account_id()); 
		
		if ($perm1 === -1 || $perm2 === -1 || $res1 === -1 || $res2 === -1) {
			$this->ui->set_query_error();
			return;
		}
		
		$main = $this->load->view('mainpane/forms/medical_record_hcps_perms',
			array(
				'list_name' => 'My HCPs',
				'list' => $res1,
				'list2' => $perm1,
				'form_action' => '/medical_records/permissions_do/'.$mid
			), TRUE
		);
		
		$main .= $this->load->view('mainpane/forms/medical_record_patients_perms',
			array(
				'list_name' => 'My Patient Friends',
				'list' => $res2,
				'list2' => $perm2,
				'form_action' => '/medical_records/permissions_do/'.$mid
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
	function permissions_do($mid = NULL) {
		$this->auth->check(array(
			auth::CurrLOG,
			auth::CurrMED_OWN, $mid
		));
		
		$this->db->trans_start();
		
		// Get from POST the account that will have permission
		$change = $this->input->post('change');
		$hid = $this->input->post('hcps');
		$pid = $this->input->post('pats');
		
		if ($change === 'hcps') {
			$account_id = $hid; // I'm managing hcps
			$complete = $this->connections_model->list_hcps_connected_with($this->auth->get_account_id()); 
		}
		else if ($change === 'patients') {
			$account_id = $pid; // I'm managing pats
			$complete = $this->connections_model->list_patients_connected_with($this->auth->get_account_id()); 
		}
		else {
			$this->ui->set_error('No account id specified','Missing Arguments');
			return;
		}
		
		if ($complete === -1) {
			$this->ui->set_query_error();
			return;
		}
		
		// Go for each connected account and see if its corresp. checkbox is checked
		foreach ($complete as $a) {
			$aid = $a['account_id'];
			$ison = FALSE;
			// Check if checkbox for $aid is checked
			if (is_array($account_id) && count($account_id) > 0)
			foreach ($account_id as $b) {
				if ($b === $aid) {
					$ison = TRUE;
					break;
				}
			}
			
			if ($ison === TRUE) {
				// Give permission
				$res = $this->medical_records_model->allow_permission(array($mid, $aid));
				if ($res === -1) {
					$this->ui->set_query_error(); 
					return;
				}
				// if account has already perms, skip
				if ($res === -2) continue;
			} else {
				// Remove permission
				$res = $this->medical_records_model->delete_permission(array($mid, $aid));
				if ($res === -1) {
					$this->ui->set_query_error(); 
					return;
				}
				// if account does not have perms already, skip
				if ($res === -2) continue;
			}
		}
		
		$this->db->trans_complete();
		$this->ui->set_message('Permissions have been changed', 'Confirmation');
		$this->permissions($mid);
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

<?php
class Medical_Records extends Model {
	


	function __construct() {
		parent::Model();
		$this->load->database();
	}

	function query($inputs){ //$inputs of the form(record_id, accountid, account_type, creatorid)
	}
	function insert($inputs){ //$inputs of the form(accountid, accounttype, issue, additional_info, file_path)
	}
	function delete($inputs){ //$inputs of the form(med_rec_id)
	}
	function update($inputs){ //$inputs of the form(med_rec_id, issue, additional_info)
	}

	return NULL;
	
	

	
}
?>

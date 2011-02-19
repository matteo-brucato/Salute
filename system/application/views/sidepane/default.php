<?php
if ($this->auth->get_type() === 'patient') {
	require('personal_patient_profile.php');
}
else if ($this->auth->get_type() === 'hcp') {
	require('personal_hcp_profile.php');
}
else {
	require('forms/login.php');
}
?>

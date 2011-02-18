<?php
if ($this->auth->get_type() === 'patient') {
	require('patient-profile.php');
}
else if ($this->auth->get_type() === 'hcp') {
	require('hcp-profile.php');
}
else {
	require('forms/login.php');
}
?>

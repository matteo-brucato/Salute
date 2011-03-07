<?php
function send_email($from, $to, $subj, $msg) {
	$CI = &get_instance();
	$CI->load->library('email');
	$config['mailtype'] = 'html';
	$CI->email->initialize($config);
	$CI->email->from($from);
	$CI->email->to($to);
	$CI->email->subject($subj);
	$CI->email->message($msg);
	$CI->email->send();
}

?>

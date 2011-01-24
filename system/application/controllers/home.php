<?php
class Home extends Controller {
	
	function __construct() {
		parent::Controller();
		$this->load->library('ajax');
	}
	
	function index()
	{
		$this->ajax->view(array('home', 'lorem-ipsum'));
	}
	
	function different() {
		$this->ajax->view(array('try.html', ''));
	}
	
	function comments()
	{
		$this->ajax->view(array('', 'try.html'));
	}
	
	/*function _remap($method)
	{
		echo $method;
	}*/
	
	/*function _output($output) {
		//echo $output."!";
	}*/
}
?>

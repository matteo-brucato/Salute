<?php
class Home extends Controller {

	function index()
	{
		if (IS_AJAX) {
			$array = array (
				'left'	=> $this->load->view('home', '', true),
				'right' => $this->load->view('lorem-ipsum', '', true)
			);
			echo json_encode($array);
		} else {
			$this->load->library('layout');
			$this->load->library('parser');
			
			// The actual layout to use can be set differently, for
			// instance, reading it from a cookie or a global variable
			$this->layout->set('faux-8-2-col');
			
			// View the previously specified layout
			// You must know how many views it needs and pass them
			// in an array
			$this->layout->view(array ('home', 'lorem-ipsum'));
		}
	}
	
	function different() {
		if (IS_AJAX) {
			$array = array (
				'left'	=> $this->load->view('try.html', '', true),
				'right' => $this->load->view('lorem-ipsum', '', true)
			);
			echo json_encode($array);
		} else {
			$this->load->library('layout');
			$this->load->library('parser');
			
			// The actual layout to use can be set differently, for
			// instance, reading it from a cookie or a global variable
			$this->layout->set('faux-8-2-col');
			
			// View the previously specified layout
			// You must know how many views it needs and pass them
			// in an array
			$this->layout->view(array ('home', 'lorem-ipsum'));
		}
	}
	
	function comments()
	{
		echo 'Look at this!';
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

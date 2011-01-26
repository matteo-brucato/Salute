<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * CodeIgniter Ajax Class
 *
 * Allows setting and viewing of a specific set of implemented layouts
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		Matteo Brucato
 */

class Ajax {
	
	private $CI;
	
	function __construct() {
		$this->CI =& get_instance();
		$this->CI->load->library('layout');
		$this->CI->load->library('parser');
		$this->CI->load->helper('url');
		
		// The actual layout to use can be set differently, for
		// instance, reading it from a cookie or a global variable
		$this->CI->layout->set('faux-8-2-col');
	}
	
	function view($views = array()) {
		if (IS_AJAX) {
			// Slow down the server
			//for ($i=0; $i<9999999/4; $i++) {
			//	$j = 0;
			//}
			echo json_encode(array (
				'mainpane'	=> ($views[0] != ''? $views[0] : ''),
				'sidepane'	=> ($views[1] != ''? $views[1] : '')
			));
		} else {
			// View the previously specified layout
			// You must know how many views it needs and pass them
			// in an array
			$this->CI->layout->view($views);
		}
	}
	
	function redirect($url) {
		if (IS_AJAX) {
			echo json_encode(array (
				'redirect'	=> $url
			));
		} else {
			redirect($url, 'location', 301);
		}
	}
	
}

?>

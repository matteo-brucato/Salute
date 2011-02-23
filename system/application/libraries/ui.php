<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * CodeIgniter Ui Class
 *
 * Allows interaction with the User Interface
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		Matteo Brucato
 * 
 * @defgroup lib Libraries
 * @ingroup lib
 */

class Ui {
	
	private $CI;
	private $panels; // array of UI panels
	private $redirect = '';
	private $curr_url;
	
	/**
	 * Status code that the UI returns to the client if this class is
	 * set in debug mode.
	 * */
	private $status_code; 
	
	function __construct() {
		$this->CI =& get_instance();
		$this->CI->load->library('layout');
		$this->CI->load->library('parser');
		$this->CI->load->library('auth');
		$this->CI->load->helper('url');
		
		// The actual layout to use can be set differently, for
		// instance, reading it from a cookie or a global variable
		$this->CI->layout->set('faux-8-2-col');
		
		// Create curr_url string
		if ($this->CI->uri->segment(1) != '') {
			$curr_url = '> <a href="/'.$this->CI->uri->segment(1).'" class="ajax">'.$this->CI->uri->segment(1).'</a>';
			if ($this->CI->uri->segment(2) != '') {
				$curr_url .= ' > <a href="/'.$this->CI->uri->segment(1).'/'.$this->CI->uri->segment(2).'" class="ajax">'.$this->CI->uri->segment(2).'</a>';
				if ($this->CI->uri->segment(3) != '')
					$curr_url .= ' > '.$this->CI->uri->segment(3);
			}
		}
		else $curr_url = '';
		
		// Set the default panels
		if (IS_AJAX) {
			$this->panels[0] = NULL;
			$this->panels[1] = NULL;
			$this->panels[2] = NULL;
			$this->panels[3] = NULL;
			$this->panels[4] = NULL;
			$this->panels[5] = $curr_url;
		} else {
			$this->panels[0] = $this->CI->load->view('mainpane/default', '', TRUE);
			$this->panels[1] = $this->CI->load->view('sidepane/default', '', TRUE);
			$this->panels[2] = $this->CI->load->view('others/navbar', '', TRUE);
			$this->panels[3] = $this->CI->load->view('others/footer', '', TRUE);
			$this->panels[4] = $this->CI->load->view('others/header', '', TRUE);
			$this->panels[5] = $curr_url;
		}
	}
	
	/**
	 * View the UI content [aka, it sends the UI to the browser]
	 * 
	 * @param
	 *   @views
	 *   Is an associative array of strings, where the keys are the
	 *   names of the panels in the UI and the values are the strings
	 *   to put into each panel
	 * @note
	 *   Possible keys: main, side, [ others in the future ]
	 * */
	function __destruct() {
		if (TESTING_ON) die($this->status_code);
		
		if (IS_AJAX) {
			// Slow down the server
			//for ($i=0; $i<9999999/4; $i++) {
			//	$j = 0;
			//}
			echo json_encode(array (
				'redirect'	=> $this->redirect,
				'mainpane'	=> $this->panels[0],
				'sidepane'	=> $this->panels[1],
				'navbar'	=> $this->panels[2],
				'footer'	=> $this->panels[3],
				'header'	=> $this->panels[4],
				'curr_url'	=> $this->panels[5]
			));
		} else {
			// View the previously specified layout
			// You must know how many views it needs and pass them
			// in an array
			if ($this->redirect != '') {
				redirect($this->redirect, 'location', 303);
			} else {
				echo $this->CI->layout->get($this->panels);
			}
		}
	}
	
	/**
	 * @param $panels
	 *   An array of strings (panel content) in this order:
	 *   0) Main panel
	 *   1) Side panel
	 *   2) Navigation bar
	 *   3) Footer
	 *   4) Header
	 * 
	 * @note If one of the items in the array is set to '', it means
	 * that the corresponding default panel will be displayed
	 * 
	 * @note If this function is never called, all panels will be
	 * set to the default ones. 
	 */
	function set($panels = array()) {
		for ($i = 0; $i < count($panels); $i++) {
			if ($panels[$i] === NULL ) continue;
			$this->panels[$i] = $panels[$i];
		}
		$this->status_code = ALL_OK; // no error
	}
	
	/**
	 * Show an error message (in the main panel)
	 * */
	function set_error($error_message, $type = 'Generic error') {
		$this->panels[0] = "<h2 class=\"error_hdr\">Error</h2><p class=\"error_type\"><i>type: </i>$type</p><p class=\"error_body\">$error_message</p>";
		$this->status_code = CTR_ERROR; // controller error
	}
	
	function set_query_error() {
		$this->set_error('Error accessing the database, please contact the administrator', 'Query error');
		$this->status_code = QUERY_ERROR; // query error
	}
	
	/**
	 * Show a message (in the main panel)
	 * */
	function set_message($message, $type = '') {
		$this->panels[0] = "<h2 class=\"message_hdr\">$type</h2><h3 class=\"message_body\">$message</h3>";
		$this->status_code = OK_MESSAGE; // message, no error
	}
	
	/**
	 * Set a redirection response, ignoring any UI settings
	 * */
	function set_redirect($url) {
		//redirect($url, 'location', 303);
		if (! TESTING_ON) $this->redirect = $url;
		$this->status_code = REDIRECTED; // redirect, no error
	}
}
?>

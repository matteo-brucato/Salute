<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * CodeIgniter Layout Class
 *
 * Allows setting and viewing of a specific set of implemented layouts
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		Matteo Brucato
 */

class Layout {
	private $defined_layouts = array (
		0	=>	'faux-8-2-col.xhtml'
	);
	const default_layout = 0;
	
	// The layout set dinamically by the controller, using set()
	private $active_layout = self::default_layout;
	
	/**
	 * Sets the layout to use in the future calls of view()
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	function set($layout_name = '') {
		foreach($this->defined_layouts as $val => $string) {
			if ($string == $layout_name) {
				$this->active_layout = $val;
			}
		}
	}
	
	/**
	 * Takes an array with all the dynamic content, i.e. the content
	 * that can change during the use of the application.
	 * All the 'static' content is already known by the layout and
	 * will be automatically showed by this function.
	 * */
	function view($dyn_views = array()) {
		$CI =& get_instance();
		
		switch ($this->active_layout) {
			case 0:								// faux-8-2-col.xhtml
				// This layout needs 2 dynamic views
				if (count($dyn_views) < 2) {
					$dyn_views = array('static/error','static/error');
				}
				$data = array (
					'header' => $CI->load->view('static/header', '', TRUE),
					'navbar' => $CI->load->view('static/navbar', '', TRUE),
					'footer' => $CI->load->view('static/footer', '', TRUE),
					// The following is the dynamic content of this layout
					'left_column' => $CI->load->view($dyn_views[0], '', TRUE),
					'right_column' => $CI->load->view($dyn_views[1], '', TRUE),
				);
				break;
			default:
				// This is the view for the default layout, if no
				// action for it has been specified before
				$data = array (
					'header' => $CI->load->view('static/header', '', TRUE),
					'navbar' => $CI->load->view('static/navbar', '', TRUE),
					'footer' => $CI->load->view('static/footer', '', TRUE),
					'left_column' => $CI->load->view('static/error', '', TRUE),
					'right_column' => $CI->load->view('static/error', '', TRUE)
				);
				break;
		}
		
		$CI->load->library('parser');
		$CI->parser->parse(
			'layout/'.$this->defined_layouts[$this->active_layout],
			$data);
	}
	
}

?>

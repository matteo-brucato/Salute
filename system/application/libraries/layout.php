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
 * 
 * @defgroup lib Libraries
 * @ingroup lib
 */

class Layout {
	private $defined_layouts = array (
		'faux-8-2-col.xhtml'
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
	 * 
	 * @param An array of views. The first two MUST be the two dynamic
	 * content of the application, i.e. the mainpane and sidepane.
	 * The others can be every other view that you want to append to
	 * the final output.
	 * */
	function get_layout($views = array()) {
		$CI =& get_instance();
		
		// Applicatoin needs 2 dynamic views
		if (count($views) != 2) {
			$views = array('static/app_error','static/app_error');
		}
		
		switch ($this->active_layout) {
			case 0:								// faux-8-2-col.xhtml
				$data = array (
					'header' => $CI->load->view('static/header', '', TRUE),
					'navbar' => $CI->load->view('static/navbar', '', TRUE),
					'footer' => $CI->load->view('static/footer', '', TRUE),
					// The following is the dynamic content of this layout
					'mainpane'	=> ($views[0] != '' ? $views[0] :
						$CI->load->view('mainpane/default', '', TRUE)),
					'sidepane'	=> ($views[1] != '' ? $views[1] : 
						$CI->load->view('sidepane/default', '', TRUE))
				);
				break;
			/*default:
				// This is the view for the default layout, if no
				// action for it has been specified before
				$data = array (
					'header' => $CI->load->view('static/header', '', TRUE),
					'navbar' => $CI->load->view('static/navbar', '', TRUE),
					'footer' => $CI->load->view('static/footer', '', TRUE),
					'left_column' => $CI->load->view('static/error', '', TRUE),
					'right_column' => $CI->load->view('static/error', '', TRUE)
				);
				break;*/
		}
		
		$CI->load->library('parser');
		$CI->parser->parse(
			'layouts/'.$this->defined_layouts[$this->active_layout],
			$data);
		
		/** @todo Append all the others views on the input array */
	}
	
}
?>

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
	 * Takes an array with all the panel contents, i.e. the strings
	 * that must be put into the layout.
	 * 
	 * @param An array of strings in this order:
	 *   0) Main panel
	 *   1) Side panel
	 *   2) Navigation bar
	 *   3) Footer
	 *   4) Header
	 * */
	function get($panels = array()) {
		$CI =& get_instance();
		
		switch ($this->active_layout) {
			case 0:								// faux-8-2-col.xhtml
				$data = array (
					'mainpane'			=> $panels[0],
					'sidepane'			=> $panels[1],
					'navbar' 			=> $panels[2],
					'footer' 			=> $panels[3],
					'header' 			=> $panels[4],
					'curr_location' 	=> $panels[5]
				);
				break;
		}
		
		$CI->load->library('parser');
		return $CI->parser->parse(
			'layouts/'.$this->defined_layouts[$this->active_layout],
			$data, TRUE);
	}
	
}
?>

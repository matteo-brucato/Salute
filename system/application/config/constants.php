<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ', 							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE', 					'ab');
define('FOPEN_READ_WRITE_CREATE', 				'a+b');
define('FOPEN_WRITE_CREATE_STRICT', 			'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH'])
	&& strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

define('SITENAME', "Salute");

define('DEBUG', FALSE);
define('TESTING_ON',FALSE);

// Result constants for application testing when TESTING_ON = true
define('CI_ERROR', 'CI_ERROR'); // code igniter error
define('CI_PHP_ERROR', 'CI_PHP_ERROR'); // php error
define('CI_404_ERROR', 'CI_404_ERROR'); // 404, page not found
define('QUERY_ERROR', 'QUERY_ERROR'); // query error
define('CTR_ERROR', 'CTR_ERROR'); // controller error (any)
define('REDIRECTED', 'REDIRECTED'); // redirect
define('OK_MESSAGE', 'OK_MESSAGE'); // message display
define('ALL_OK', 'ALL_OK'); // all fine





/* End of file constants.php */
/* Location: ./system/application/config/constants.php */

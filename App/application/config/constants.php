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

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

//Application Root
define('POS_APP_ROOT','application/',true);
define('POS_IMG_ROOT','application/images/',true);
define('POS_JS_ROOT','application/javascript/',true);
define('POS_CSS_ROOT','application/style/',true);
define('POS_3PARTY_ROOT','application/third_party/',true);
define('POS_VIEW_ROOT','application/views/',true);

//JS CSS file direct root
define('BS3_MAIN_CSS','application/style/repository/css/bootstrap.min.css',true);
define('BS3_XL_CSS','application/style/repository/css/bootstrap-xl.css',true);
define('BS3_SIDEBOX_CSS','application/style/repository/css/sidebar.css',true);
define('PRINT_CSS','application/style/repository/print/printable.css',true);
define('BS3_FA_CSS','application/style/repository/font-awesome/css/font-awesome.min.css',true);
define('BS3_METISMENU_CSS','application/style/repository/metismenu/metisMenu.css',true);

define('JQUERY_FOR_SB','application/style/repository/js/jquery.js',true);
define('JQUERY_COOKIE','application/style/repository/js/jquery.cookie.js',true);
define('BS_MAIN_JS','application/style/repository/js/bootstrap.min.js',true);
define('MORRIS','application/style/repository/js/plugins/morris/morris.min.js',true);
define('RAPHAEL','application/style/repository/js/plugins/morris/raphael.min.js',true);
define('BS3_SIDEBOX_JS','application/style/repository/js/sidebar.js',true);
define('BS3_METISMENU','application/style/repository/metismenu/metisMenu.min.js',true);


/* End of file constants.php */
/* Location: ./application/config/constants.php */
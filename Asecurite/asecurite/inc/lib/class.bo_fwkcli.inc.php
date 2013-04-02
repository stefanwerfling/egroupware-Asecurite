<?php
/**
 * <b>File class.bo_fwkcli.inc.php</b>
 * Command line library for eGroupware applications
 * @author Stephan Acquatella
 * @since   09/03/2012
 * @version 1.0
 * @copyright France Telecom
 * @package egw-enp-framework
 * @subpackage 
 * @filesource  class.bo_fwkcli.inc.php
 */

class bo_fwkcli {

	/** @var string path of header.inc.php file */ 	
	public static $egwinstallpath="/usr/share/egroupware/";
	//public static $egwinstallpath="../";

	/**
     * Constructor
     * 
     * Static Class, can't be instancied
     */
    private function __construct() {
        
    }
	
	
	/**
 	* callback if the session-check fails, creates session from user/passwd in $GLOBALS['egw_login_data']
 	*
 	* @param array &$account account_info with keys 'login', 'passwd' and optional 'passwd_type'
 	* @return boolean/string true if we allow the access and account is set, a sessionid or false otherwise
 	*/
	public static function user_pass_from_argv(&$account) {
		$account = $GLOBALS['egw_login_data'];
	
		if (!($sessionid = $GLOBALS['egw']->session->create($account))) {
			echo "Wrong user-account or -password !!!\n\n";
			//display_usage();
		}
		return $sessionid;
	}

 
 	/**
 	* Start the eGW session, exits on wrong credintials
 	*
 	* @param string $user
 	* @param string $passwd
 	* @param string $domain
 	*/
	public static function load_egw($user,$passwd,$domain='default') {
	
		$_REQUEST['domain'] = $domain;
		$GLOBALS['egw_login_data'] = array(
			'login'  => $user,
			'passwd' => $passwd,
			'passwd_type' => 'text',
		);

		if (ini_get('session.save_handler') == 'files' && !is_writable(ini_get('session.save_path')) && is_dir('/tmp') && is_writable('/tmp')) {
			ini_set('session.save_path','/tmp');	// regular users may have no rights to apache's session dir
		}

		$GLOBALS['egw_info'] = array(
			'flags' => array(
				'currentapp' => 'adviseadm',
				'noheader' => true,
				'autocreate_session_callback' => array('bo_fwkcli','user_pass_from_argv'),
				'no_exception_handler' => 'cli',
			)
		);

		if (substr($user,0,5) != 'root_') {		
			include(self::$egwinstallpath.'/header.inc.php');
		} else {
			$GLOBALS['egw_info']['flags']['currentapp'] = 'login';		
			include(self::$egwinstallpath.'/header.inc.php');

			if ($user == 'root_'.$GLOBALS['egw_info']['server']['header_admin_user'] &&
				self::check_pw($GLOBALS['egw_info']['server']['header_admin_password'],$passwd) ||
				$user == 'root_'.$GLOBALS['egw_domain'][$_GET['domain']]['config_user'] &&
				self::check_pw($GLOBALS['egw_domain'][$_GET['domain']]['config_passwd'],$passwd))  {
					echo "\nRoot access granted!\n";			
			} else {
					die("Unknown user or password!\n");
			}
		}
	}

	/**
 	* Initialyse EGW 
 	* 
 	* @param string user name
 	* @param string user password
 	* @param string egw domain. default is 'default' domain
 	* @return void
 	*/
	public static function init_ewg($user,$passwd,$domain='default'){
	
		if (!isset($GLOBALS['egw'])) {
			
				self::load_egw($user,$passwd,$domain);
		}
		// get eGW's __autoload() function
		include_once(EGW_API_INC.'/common_functions.inc.php');
		// init egwfwk (for log etc)
		include_once('class.bo_fwkenpcommon.inc.php');
		bo_fwkenpcommon::initfwk();		
	}


	/**
 	* Check password against a md5 hash or cleartext password
 	*
 	* @param string $hash_or_cleartext
 	* @param string $pw
 	* @return boolean
 	*/
	public static function check_pw($hash_or_cleartext,$pw) {
		//echo "_check_pw($hash_or_cleartext,$pw) md5=".md5($pw)."\n";
		if (preg_match('/^[0-9a-f]{32}$/',$hash_or_cleartext)) {
			return $hash_or_cleartext == md5($pw);
		}
		return $hash_or_cleartext == $pw;
	}
	
}
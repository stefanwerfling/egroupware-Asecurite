<?php
/**
 * <b>File class.bo_fwkrights.inc.php</b>
 * Rights library for eGroupware applications
 * @author Stephan Acquatella
 * @since   21/11/2011
 * @version 1.0
 * @copyright France Telecom
 * @package egw-enp-framework
 * @subpackage 
 * @filesource  class.bo_rights.inc.php
 */

class bo_fwkrights {

	
	/** @ var array Array of current user's grants on other users or groups **/
	protected $grants;
	/** @ var int current user id **/
	protected $current_user;
	/** @ var int element owner  **/
	protected $element_owner;
	/** @ var string current application name **/
	private $application_name;
	
    /**
     * Constructor
     * Static Class, can't be instancied
     */
    public function __construct($application_name) {   
    	
    	OPF_Logger::logDebug("Contructor call :", $application_name);
    	$this->application_name=$application_name;
    	// get current right for user.
    	$this->delete_right			= EGW_ACL_DELETE;
    	$this->read_right			= EGW_ACL_READ;
		$this->edit_right			= EGW_ACL_EDIT;
		$this->publish_right		= EGW_ACL_CUSTOM_1;
		
		$this->current_user=$GLOBALS['egw_info']['user']['account_id'];
		
		// not specific right by element id for the moment. So owner is automaticaly the current user.
		$this->element_owner=$this->current_user;
		$this->init();
    }
    
    
    /**
     * <b>init()</b> initialyse rights
     * initialyse rights
     * @return void
     */
    public function init(){
    	
    	$grants_user="";
    	$this->grants = $GLOBALS['egw']->acl->get_grants($this->application_name);
		//OPF_Logger::logDebug("get grants :", $this->grants);
		// full grants for admin on user 0 (anonymous questions on previous phpbrain version)
		if ($GLOBALS['egw']->acl->check('run',1,'admin')) $this->grants[0] = -1;
    	
    	// acl grants puts all rights (-1) on current the user itself.
    	// That has to be modified here since the user doesn't have necessarily publish rights		
		if (($user_groups = $GLOBALS['egw']->accounts->membership($GLOBALS['egw_info']['user']['account_id']))) {
			//OPF_Logger::logDebug("current user groups are :", $user_groups);	
			foreach ($user_groups as $group) {
				if (!empty($this->grants[$group['account_id']])) $grants_user |= $this->grants[$group['account_id']];						
			}
		}
		//OPF_Logger::logDebug("Grants for user are :", $grants_user);			
		$this->grants[$GLOBALS['egw_info']['user']['account_id']] = $grants_user;    	
    }
        
    
	/**
	* <b>checkPermission()</b> Checks for rights
	* 
	* @param	int		$check_rights	bitmask ACL right (use $this->read_right or $this->edit_right)
	* @param	int		$article_owner	if not set, checks rights against current article
	* @return	bool					True if has rights, False if not
	*/
	public function checkPermission($check_rights, $element_owner = 0){
		//OPF_Logger::logDebug("Grant array", $this->grants);				
		if (!$article_owner) $element_owner = $this->element_owner;
		//OPF_Logger::logDebug("element_owner :", $element_owner);
		if ($this->grants[$element_owner]) {
			$rights_on_owner = $this->grants[$element_owner];
		} else {
			//OPF_Logger::logDebug("element_owner not found in grants array return false :", $element_owner);
			return False;
		}
		//OPF_Logger::logDebug("rights_on_owner :", $rights_on_owner);
		//OPF_Logger::logDebug("check_rights :", $check_rights);
		$_return=($rights_on_owner & $check_rights);
		//OPF_Logger::logDebug("element_owner has been found in grants array return :", $_return);
		return $_return;
	}
    
}
<?php
/**
 * <b>File class.bo_enpcommon.inc.php</b>
 * library for ADVISE applications
 * @author N'faly KABA
 * @author Stephan ACQUATELLA
 * @since   21/11/2011
 * @version 1.0
 * @copyright France Telecom
 * @package egroupware
 * @subpackage src/phpgwapi/inc/
 * @filesource  class.bo_fwkenpcommon.inc.php
 */
include_once('OPF/Logger.php');
class bo_fwkenpcommon extends so_sql {

    /**
     * current template
     * @var string
     */
    public $tmpl;

    /** @var boolean contains true if log as been already initialysed * */
    protected static $isloginit = false;
    
    /** @var string . current application name. */
	public static $currentapp;

	/** @var array . user preferences for the current application. */
	public $user_preferences;
	
	/** @var array . Preferences for the current application. */
	public static $preferences; 
	
	/** @var object application rights **/
	protected $apprights;
    
	/**
	 * bo_fwkenpcommon contructor
	 * Enter description here ...
	 * @param string $table table name
	 */
	public function __construct($table='') {
		
    	self::$currentapp=$GLOBALS['egw_info']['flags']['currentapp'];
        parent::__construct(self::$currentapp, $table);       
        
        // get user preferences       
        $_user_preferences=$GLOBALS['egw_info']['user']['preferences'][self::$currentapp];
        // add site preferences setting
        $_site_setting= config::read(self::$currentapp);
        // merge settings
        $this->user_preferences=array_merge($_user_preferences,$_site_setting);

        self::initfwk();   
    }

    /**
     * Initialisze for static usage. Initialize self::$currentapp, self::$preferences, log and loghistory
     * Enter description here ...
     * @return void
     */
    public static function initfwk(){
    	self::$currentapp=$GLOBALS['egw_info']['flags']['currentapp'];
    	self::$preferences=$GLOBALS['egw']->preferences->data[self::$currentapp];
    	// Merge with site config . @TODO Check with user_preferences why two values.
    	self::$preferences=array_merge(self::$preferences,config::read(self::$currentapp));
    	
    	
    	// setup log history. log history historise users operations 
       	$GLOBALS['egw']->historylog	= CreateObject('phpgwapi.historylog',self::$currentapp);        
         //  initialyse OPF Logger
        if (!self::$isloginit) self::initLog();
    }
    
    /**
     * construct objects for user interface
     * @param string page name 
     */
    public function init_template($pagename="") {    	
        $this->tmpl = new etemplate();
        if ($pagename != "")
            $pagename = " - " . $pagename;
        $GLOBALS['egw_info']['flags']['app_header'] = lang(self::$currentapp) . $pagename;
        $this->html = & $GLOBALS['egw']->html;
        if (!@is_object($GLOBALS['egw']->js)) {
            $GLOBALS['egw']->js = & CreateObject('phpgwapi.javascript');
        }
    }


    /**
     * Set up default table
     * @see so_sql::setup_table()
     */
    public function setup_table($app, $table, $colum_prefix='') {
        parent::setup_table($app, $table, $colum_prefix);
        $this->init();
    }
    
    /**
     * 
     * Set up table to use
     * @param string $table table name
     * @param string $app application attached to table name. default is current application
     * @param string $colum_prefix 
     * @throws Exception
     */
    public function fwkSetupTable($table='',$app='',$colum_prefix=''){    	
    	if ($app==='') $app=self::$currentapp;
    	if ($table==='') throw new Exception("No table defined");    	
    	parent::setup_table($app, $table, $colum_prefix);
    	$this->init();
    }

    /**
     * Update  data
     * @param array $data update data
     * @param array $where i.e array('id' => 1)
     * @return ADORecordSet or false, if the query fails
     */
    public function update($data, $where) {
        return $this->db->update($this->table_name, $data, $where, __LINE__, __FILE__);
    }

    /**
     * Moves an element
     *
     * @param int $up_down the direction to move: -1 up, 1 down
     * @param int $id id to read
     * @param array $where where clause in sql query
     * @param array|empty string $extra to complete where clause
     * @return void
     */
    public function move_up_down($up_down, $id, $where, $extra='') {
        $content = array();
        if ($this->read($id)) {
            foreach ($this->data as $db_col => $col) {
                $content[$db_col] = $col;
            }
            $weight = (int) $content['weight'] + $up_down;
            $newWeight = $weight <= 0 ? 1 : $weight;
            if (is_array($extra)) {
                $this->update(array('weight' => $content['weight']), array('weight' => $newWeight) + $extra);
                $content['weight'] = $newWeight;
                $this->update($content, $where + $extra);
            } else {
                $this->update(array('weight' => $content['weight']), array('weight' => $newWeight));
                $content['weight'] = $newWeight;
                $this->update($content, $where);
            }
        }
    }

    /**
     * reset all weight containing in a given table (weight start from 1 to table content length)
     * @param string $table_name, table to use
     * @param array $extra_param array filter for where clause
     * @param string $pk primary key column name
     */
    public function reset_all_weight($table_name, $pk, $extra_param='') {
        $this->setup_table(self::$currentapp, $table_name);
        $f_all = array();
        if ($extra_param == '') {
            $f_all = $this->search('', false, 'weight DESC');
        } else {
            $f_all = $this->search($extra_param, false, 'weight DESC');
        }
        if ($f_all) {
            $i = count($f_all);
            foreach ($f_all as $k => $v) {
                if ($v['weight'] > 0) {
                    if (is_array($extra_param)) {
                        $this->update(array('weight' => $i), array($pk => $v[$pk]) + $extra_param);
                    } else {
                        $this->update(array('weight' => $i), array($pk => $v[$pk]));
                    }
                }
                $i--;
            }
        }
    }

    /**
     * <b>initLog()</b> : Initialyse OPF logger
     * @return void
     */
    public static function initLog() {
                
        $_temppath = '/tmp';
        if (!empty($GLOBALS['egw_info']['server']['temp_dir'])) {
            $_temppath = $GLOBALS['egw_info']['server']['temp_dir'];
        }
        $opfConf['handler'] = self::$preferences['log_handler'];
        $opfConf['Locallog']['fileName'] = $_temppath . '/' . self::$preferences['log_file'];
        $opfConf['maxPriority'] = (int) self::$preferences['log_level'];
        $opfConf['ident'] = self::$preferences['log_id'];
        $opfConf['Syslog']['facility'] = LOG_LOCAL6;
        $opfConf['maxLineLength'] = (int) self::$preferences['log_max_line_length'];
        $opfConf['maxLineLengthCutMessage'] = true; // force message cuting if message size is longeur than 4096.

        // otherwrite OPF values if there are set in env variable. Construction is PHP_ADVISE_appname_opfkeyword . ex: PHP_ADVISE_adv_broadband_handler
        foreach ($opfConf as $opfkey => $opfsetting) {
            $_forcedbyenvvalue = getenv('PHP_ADVISE_'.self::$currentapp.'_' . $opfkey);
            if (!empty($_forcedbyenvvalue))
                $opfConf[$opfkey] = $_forcedbyenvvalue;
        }

        OPF_Logger::init($opfConf, time());
        OPF_Logger::logDebug("Log initialised");
        //OPF_Logger::log("Log initialised avec log", OPF_CRIT);
        self::$isloginit = true;
    }

    /**
     * bo_fwkenpcommon class destuctor
     * Enter description here ...
     */
    public function __destruct() {
    	OPF_Logger::logDebug("Closing logs");
        OPF_Logger::close();
        self::$isloginit = false;
    }
    
     /**
     * find all icons for a current application 
     * @param  string $appname current application name
     * @param string $filter to filter existing images
     * @return type 
     */
    public static function getIcons( $filter="/^(.*)\\.(png|gif|jpe?g)$/i") {
    	$icons = array();
    	if ( !isset(common::$found_files) )
    		$GLOBALS['egw']->common->image( self::$currentapp , "book.png");
    	foreach( common::$found_files as $key => $value  ) {
    		//echo "<br>KEY ".$key ;
    		$images = common::$found_files[$key] ;
    		if ( $key == self::$currentapp ) {
    			foreach( $images as $imageName => $imagePath ) {
    				//echo "<br> NAME => $imageName : PATH => $imagePath ";
    				if (preg_match($filter,$imageName,$matches)) {
    					//$icons[ $imagePath."/". $imageName] = ucfirst($matches[1]);
    					$icons[ $imageName ] = ucfirst($matches[1]);
    				}
    			}    			
    		}
    	}        
    	return $icons;
    }
    /**
     * Return URL of an icon, or base url with trailing slash
     *
     * @param string $icon='' filename
     * @return string url
     */
    public static function iconUrl( $icon='') {
    	return $GLOBALS['egw_info']['server']['webserver_url'].'/'.self::$currentapp.'/templates/default/images/'.$icon; //	return $GLOBALS['egw_info']['server']['webserver_url'].self::ICON_PATH.'/'.$icon;
    }
    
    
    /**
     * Set formated message
     * @param string 
     */
    public static function setMessage($usermessage){
    	$usermessage='<div id=message><span>'.lang($usermessage).'</span></div>';
    	egw_session::appsession('msg', self::$currentapp, $usermessage);
    }
}

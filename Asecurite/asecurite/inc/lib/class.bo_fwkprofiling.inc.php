<?php
/**
 * <b>File class.bo_fwkprofiling.inc.php</b>
 * XhProf profiling class for eGroupware applications
 * @author Stephan Acquatella
 * @since   09/04/2012
 * @version 1.0
 * @copyright France Telecom
 * @package egw-enp-framework
 * @subpackage 
 * @filesource  class.bo_fwkprofiling.inc.php
 */
class bo_fwkprofiling {
	
	private static $profile=false;
	private static $profilerurl="http://localhost/xhprof/";
	
	
	/**
	 * 
	 * Enter description here ...
	 */
	public static function init(){
		if (extension_loaded('xhprof')) {
    		include_once '/usr/share/php5-xhprof/xhprof_lib/utils/xhprof_lib.php';
    		include_once '/usr/share/php5-xhprof/xhprof_lib/utils/xhprof_runs.php';
    		self::$profile=true;
		} else {
			throw new ErrorException("can't initialize profiling or xhprof module not load");
		}
		
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $poptions
	 * @throws ErrorException
	 */
	 public static function start($poptions=array()) {
	 	if (!self::$profile) throw new ErrorException("profiling not initialized");

	 	//$ignore = array('call_user_func', 'call_user_func_array');
		//xhprof_enable(0, array('ignored_functions' =>  $ignore));
		xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $save
	 * @throws ErrorException
	 */
	public static function stop($save=false){
		if (!self::$profile) throw new ErrorException("profiling not initialized");
		$xhprof_data=array();
		$profiler_namespace = $GLOBALS['egw_info']['flags']['currentapp'];  // namespace for your application
        $xhprof_data['data'] = xhprof_disable();
        if ($save) {
        	$xhprof_runs = new XHProfRuns_Default();
        	$run_id = $xhprof_runs->save_run($xhprof_data['data'] , $profiler_namespace);       
         	$profiler_url = sprintf(self::$profilerurl.'xhprof_html/index.php?run=%s&source=%s', $run_id, $profiler_namespace);
    		$profiler_url = sprintf(self::$profilerurl.'xhprof_html/index.php?run=%s&source=%s', $run_id, $profiler_namespace);
         	$xhprof_data['url']= '<a href="'. $profiler_url .'" target="_blank">Profiling result page</a>';    		
        }
        return $xhprof_data;
	}
}
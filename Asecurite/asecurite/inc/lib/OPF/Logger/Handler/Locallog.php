<?php
/**
 * This file contains class definition for Locallog log handler
 * 
 * @author   Stephan Acquatella, Philippe Bouery, Sandro Lex
 * @version $Revision-Id: slex-20111020132927-ccpcd214y95tvua4 $
 * @since   12/12/2007
 * @copyright France Telecom
 * @package OPF
 * @filesource OPF/Logger/Handler/Locallog.php
 */

/**
 * OPF_Logger_Handler_Locallog extends the console log handler
 */
include_once 'OPF/Logger/Handler/Console.php';

/**
 * <b>OPF_Logger_Handler_Locallog.php</b>
 *
 * OPF_Locallog class is a log handler that uses local file
 * It is Based on logger Class developped for 
 * ECMS CMS by Stephan Acquatella (GPL)
 * 
 * @package OPF
 * @subpackage Logger
 */
class OPF_Logger_Handler_Locallog 
extends OPF_Logger_Handler_Console
{
    /**
     * The file that will hold the logs events
     * @var string
     */
    private $_logFile = '';

    
    /**
     * Class constructor
     * @param string $ident The application's identification
     * @param array $conf Additional configuration parameters
     * <code>Ex: $conf = array('fileName' => 'php.log')</code>
     * @return void
     */
    public function __construct(array $conf)
    {
        if (isset($conf['Locallog']['fileName'])) {
            $this->setLogFile($conf['Locallog']['fileName']);
        }
            
        parent::__construct($conf);
    }
    

    /**
     * Opens the handler for usage
     * @return boolean
     */
    public function open()
    {
        if (!$this->_isOpened && !empty($this->_logFile)) {
            $this->_isOpened = true;
            return true;
        }
        return false;
    }
    

    /**
     * Closes the handler
     * @return boolean
     */
    public function close()
    {
        if ($this->_isOpened) {
            $this->_isOpened = false;
            return true;
        }
        return false;
    }

    
    /**
     * Writes an event to the locallog
     * @param string $message The message to log
     * @param integer $level the priority level of the message
     * @return void
     */
    protected function _write($message, $level = OPF_ERR)
    {
        $message = $message . PHP_EOL;
        error_log($message, 3, $this->_logFile);
    }


    /**
     * Formats the log line
     * @param string $message the message to log
     * @param int $level Priority level of the log event
     * @param string $tag the optional tag name
     * @return void  
     */
    protected function _formatMsg(&$message, $level = OPF_ERR, $tag = '')
    {
        parent::_formatMsg($message, $level, $tag);
        
        $message = $this->_createHeader($level) . $message;
    }

    
    /**
     * Formats the Debug message
     * @param string $message the message to log
     * @param mixed $datas a container of datas to be appended to the message
     * (using print_r())
     * @param string $tag a tag to be associated to the message
     * @return void
     */
    protected function _formatDebug(&$message, $datas = '', $tag = '')
    {
        parent::_formatDebug($message, $datas, $tag);
        
        $message = $this->_createHeader(OPF_DEBUG) . $message;
    }
    
    
    /**
     * Format a statics log message
     * @param boolean $result the result of the operation
     * @param string $code The status code of the application
     * @param string $message The message to log
     * @param string $tag the optional tag name
     * @return void
     */
    protected function _formatStats($result, $code, &$message, $tag = '')
    {
        parent::_formatStats($result, $code, $message, $tag);
        
        $message = $this->_createHeader(OPF_NOTICE) . $message;
    }
    
    
    
    /**
     * Formats a message to be used with the special logData method
     * @param mixed $data a container of datas
     * @param string $tag a tag to be associated to the message
     * @return string the formated message
     */
    protected function _formatData($data, $tag)
    {
        $msg = parent::_formatData($data, $tag);
        
        return $this->_createHeader($this->_levelData) . $msg;
    }
    
    /**
     * Configures the file to store the log events
     * @param string $file
     * @return void
     * @throws Exception
     */
    public function setLogFile($file)
    {
        if (!is_writeable(dirname($file))) { 
            throw new Exception(
                'OPF_Logger: Unable to write log file: ' . $file
            );
        }
        $this->_logFile = $file;
        
    }
    
    
    /**
     * Gathers debug information with the debug_backtrace function
     * @param integer $dep the depht level of back trace function
     * @return array
     * keys:<br>
     * <ul>
     * <li>string $class The class name of the caller</li>
     * <li>string $function The execution method/function of the caller</li>
     * <li>int $line The line of the caller</li>
     * <li>string $date</li>
     * </ul> 
     */
    protected function _getBacktraceVars($dep = 4)
    {
        return parent::_getBackTraceVars(6);
    }
}

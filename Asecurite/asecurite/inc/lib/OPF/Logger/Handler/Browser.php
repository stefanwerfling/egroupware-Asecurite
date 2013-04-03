<?php 
/**
 * This file contains the class definition of Browser log handler
 *
 * @author  Leiha Selllier
 * @version $Revision-Id: slex-20111020132927-ccpcd214y95tvua4 $
 * @since   25/08/2011
 * @copyright France Telecom
 * @package OPF
 * @filesource OPF/Logger/Handler/Browser.php
 */

/**
 * OPF_Logger_Handler_Browser extends the handler abstract
 */
require_once 'OPF/Logger/Handler/Abstract.php';

/**
 * Used to encode/decode Json structures
 */
require_once('OPF/Json.php');

/**
 * <b>OPF_Loggger_Handler_Browser</b>
 *
 * OPF_Log_Handler_Browser class defines the common members
 * and operations for the log handlers that will display log into a browser
 *
 * @package OPF
 * @subpackage Logger
 */
abstract class OPF_Logger_Handler_Browser 
extends OPF_Logger_Handler_Abstract
{

    /**
     * Holds the number of log events sent to the handler
     * @var integer
     */
    protected $_incrementer = 0;

    /**
     * True if log messages are already rendered
     * @var boolean
     */
    protected $_alreadyRendered = false;

    /**
     * Holds the list of log events
     * @var array
     */
    protected $_stackLogs = array();
    
    /**
     * A list of classes that should not be considered by backtrace mechanism
     * @var array
     */
    protected $_backTracefilters = array(
        'OPF_Logger_Handler_Abstract',
        'OPF_Logger_Handler_Browser',
        'OPF_Logger_Handler_Notifier',
        'OPF_Logger_Handler_FirePHP'
    );
    
    /**
     * Method for render all messages
     * @return void
     */
    abstract protected function _render();

    
    /**
     * Method for render one message
     * @param array $item Elements for building a complete item
     * @return mixed (either string or void)
     */
    abstract protected function _renderItem($item);

    /**
     * Class destructor
     * Calls the render mechanism to send the log to the browser
     */
    public function __destruct(){
        if ($this->_alreadyRendered) {
            return;
        }

        $this->_render();
    }

    /**
     * Have to implement this method to only respect the abstract interface
     * It does nothing
     * @return boolean
     */
    protected function _write($message, $level = OPF_ERR)
    {
        return;
    }

    /**
     * Have to implement this method to only respect the abstract interface
     * It does nothing
     * @return boolean
     */
    public function open()
    {
         return true;
    }
     
    /**
     * Calls the render mechanism
     * @return boolean
     */
    public function close()
    {
        $this->_render();
    }
    
    /**
     * Puts an item into the top of the stack
     * @param string $stackItem item builded for the output of handler
     */
    protected function _stack($stackItem)
    {
        $this->_stackLogs[] = $stackItem;
    }

    /**
     * Renders the queryId into the log stack
     * @return mixed (string or void)
     */
    protected function _setLogForRequestID()
    {
        return $this->_renderItem(array(
            'OPFLevel'  => OPF_INFO,
            'type' => 'log',
            'msg'   => 'Request ID : '.$this->_queryId
        ));
    }

    /**
     * Getter for litteral log level
     * @param   int $OPFlevel
     * @return  string
     */
    protected function _getLitteralOPFLevel($opflevel)
    {
        switch ($opflevel) {
            case OPF_EMERG: 
                $ret = 'Emergency';
                break;
            case OPF_ALERT: 
                $ret = 'Alert';
                break;
            case OPF_CRIT: 
                $ret = 'Critical error';
                break;
            case OPF_ERR: 
                $ret = 'Error';
                break;
            case OPF_WARNING: 
                $ret = 'Warning';
                break;
            case OPF_NOTICE: 
                $ret = 'Notice';
                break;
            case OPF_INFO: 
                $ret = 'Info';
                break;
            case OPF_DEBUG: 
                $ret = 'Debug';
                break;
            default:
                $ret = 'Log';
                break;
        }
        
        return 'OPF ' . $ret;
    }

    /**
     * Getter for OPF log type
     * @param   string $OPFLogType
     * @return  string
     */
    protected function _getLogType($opfLogType)
    {
        switch ($opfLogType) {
            case 'data': 
                $ret = 'logData';
                break;
            case 'debug': 
                $ret = 'logDebug';
                break;
            case 'stat': 
                $ret = 'logStat';
                break;
            case 'log': 
            default:
                $ret = 'log';
                break;
        }
        
        return 'OPFLogger::' . $ret.'()';
    }
    

    
    /**
     * Getter for one backtrace
     * @param   map $backTrace
     * @return  array
     */
    protected function _getBacktrace($backTrace = null, $dep = 5)
    {
        if (is_null($backTrace)) {
            $backTrace = debug_backtrace();
            $backTrace = $backTrace[$dep];
        }

        $trace = array();
        
        switch (isset($backTrace['class'])) {
            case true: 
                $trace[] = $backTrace['class'] . $backTrace['type']
                . $backTrace['function'];
                break;
            default: 
                $trace[] = $backTrace['function'];
                break;
        }

        switch (isset($backTrace['file'])) {
            case true: 
                $trace[] = $backTrace['file'];
                $trace[] = $backTrace['line'];
                break;
            default: 
                $trace[] = '';
                break;
        }

        return $trace;//array(class.type.function,file,line)
    }

    
    /**
     * Format the PHP error message
     * @param string $message
     * @param int $OPFLevel
     * @param int $PHPLevel
     * @param array $context
     * @return void
     */
    protected function _formatPHPError(&$message, $opfLevel, $phpLevel, 
        $context)
    {
        $item = array(
            'OPFLevel'  => $opfLevel,
            'PHPLevel'  => $phpLevel,
            'type' => 'PHPError',
            'msg'   => $message,
            'data' => $context,
            'tag'   => 'PHP'
        );
        
        $this->_stack($this->_renderItem($item));
    }

    /**
     * Format the log message.
     * 
     * Should be implemented by a real log handler as well
     * @param &string $message The message to log
     * @param int $level Priority level of the log event
     * @param string $tag the optional tag name
     * @return void
     */
    protected function _formatMsg(&$message, $level = OPF_ERR, $tag = '')
    {
        $item = array(
            'OPFLevel'  => $level,
            'type' => 'log',
            'msg' => $message,
            'tag' => $tag
        );
        
        $this->_stack($this->_renderItem($item));
    }
        
    /**
     * Format the log message.
     * 
     * Should be implemented by a real log handler as well
     * @param &string $message The message to log
     * @param string $tag the optional tag name
     * @param mixed $datas Additional content of the log event
     * @return void
     */
    protected function _formatDebug(&$message, $datas = '', $tag = '')
    {
        $item = array(
            'OPFLevel' => OPF_DEBUG,
            'type' => 'debug',
            'msg'   => $message,
            'tag'   => $tag,
            'data'  => $datas
        );
        
        $this->_stack($this->_renderItem($item));
    }

    /**
     * Formats the message to the logData method
     * @param string $jsonStr json format
     * @param string $tag a tag to be associated to the message
     * @return void
     */
    protected function _createDataMessage($jsonStr, $tag)
    {
        OPF_Json::decode($jsonStr, $jsonDecoded, true);
    
        $item = array(
            'OPFLevel'  => $this->_levelData,
            'type' => 'data',
            'tag' => $tag,
            'data'  => $jsonDecoded
        );
        
        $this->_stack($this->_renderItem($item));
    }

    /**
     * Formats the message log for a logStat event
     * @param boolean $result the result of the operation
     * @param string $code The status code of the application
     * @param string $message The message to log
     * @param string $tag the optional tag name
     * @return void
     */
    protected function _createStatMessage($code, &$message, $tag = '')
    {
        $msgLine = $this->_resultStatus . ' | ' . $this->_executionTime 
            . ' | ' . $code . ' | ' . $message;
        
        $item = array(
            'OPFLevel' => OPF_NOTICE,
            'type' => 'stat',
            'msg' => $msgLine,
            'tag' => $tag
        );
        
        $this->_stack($this->_renderItem($item));
    }
}

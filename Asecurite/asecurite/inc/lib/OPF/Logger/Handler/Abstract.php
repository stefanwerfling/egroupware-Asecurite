<?php
/**
 * This file contains the common definitions for a Logger Handler
 * 
 * @author  Sandro Lex
 * @version $Revision-Id: slex-20120214130126-b1yrolzfd1b6kjid $
 * @since   07/04/2010
 * @copyright France Telecom
 * @package OPF
 * @filesource OPF/Logger/Handler/Abstract.php
 */

/**
 * Needed for the constants
 */
require_once 'OPF/Logger.php';

/**
 * <b>OPF_Logger_Handler_Abstract</b>
 *
 * OPF_Log_Handler_Abstract class defines the common members
 * and operations for all OPF Log Handlers.
 *
 * @package OPF
 * @subpackage Logger
 *
 */
abstract class OPF_Logger_Handler_Abstract
{
    /**
     * True when the log handler is ready for use
     * @var boolean
     */
    protected $_isOpened = false;

    /**
     * The application's identification
     * @var string
     */
    protected $_ident = 'TEST';

    /**
     * The log handler will only log messages
     * with level <= $_maxPriority.
     *
     * @var int
     */
    protected $_maxPriority = OPF_NOTICE;

    /**
     * An unique identifier for the application's request
     * @var int
     */
    protected $_queryId;

    /**
     * The result of the operation.
     *
     * OK: Success
     * KO: Failure
     * @var string
     */
    protected $_resultStatus;

    /**
     *  The last log event that was sent to a handler
     *  @var string
     */
    protected $_lastMessage = '';

    /**
     * Default value used for the tag in the logData() method
     * (it can be overloaded with the tagData conf parameter)
     * @var string $_tagdata
     */
    protected $_tagData = 'DATA';

    /**
     * Default value used for LOG_LEVEL in the logData method
     * (it can be overloaded with the levelData conf parameter)
     * refer to OPF/Logger/Constants.php for possible values
     * @var integer $_levelData
     */
    protected $_levelData = OPF_INFO;

    /**
     * The maximum line length of a log message
     * If it is set to 0, line length is ignored
     * @var integer $_maxLineLength
     */
    protected $_maxLineLength = 0;

    /**
     * Tag used in the max line length log messages
     * @var string
     */
    protected $_maxLineLengthTag = 'MAX_LENGTH';

    /**
     * Log level used in the line length log messages
     * @var integer
     */
    protected $_maxLineLengthLevel = OPF_WARNING;
     
    /**
     * If true, it will cut messages to fit maxLineLenght.
     * If false, it will just send an alert message
     * @var boolean
     */
    protected $_maxLineLengthCutMessage = true;


    /**
     * Holds the name of the application server
     *
     * It is going to be outputed in each log line!
     * @var string
     */
    protected $_serverName = '';

    /**
     * Signature for opening a log handler.
     *
     * Should be implemented by a real handler
     * @return boolean
     */
    abstract public function open();

    /**
     * Signature for closing a log handler.
     *
     * Should be implemented by a real handler
     * @return boolean
     */
    abstract public function close();

    /**
     * Signature for writing message to the real log handlder
     * @param mixed $message It it usually a string but for ApacheNotes it
     * has to be a array.
     * @param integer $level The priority level of the message
     */
    abstract protected function _write($message, $level = OPF_ERR);
     
    /**
     * Signature for Format log message method.
     *
     * Should be implemented by a real log handler
     * @param &string $message The message to log
     * @param int $level Priority level of the log event
     * @param string $tag the optional tag name
     * @return void
     */
    abstract protected function _formatMsg(&$message, $level = OPF_ERR,
    $tag = '');

    /**
     * Signature for the format debug log message method
     *
     * Should be implemented by a real log handler
     * @param &string $message The message to log
     * @param string $tag the optional tag name
     * @param mixed $datas Additional content of the log event
     * @return void
     */
    abstract protected function _formatDebug(&$message, $datas = '', $tag = '');

    /**
     *
     * Signature for the stat message method
     * @param integer $code the result of the operation
     * @param string $message
     * @param string $tag
     * @return void
     */
    abstract protected function _createStatMessage($code, &$message, $tag = '');

    /**
     * Signature for the data message method
     * @param string $jsonStr
     * @param string $tag
     * @return string
     */
    abstract protected function _createDataMessage($jsonStr, $tag);

    /**
     * Signature for the PHP error message method
     * @param string $message
     * @param int $OPFLevel
     * @param int $PHPLevel
     * @param array $context
     * @return void
     */
    abstract protected function _formatPHPError(&$message, $opfLevel, $phpLevel,
        $context);


    /**
     * Class constructor
     * @param array $conf the handler's specific configuration parameters
     * @return void
     */
    public function __construct(array $conf)
    {
        // have to shut up warnings.
        // shall we put it somewhere in the conf file?
        date_default_timezone_set('Europe/Paris');


        // OPF_Time is used to calculate the execution time
        if (!class_exists('OPF_Time')) {
            include 'OPF/Time.php';
        }

        OPF_Time::setInitialTime('OPF_Logger');

        if (!empty($conf['ident'])) {
            $this->_ident = $conf['ident'];
        }

        // if an queryId is not passed, generate one with uniqid()
        if (isset($conf['queryId'])) {
            $this->_queryId = $conf['queryId'];
        } else {
            $this->_queryId = uniqid();
        }

        $this->_checkLogDataConf($conf);

        $this->_checkLineLengthConf($conf);

        if (!isset($_SERVER['SERVER_NAME'])) {
            $this->_serverName = (string)'Not Defined';
        } else {
            $this->_serverName = $_SERVER['SERVER_NAME'];
        }
    }


    /**
     * writes a regular log message
     *
     * It will check the priority and format the message before send it to
     * the log handler.
     * @param string $message The message to log
     * @param integer $level the priority level of the message
     * @param string $tag a tag to be associated with the log message
     * @return void
     */
    public function log($message, $level = OPF_ERR, $tag = '')
    {
        if ($level == OPF_NOTICE || !$this->_checkPriority($level)) {
            return;
        }

        $this->_checkMessageLength($message);

        $this->_formatMsg($message, $level, $tag);

        $this->_lastMessage = $message;

        $this->_write($message, $level);
    }


    /**
     * writes a PHP error log message
     *
     * @param string $message The message to log
     * @param int $OPFLevel The OPF Level
     * @param int $PHPLevel The PHP Level error
     * @param string $errcontext The context of PHP error
     * @return void
     */
    public function logPHPError($message, $opfLevel, $phpLevel, array $context)
    {
        $this->_formatPHPError($message, $opfLevel, $phpLevel, $context);

        $this->_checkMessageLength($message);

        $this->_lastMessage = $message;

        $this->_write($message, $opfLevel);
    }


    /**
     * writes a logStat message
     * @param boolean $result the result of the operation
     * @param string $code The status code of the application
     * @param string $message the message to log
     * @param string $tag a tag to be associated to the message
     * @return void
     */
    public function logStat($result, $code, $message, $tag = '')
    {
        $this->_checkMessageLength($message);

        $this->_formatStats($result, $code, $message, $tag);

        $this->_lastMessage = $message;

        $this->_write($message, OPF_NOTICE);
    }


    /**
     * writes a debug message
     * It will format the message (with class name, function a line number)
     * and write a log message with log level = OPF_DEBUG
     * @param string $message The message to log
     * @param mixed $datas optional data container to be included in the
     * debug message
     * @param string $tag a tag to be associated to the message
     * @return void
     */
    public function logDebug($message, $datas = '', $tag = '')
    {
        if (!$this->_checkPriority(OPF_DEBUG)) {
            return;
        }

        $this->_formatDebug($message, $datas, $tag);

        $this->_checkMessageLength($message);

        $this->_lastMessage = $message;

        $this->_write($message, OPF_DEBUG);
    }


    /**
     *
     * writes a special Hebex log message
     * @param mixed $data a container of datas
     * @param string $tag a tag to be associated to the message
     * @return void
     */
    public function logData($data, $tag = '')
    {
        if (!$this->_checkPriority($this->_levelData)) {
            return;
        }

        if (empty($tag)) {
            $tag = $this->_tagData;
        }

        $message = $this->_formatData($data, $tag);

        $this->_checkMessageLength($message);

        $this->_lastMessage = $message;

        $this->_write($message, $this->_levelData);
    }

    /**
     * Getter for backtraces
     * @param	array $backTraces
     * @return	array
     */
    protected function _getBacktraces($backTraces=null)
    {
        if (is_null($backTraces)) {
            $backTraces = debug_backtrace();
        }

        $traces = array();
        foreach ($backTraces as $backTrace) {
            if ($this->_isFilteredBacktrace($backTrace)) {
                continue;
            }
            $traces[] = $this->_getBacktrace($backTrace);
        }

        return $traces;
    }

    /**
     * Backtrace Filter
     * @param	map $backTrace
     * @return boolean
     */
    protected function _isFilteredBacktrace($backTrace)
    {
        foreach ($this->_backTracefilters as $filter) {
            if ((isset ($backTrace['class']) &&
            strpos($backTrace['class'], $filter) !== false)
            || !(isset ($backTrace['file']))
            || strpos($backTrace['file'], $filter) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Checks if the given priority is authorized by the Log Handler
     * @param int $level
     * @return boolean
     */
    protected function _checkPriority($level)
    {
        if ($this->_maxPriority == OPF_NOLOG || $level == OPF_NOLOG) {
            return false;
        }

        if (OPF_Logger::isValidLevel($level)
        && $level <= $this->_maxPriority) {
            return true;
        }

        return false;
    }


    /**
     * Sets the tolerance priority level
     * @param int $level
     * @return void
     */
    public function setMaxPriority($level)
    {
        if (is_int($level)) {
            $this->_maxPriority = $level;
        }
    }


    /**
     * Gets the last log line
     * @return string
     */
    public function getLastMessage()
    {
        return $this->_lastMessage;
    }
     

    /**
     * Check configuration parameters for lofData feature
     *
     * If parameters are ok, it will set the private members values
     * If not, it will use the default values
     * @param array $conf the configuration options
     * @return void
     */
    private function _checkLogDataConf($conf)
    {
        if (isset($conf['tagData'])
        && OPF_Logger::isValidTag($conf['tagData'])) {
            $this->_tagData = $conf['tagData'];
        }

        if (isset($conf['levelData']) &&
        OPF_Logger::isValidLevel($conf['levelData'])) {
            $this->_levelData = $conf['levelData'];
        }
    }

    /**
     * Formats the message log for a logStat event
     * @param boolean $result the result of the operation
     * @param string $code The status code of the application
     * @param string $message The message to log
     * @param string $tag the optional tag name
     * @return void
     */
    protected function _formatStats($result, $code, &$message, $tag)
    {
        // calculates the total execution time
        OPF_Time::setFinalTime('OPF_Logger');
        $this->_executionTime = OPF_Time::getExecutionTimeMs('OPF_Logger');

        if ($result === true) {
            $this->_resultStatus = 'OK';
        } else {
            $this->_resultStatus = 'KO';
        }

        $this->_createStatMessage($code, $message, $tag);
    }

    /**
     * Formats the message to the logData method
     * @param string $data a conatiner of datas
     * @param string $tag a tag to be associated to the message
     * @return string the formated message
     */
    protected function _formatData($data, $tag)
    {
        if (!class_exists('OPF_Json')) {
            include_once 'OPF/Json.php';
        }

        $jsonStr = OPF_Json::encode($data);

        return $this->_createDataMessage($jsonStr, $tag);
    }


    /**
     * Checks the length of a message
     *
     *  If the configuration parameter maxLineLength is set to 0 it will
     *  disable this feature and OPF_Logger will ignore the size of the
     *  messages.
     *  If a size is set, regular behaviour is to check if message is bigger
     *  than the value. If not, write it to log handler.
     *  If it is bigger, it will cut the message to fit the maximum allowed
     *  size, log a special message to alert of the event and than write
     *  the cut message to the log handler.
     *  Other configuration parameter are:
     *  - maxLineLengthTag: set the tag used in the alert log message
     *  - maxLineLengthLevel: set the Level used in the alert log message
     *  - maxLineLengthCutMessage: set if it is going to cut the original
     *  message or not.
     * @param string $message the message to check. It is a reference!
     * @return void
     */
    protected function _checkMessageLength(&$message)
    {
        if (!$this->_maxLineLength) {
            // this feature is disabled
            return;
        }

        $msg = '';

        if (strlen($message) > $this->_maxLineLength) {
            $msg = 'Log message length is greater than '
            . $this->_maxLineLength;

            // if cut message option is enabled, cut it!
            if ($this->_maxLineLengthCutMessage) {
                $msg .= ', going to cut it.';
                $message = substr($message, 0, $this->_maxLineLength);
            }

            // log event
            $this->_formatMsg($msg,
            $this->_maxLineLengthLevel,
            $this->_maxLineLengthTag);

            $this->_write($msg, $this->_maxLineLengthLevel);
        }
    }


    /**
     * Check configuration parameters for maxLineLength feature
     *
     * If parameter are ok, it will set it to the private members.
     * If not ok, it will use the default values.
     * @param array $conf the configuration options
     * @return void
     */
    protected function _checkLineLengthConf($conf)
    {
        if (isset($conf['maxLineLength'])
        && is_int($conf['maxLineLength'])) {
            $this->_maxLineLength = $conf['maxLineLength'];
        } else {
            return;
        }

        // set a tag for this feature, or use the default?
        if (isset($conf['maxLineLengthTag'])
        && OPF_Logger::isValidTag($conf['maxLineLengthTag'])) {
            $this->_maxLineLengthTag = $conf['maxLineLengthTag'];
        }

        // level
        if (isset($conf['maxLineLengthLevel'])
        && OPF_Logger::isValidLevel($conf['maxLineLengthLevel'])) {
            $this->_maxLineLengthLevel = $conf['maxLineLengthLevel'];
        }

        // should cut the message?
        if (isset($conf['maxLineLengthCutMessage'])
        && $conf['maxLineLengthCutMessage'] === false) {
            $this->_maxLineLengthCutMessage = false;
        }
    }


    /**
     * Gathers debug information with the debug_backtrace function
     * @param integer $dep the depth to search in the back trace information
     * @return array<br>
     * keys:<br>
     * <ul>
     * <li>string $class The class name of the caller</li>
     * <li>string $function The execution method/function of the caller</li>
     * <li>int $line The line of the caller</li>
     * <li>string $date</li>
     * </ul>
     */
    protected function _getBacktraceVars($dep = 5)
    {
        $bt = debug_backtrace();
        $class = @$bt[$dep]['class'];
        $function = @$bt[$dep]['function'];
        $line = @$bt[$dep -1]['line'];
        if ($class == "") {
            $file = substr(strrchr(@$bt[$dep - 1]['file'], "/"), 1);
            $class = $file;
            $line = @$bt[$dep -1]['line'];
        }

        // have to shut up warnings.
        // shall we put it somewhere in the conf file?
        $date = date('[Y-m-d h:i:s]');

        return array($class, $function, $line, $date);
    }


    /**
     * Getter for litteral PHP log level
     * @param integer $PHPlevel
     * @return string
     */
    protected function _getLitteralPHPLevel($phpLevel)
    {
        switch ($phpLevel) {
            case E_WARNING:
                $ret = 'Runtime Warning';
                break;
            case E_NOTICE:
                $ret = 'Runtime Notice';
                break;
            case E_USER_ERROR:
                $ret = 'User Specific Error';
                break;
            case E_USER_WARNING:
                $ret = 'User Specific Warning';
                break;
            case E_USER_NOTICE:
                $ret = 'User Specific Notice';
                break;
            case E_STRICT:
                $ret = 'Strict';
                break;
            case E_RECOVERABLE_ERROR:
                $ret = 'Catchable Fatal Error';
                break;
            default:
                $ret = 'Unknown error';
                break;
        }
        return 'PHP '.$ret;
    }    
    
    
    /**
     * Returns the queryId
     * @return integer
     */
    public function getQueryId()
    {
        return $this->_queryId;
    }

}

<?php
/**
 * OPF_Logger is a logger mechanism.
 *
 * OPF_Logger is an easy to use logging system. It is compliant with Hebex
 *  recommendations and log practices at Orange Portal.
 * It supports 3 Log Handlers:
 * <ul>
 * <li>syslog</li>
 * <li>ApacheNotes</li>
 * <li>files</li>
 * </ul>
 * Logs are used for:
 * <ul>
 * <li>supervising platforms</li>
 * <li>computing services statistic and QoS</li>
 * <li>analyzing bugs occurrences in Production</li>
 * </ul>
 * 
 * @author   Stephan Acquatella, Sandro Lex
 * @version $Revision-Id: slex-20120214130126-b1yrolzfd1b6kjid $
 * @since   07/04/2010
 * @copyright France Telecom
 * @package OPF
 * @filesource OPF/Logger.php
 */

/**
 * Constants used to define log levels
 */
require_once 'OPF/Logger/Constants.php';

/**
 * <b>OPF_Logger</b>
 *
 * <b>Usage:</b>
 * <ol>
 * <li> include this file</li>
 * <li> initialise the logger with its configuration parameters<br>
 * <code>
 * OPF_Logger::init($opfConf);
 * </code></li>
 * <li> Send log events to the log handler
 * <code>
 * OPF_Logger::log('a log message', OPF_EMERG);
 * </code></li>
 * <li> Send log Stats with the result of the operation just before
 *  the end of the script
 * <code>
 * OPF_Logger::logStats(true, '200', 'request update user');
 * </code></li>
 * <li> Close the Logger
 * <code>
 * OPF_Logger::close();
 * </code>
 * </li></ol>
 *
 * @package OPF
 * @subpackage Logger
 */
class OPF_Logger
{

    /**
     * The default value used for maximum tolerance level of log.
     *
     * This value is used only if no maxPriority is specified
     * in the configuration
     * @var integer DEFAULT_MAX_PRIORITY
     */
    const DEFAULT_MAX_PRIORITY = OPF_NOTICE;


    /**
     * The maximum tolerance level of log events
     * @var integer
     */
    static private $_maxPriority = null;


    /**
     * The instance of the log handler.
     *
     * The log handler is responsible for writing log events to its destination
     * @see OPF_Logger_Handler_Abstract for more information
     * @var OPF_Logger_Handler_Abstract
     */
    static private $_handlers = array();


    /**
     * The statistics log handler.
     *
     * It is only used if the application wants to write QoS statistics to
     * a different handler than the log messages.
     * @var OPF_Logger_Handler_Abstract
     */
    static private $_statHandler = null;

    /**
     * True if the logger is ready to use (has been initialised)
     * @var boolean $_isReady
     */
    static private $_isReady = false;

    /**
     * If true it will enable the public method
     * setMaxPriority()
     * Unless you have a good reason to change this value,
     * it should always be set to false.
     * @var boolean $_authChangeMaxPriority
     */
    static private $_authChangeMaxPriority = false;


    /**
     * Keeps a reference of a standard php error to a OPF LEVEL
     * @var array
     */
    static protected $_opfPhpRelation = array(
        E_STRICT => OPF_INFO,
        E_USER_NOTICE => OPF_WARNING,
        E_NOTICE => OPF_WARNING,
        E_USER_WARNING => OPF_WARNING,
        E_WARNING => OPF_WARNING,
        E_USER_ERROR => OPF_ERR,
        E_ERROR => OPF_ERR,
        E_RECOVERABLE_ERROR => OPF_ERR
    );


    /**
     * Class construct
     * static class can't be instanciated
     */
    private function __construct()
    {

    }


    /**
     * Intialize and configure the logger. This is method is the equivalent
     * of a constructor. It should be called before using the Logger.
     * @param array $conf Configuration parameters of the Logger.<br>
     * accepted keys:<br><ul>
     * <li>integer maxPriority: the maximum level of tolerance for
     * log events</li>
     * <li>string ident: the application identification</li>
     * <li>string handler: the name of the log handler</li>
     * <li>(optional) string statHandler: the name of the statistics
     * handler</li>
     * <li>(optional) inteter facility: the syslog facility</li>
     * <li>(optional) string fileName: filePath of the log file</li>
     * </ul>
     * @param string $queryId an unique identification of the query/request
     * @return void
     * @throws Exception
     */
    static public function init(array $conf, $queryId = '')
    {
        // check mandatory configuration parameters
        self::_checkConf($conf);

        if (!empty($queryId)) {
            $conf['queryId'] = $queryId;
        }

        // if is a re-configuration, close the existing handler
        foreach (self::$_handlers as $key => $value) {
            if (isset(self::$_handlers[$key])) {
                self::$_handlers[$key]->close();
                unset(self::$_handlers[$key]);

            }
        }

        self::$_handlers = array();
        foreach ($conf['handler'] as $handlerName) {
            self::$_handlers[] = self::_initializeHandler($handlerName, $conf);
        }

        // instantiate the statistics handler if necessary
        if (isset($conf['statHandler'])) {
            if (isset(self::$_statHandler)) {
                self::$_statHandler->close();
            }
            self::$_statHandler =
            self::_initializeHandler($conf['statHandler'], $conf);
        }

        self::$_isReady = true;
    }


    /**
     * Initializes a logger handler object.
     *
     * It will try to include the handler class file if class doesn't exists
     * @param string $handlerName The name of the logger Handler.
     * @param array $conf Configuration parameter of the handler
     * @return void
     * @throws Exception
     */
    static private function _initializeHandler($handlerName, $conf)
    {
        $handlerClass = 'OPF_Logger_Handler_' . $handlerName;
        $handlerFile = 'Logger/Handler/' . $handlerName . '.php';

        // load the file containing the handler's class
        if (!class_exists($handlerClass, false)) {
            // check if handler file exists before including it
            if (!is_readable(dirname(__FILE__) . "/" . $handlerFile)) {
                throw new Exception(
                    "OPF_Logger: Could not load handler file $handlerFile"
                );
            }
            // include the handler class file
            include_once $handlerFile;
        }

        // instanciate a handler obj
        if (class_exists($handlerClass, false)) {
            $myHandler = new $handlerClass($conf);
            // open the handler for use
            if ($myHandler->open()) {
                // set the priority
                $myHandler->setMaxPriority(self::$_maxPriority);
                return $myHandler;
            } else {
                throw new Exception(
                    "OPF_Logger: Could not open log handler for $handlerName"
                );
            }
        } else {
            throw new Exception(
                "OPF_Logger: Could not instanciate Handler $handlerClass"
            );
        }
    }


    /**
     * Send a log event to the handler.
     *
     * @param mixed $message The log message.
     * @param int $level the priority of the log message
     * @param string $tag the optional tag name. This field is used to
     * categorise the log messages. It can be used to parse log files
     * and easily identify some topic specific messages.
     * @return void
     * @throws Exception
     */
    static public function log($message, $level = OPF_ERR, $tag = '')
    {
        foreach (self::$_handlers as $handler) {
            $handler->log($message, $level, $tag);
        }
    }


    /**
     * Calls the handler close method.
     *
     * @return boolean
     */
    static public function close()
    {
        if (isset(self::$_handlers) && is_array(self::$_handlers)) {
            foreach (self::$_handlers as $key => $value) {
                self::$_handlers[$key]->close();
                unset(self::$_handlers[$key]);
            }
        }

        if (isset(self::$_statHandler)) {
            self::$_statHandler->close();
            self::$_statHandler = null;
        }

        self::$_isReady = false;
    }


    /**
     * ShortCut for QoS Statistic log event.
     *
     * @param boolean $queryResult the result of the operation.
     *        True: OK
     *        False: KO
     * @param integer $code Application status code
     * @param string $message The message to be logged
     * @param string $tag a tag to be associated to the me	ssage
     * @return void
     */
    static public function logStat($queryResult, $code, $message = '',
    $tag = '')
    {
        if (isset(self::$_statHandler)) {
            self::$_statHandler->logStat($queryResult, $code, $message, $tag);
        } else {
            foreach (self::$_handlers as $handler) {
                $handler->logStat($queryResult, $code, $message, $tag);
            }
        }
    }


    /**
     * ShortCut for logging an OPF_DEBUG event.
     *
     * @param string $message The message to be logged
     * @param mixed @datas
     * @param string $tag the optional tag name
     * @return void
     */
    static public function logDebug($message, $datas = '', $tag = '')
    {
        foreach (self::$_handlers as $handler) {
            $handler->logDebug($message, $datas, $tag);
        }
    }


    /**
     * Send a special hebex log message
     * @param mixed $data a container of datas
     * @param string $tag a tag to be associated to the message
     * @return void
     */
    static public function logData($data, $tag = '')
    {
        foreach (self::$_handlers as $handler) {
            $handler->logData($data, $tag);
        }
    }


    /**
     * Checks the mandatory configuration parameter.
     *
     * @param array $conf the configuration parameters array
     * @return void
     * @throws Exception in case of missing parameter
     */
    static private function _checkConf(&$conf)
    {
        if (!isset($conf['handler']) || empty($conf['handler'])) {
            throw new Exception(
                'OPF_Logger: Missing mandatory parameter: handler'
                );
        } else {
            if (!is_array($conf['handler'])) {
                // if it is a string, create an array for it
                $handler = $conf['handler'];
                $conf['handler'] = array($handler);
            }
        }

        if (!isset($conf['ident']) || empty($conf['ident'])) {
            throw new Exception(
                'OPF_Logger: Missing mandatory parameter: ident'
                );
        }

        if (!isset($conf['maxPriority'])) {
            self::$_maxPriority = self::DEFAULT_MAX_PRIORITY;
        } else {
            self::$_maxPriority = $conf['maxPriority'];
        }

        if (isset($conf['authChangeMaxPriority'])) {
            self::$_authChangeMaxPriority = $conf['authChangeMaxPriority'];
        }

        if (isset($conf['setUserErrorHandler']) && $conf['setUserErrorHandler']) {
            self::initErrorHandler();
        }
    }

    /**
     * Init for declaring a OPF handler for PHP error
     * @return void
     */
    static public function initErrorHandler()
    {
        set_error_handler(array(__CLASS__, 'errorHandler'));
    }

    /**
     * Handler for PHP Error
     *
     * @param int $errno The number of PHP Level error
     * @param string $errstr The message to log
     * @param string $errfile File of error
     * @param int $errline Line of erreur
     * @param array $errcontext The context of PHP error
     * @return void
     */
    static public function errorHandler($errno, $errstr, $errfile, $errline, 
        array $errcontext)
    {
        if (isset(self::$_opfPhpRelation[$errno])) {
            $level = self::$_opfPhpRelation[$errno];
        } else {
            $level = OPF_ERR;
        }

        foreach (self::$_handlers as $handler) {
            $handler->logPHPError($errstr, $level, $errno, $errcontext);
        }
    }


    /**
     * Gets the last log event that was written by the Handler.
     *
     * @return string the formatted message written
     */
    static public function getLastMessage()
    {
        return self::$_handlers[0]->getLastMessage();
    }


    /**
     * True if the Logger has been correctly initialised and is ready to use
     * @return boolean
     */
    static public function isReady()
    {
        return self::$_isReady;
    }

    /**
     * Checks if a tag is a valid tag to be used with OPF_Logger
     * @param string $tag the tag to be verified
     * @return boolean
     */
    static public function isValidTag($tag)
    {
        if (!is_string($tag)) {
            return false;
        }

        if (strlen($tag) > 15) {
            return false;
        }

        if (strlen($tag) < 3) {
            return false;
        }

        return true;
    }

    /**
     * Checks if a level is a valid level to be used with OPF_Logger
     *
     * see OPF/Logger/Constants.php for a list of possible values
     * @param integer $level the level to be verified
     * @return boolean
     */
    static public function isValidLevel($level)
    {
        if (!is_int($level)) {
            return false;
        }

        if ($level < -1) {
            return false;
        }

        if ($level > 7) {
            return false;
        }

        return true;
    }

    /**
     * Gets a string with the name of a log level
     * @param integer $level see OPF/Logger/Constants.php for list of
     * possible values
     * @return string the name of the log level
     */
    public static function getStringFromLevel($level)
    {
        if (!is_int($level)) {
            return 'UNKNOWN LEVEL';
        }

        switch($level) {
            case OPF_NOLOG:
                return 'OPF_NOLOG';
                break;
            case OPF_EMERG:
                return 'OPF_EMERG';
                break;
            case OPF_ALERT:
                return 'OPF_ALERT';
                break;
            case OPF_CRIT:
                return 'OPF_CRIT';
                break;
            case OPF_ERR:
                return 'OPF_ERR';
                break;
            case OPF_WARNING:
                return 'OPF_WARNING';
                break;
            case OPF_NOTICE:
                return 'OPF_NOTICE';
                break;
            case OPF_INFO:
                return 'OPF_INFO';
                break;
            case OPF_DEBUG:
                return 'OPF_DEBUG';
                break;
            default:
                return 'UNKNOWN LEVEL';
                break;
        }
    }

    /**
     * Sets the maximum priority tolerance of log events.
     *
     * @param integer $priority the tolerance level to be set
     * @return void
     */
    public static function setMaxPriority($priority)
    {
        if (!self::$_authChangeMaxPriority) {
            return;
        }

        foreach (self::$_handlers as $handler) {
            $handler->setMaxPriority($priority);
        }
    }
    
    
    /**
     * Returns the first handler queryId
     * In theory it doens't matter which handler 
     * they all should have the same queryId
     * @return integer
     */
    public static function getQueryId()
    {
        return self::$_handlers[0]->getQueryId();
    }

}


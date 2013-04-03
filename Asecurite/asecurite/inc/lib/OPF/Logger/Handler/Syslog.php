<?php
/**
 * This file contains class definition for Syslog log handler
 *
 * @author Stephan Acquatella, Philippe Bouery, Sandro Lex
 * @version $Revision-Id: slex-20111020132927-ccpcd214y95tvua4 $
 * @since   12/12/2007
 * @copyright France Telecom
 * @package OPF
 * @filesource OPF/Logger/Syslog/Syslog.php
 */


/**
 * OPF_Logger_Handler_Syslog extends the console log handler
 */
include_once 'OPF/Logger/Handler/Console.php';

/**
 * <b>OPF_Logger_Handler_Syslog</b>
 *
 * OPF_Syslog class is a log handler that uses syslog
 * 
 * @package OPF
 * @subpackage Logger
 */
class OPF_Logger_Handler_Syslog 
extends OPF_Logger_Handler_Console
{
    /**
     * Holds the syslog facility 
     * @var int
     */
    private $_facility = null;

    private $_options = LOG_PID;
    
    /**
     * Class constructor
     * @param array $conf Syslog configuration parameters
     * @return void
     */
    public function __construct(array $conf)
    {
        if (isset($conf['Syslog']['facility'])) {
            $this->setFacility($conf['Syslog']['facility']);
        }
        
        $this->_setOptionsValue($conf);        
        
        parent::__construct($conf);
    }


    /**
     * Tries to open the syslog
     * @return boolean 
     */
    public function open()
    {
        if (!$this->_isOpened) {
            if (PHP_OS === 'WINNT') {
                // parameter 3 LOG_LOCAL0 to LOG_LOCAL7
                // doesn't exist on Windows
                $this->_isOpened = openlog(
                    $this->_ident, 
                    $this->_options, 
                    LOG_USER
                );
            } else {
                // if facility not set, use a default value
                if ($this->_facility === null) {
                    $this->_facility = LOG_LOCAL6;
                }
                
                $this->_isOpened = openlog(
                    $this->_ident, 
                    $this->_options, 
                    $this->_facility
                );
            }
            return $this->_isOpened;
        }

        return false;
    }

    
    /**
     * Closes the syslog
     * @return boolean
     */
    public function close()
    {
        if ($this->_isOpened) {
            $this->_isOpened = !closelog();
            return true;
        }
        return false;
    }

    
    /**
     * Configures syslog facility
     * @param int $facility
     * @return void
     */
    public function setFacility($facility)
    {
        if (!is_long($facility)) {
            return false;
        } 
        
        // if syslog already opened, have to close it before
        // setting facilty, and the re-open it.
        if ($this->_isOpened) {
            $this->close();
            $this->_facility = $facility;
            return $this->open();
        } else {
            $this->_facility = $facility;
            return true;
        }
    }

    
    /**
     * Writes an event to the syslog
     * @param string $message The message to log
     * @param int $level the priority level of the log message
	 * @param string $logType Type of log (log || debug || data || stats)
     * @return void
     */
    protected function _write($message, $level = OPF_ERR)
    {
        syslog($level, $message);
    }

    
    /**
     * delete all '\n' entries of the message to avoid the #012 in the output
     * @param string $message the message to log
     * @param mixed $datas a container of datas to be ouputed with 
     * the print_r function
     * @param string $tag a tag to be associated to the message
     * @return void
     */
    protected function _formatDebug(&$message, $datas = '', $tag = '')
    {
        parent::_formatDebug($message, $datas, $tag);
        
        // avoid the output of #12 in syslog messages
        $message = str_replace("\n", '', $message);
    }
    
    /**
     * Sets the options value 
     * 
     * It is the second parameter of openlog function
     * @param array $conf
     * @returns void
     */
    private function _setOptionsValue(array $conf)
    {
        if (isset($conf['Syslog']['options'])) {
            if (is_integer($conf['Syslog']['options'])) {
                $this->_options = $conf['Syslog']['options'];
            }
        } else {
            $this->_options = LOG_PID;
        }
    }
    
}


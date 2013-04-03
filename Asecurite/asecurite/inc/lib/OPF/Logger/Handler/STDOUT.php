<?php
/**
 * This file contains class definition to STDOUT log handler
 * 
 * @author   Sandro Lex
 * @version $Revision-Id: slex-20111020132927-ccpcd214y95tvua4 $
 * @since   20/08/2011
 * @copyright France Telecom
 * @package OPF
 * @filesource OPF/Logger/Syslog/STDOUT.php
 */

/**
 * STDOUT extends console handler
 */
include_once 'OPF/Logger/Handler/Console.php';

/**
 * <b>OPF_Logger_Handler_STDOUT</b>
 *
 * OPF_STDOUT class is a log handler that uses standard output
 * 
 * @package OPF
 * @subpackage Logger
 */
class OPF_Logger_Handler_STDOUT 
extends OPF_Logger_Handler_Console
{
    /**
     * Have to implement just to be compatible with Abstract class
     * @return boolean always true
     */
    public function open()
    {
        return true;
    }
     
    /**
     * Have to implement just to be compatible with Abstract class
     * @return boolean always true
     */
    public function close()
    {
        return true;
    }
    
    /**
     * Writes the formatted message to the standard output using echo
     * @param string $message the formatted message
     * @param integer $level the log level of the message
     * @return void
     */
    protected function _write($message, $level = OPF_ERR)
    {
        echo $message . PHP_EOL;
    }
     
    /**
     * Formats the log message 
     * @param string $message
     * @param integer $level
     * @param string $tag
     * @return void
     */
    protected function _formatMsg(&$message, $level = OPF_ERR, $tag = '')
    {
        parent::_formatMsg($message, $level, $tag);
       
        $message = $this->_createHeader($level) . $message . PHP_EOL;
    }
    
    /**
     * Formats the debug log message
     * @param string $message
     * @param array $datas
     * @param string $tag
     * @return void
     */
    protected function _formatDebug(&$message, $datas = '', $tag = '')
    {
        parent::_formatDebug($message, $datas, $tag);
        
        $message = $this->_createHeader(OPF_DEBUG) . $message . PHP_EOL;
    }
    
    /**
     * Formats the stat message
     * @param boolean $result
     * @param integer $code
     * @param string $message
     * @param string $tag
     * @return void
     */
    protected function _formatStats($result, $code, &$message, $tag = '')
    {
        parent::_formatStats($result, $code, $message, $tag);
        
        $message = $this->_createHeader(OPF_NOTICE) . $message . PHP_EOL;
    }
    
    /**
     * format a datas log message
     * @param array $data
     * @param string $tag
     * @return string the formatted message
     */
    protected function _formatData($data, $tag)
    {
        $msg = parent::_formatData($data, $tag);
        
        return $this->_createHeader($this->_levelData) . $msg . PHP_EOL;
    }
}

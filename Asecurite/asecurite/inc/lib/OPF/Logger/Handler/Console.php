<?php
/**
 * This file contains the definition of Console log handler
 * 
 * @author  Sandro Lex
 * @version $Revision-Id: dbuteau.ext@orange.com-20120918080813-3r5vl067txirgvor $
 * @since   20/08/2011
 * @copyright France Telecom
 * @package OPF
 * @filesource OPF/Logger/Handler/Console.php
 */

/**
 * OPF_Logger_Handler_Console extends handler abstract
 */
require_once 'OPF/Logger/Handler/Abstract.php';

/**
 * <b>OPF_Logger_Handler_Console</b>
 *
 * OPF_Log_Handler_Console class defines the common members
 * of log handler that writes output to console (not a browser)
 *
 * @package OPF
 * @subpackage Logger
 *
 */
abstract class OPF_Logger_Handler_Console
extends OPF_Logger_Handler_Abstract
{
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
        $message = '[' . $this->_getLitteralPHPLevel($phpLevel) . '] ' 
            . $message;
        $this->_formatMsg($message, $opfLevel);
    }

    /**
     * Creates the formatted log message string to write
     * @param string $code The status code of the application
     * @param string $message The message to log
     * @param string $tag the optional tag name
     * @return void
     */
    protected function _createStatMessage($code, &$message, $tag = '')
    {
    	/* @bug #292392
    	 * @author dbuteau
    	 * if conf value queryId is given at false then don't write queryId in log
    	 */ 
    	$messageFromParam = $message; 
    	$message = '';
    	if($this->_queryId != false){
    		$message .= $this->_queryId . ' | '; 
    	}
    	/* end bugfix */
        $message .= $this->_resultStatus . ' | '
        . $this->_executionTime .  ' | ' . $code . ' | ' . $messageFromParam;

        if (!empty($tag)) {
            $message = '#' . $tag . ' | ' . $message;
        }
    }


    /**
     * Format the log message.
     *
     * Should be implemented by a real log handler as well
     * @param &string $message The message to log
     * @param int $lelevelvel Priority level of the log event
     * @param string $tag the optional tag name
     * @return void
     */
    protected function _formatMsg(&$message, $level = OPF_ERR, $tag = '')
    {
    	/* @bug #292392
    	 * @author dbuteau
    	 * if conf value queryId is given at false then don't write queryId in log
    	 */
    	$messageFromParam = $message;
    	$message = '';
    	if($this->_queryId != false){
    		$message .= $this->_queryId . ' | ';
    	}
    	/* end bugfix */
        $message .=  $messageFromParam;

        if (!empty($tag)) {
            $message = '#' . $tag . ' | ' . $message;
        }
    }

    /**
     * Creates the format of a data message specific to the console ouput
     *
     * @param string $jsonStr the data array in a json string representation
     * @param string $tag
     * @return string the formatted message
     */
    protected function _createDataMessage($jsonStr, $tag)
    {
    	/* @bug #292392
    	 * @author dbuteau
    	 * if conf value queryId is given at false then don't write queryId in log
    	 */
    	$return = '#' . $tag . ' | ';
		if($this->_queryId != false){
			$return.= $this->_queryId . ' | '; 
		}
    	$return.= $jsonStr;
        return $return;
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
        $bt = $this->_getBacktraceVars();

        if (is_array($datas) || is_object($datas)) {
            $datas = print_r($datas, true);
        }

        $message = $bt[0] . '::' . $bt[1] . '::' . $bt[2]
        . ': ' . $message. ' ' . $datas;

        if (!empty($tag)) {
            $message = '#' . $tag . ' | ' . $message;
        }
        /* @bug #292392
         * @author dbuteau
        * if conf value queryId is given at false then don't write queryId in log
        */
        if($this->_queryId != false){
        	$message =  $this->_queryId . ' | ' . $message;
        }
    }



    /**
     * Creates the header of the log message
     * with the formated date, server name and process id
     *
     * @param integer $level The log level of the message
     * @return string the head to be appended to the log message
     */
    protected function _createHeader($level)
    {
        $date = date('M d H:i:s');
        $head = OPF_Logger::getStringFromLevel($level) . ' | '
        . $date . ' ' . $this->_serverName . ' ' . $this->_ident
        . '[' . getmypid() . ']: ';

        return $head;
    }


}

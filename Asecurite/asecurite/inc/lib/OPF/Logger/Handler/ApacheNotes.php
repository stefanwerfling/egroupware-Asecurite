<?php
/**
 * This file contains class defintion for the ApacheNotes log handler
 * 
 * @author   Stephan Acquatella, Sandro Lex
 * @version $Revision-Id: slex-20111020132927-ccpcd214y95tvua4 $
 * @since   07/04/2010
 * @copyright France Telecom
 * @package OPF
 * @filesource OPF/Logger/Handler/ApacheNotes.php

 */

/**
 * Apache notes extends the console handelr
 */
include_once 'OPF/Logger/Handler/Console.php';

/**
 * <b>OPF_Logger_Handler_ApacheNotes</b>
 *
 * OPF_ApacheNotes class is a handler that writes log events
 * to Apache request notes  using the LogFormat configuration 
 * directive of Apache Web Server.
 *    
 * <b>Usage</b>
 * <ol>
 * <li> Configure a LogFormat or CustomLog configuration directive in
 * the Apache Web Server with the expected notes.<br>
 * <code>
 * CustomLog /var/log/tmp-notes.log "%h %{method}n %{m_country_code}n"
 * </code>
 * <br/></li>
 * 
 * <li> Send log events with the notes
 * <code>
 * $myNotes = array('method' => 'get',
 *                  'm_contry_code' = 'BR',
 *                  'filler' = > 'not used');
 * </code>
 * <br/></li>
 * 
 * <li> This will produce the log line: 
 * <code>
 * 127.0.0.1 get FR
 * </code>
 * <br/></li>
 * </ol>
 *  
 * @package OPF
 * @subpackage Logger
 * 
 */
class OPF_Logger_Handler_ApacheNotes 
extends OPF_Logger_Handler_Console
{
    /**
     * Class construct
     * @param array $conf the configuration parameters
     * @return void
     */
    public function __construct(array $conf)
    {
        $this->_isOpened = true;
        parent::__construct($conf);
    }
    
    
    /**
     * Not used
     * @return boolean always true
     */
    public function open()
    {
        return true;
    }

    
    /**
     * Not used
     * @return boolean always true
     */
    public function close()
    {
        return true;
    }


    /**
     * Writes a log event to apache request note
     * @param array $message the log events disposed in an 
     * associative array key => value
     * Ex:<br>
     * <code>
     * $message = array('method' => 'get',
     *                  'country' => 'FR',
     *                  'ise' => 'xxx');
     * </code>                  
     * @param int $level Not Used
     * @return void
     * @throws Exception              
     */
    protected function _write($message, $level = OPF_ERR)
    {
        $this->_sendRequestNote($message);
    }
    
    
    /**
     * Send the message to Apache Notes
     * @param array $message associative array containing the notes 
     * keys and values
     * @param integer $level not used
     * @param string $tag not used
     * @return void
     * @throws Exception
     */
    public function log($message, $level = OPF_ERR, $tag = '')
    {
        $this->_write($message, $level);
    }
    
    /**
     * Not used
     * @param array $messagge
     * @param array $datas
     * @param string $tag
     * @return void
     */
    public function logDebug($messagge, $datas = '', $tag = '')
    {
        return;
    }
    
    
    /**
     * Not used
     * @param mixed $data
     * @param string $tag
     * @return void
     */
    public function logData($data, $tag = '')
    {
        return;
    }
    
    /**
     * Writes a QoS statistics event
     * @param boolean $result The result of the operation
     * @param string $code Application's status code
     * @param string $message The message to log
     * @param string $tag a tag to be associated to the message
     * @return void
     * @throws Exception 
     */
    public function logStat($result, $code, $message, $tag = '')
    {
        $this->_formatStats($result, $code, $message, $tag);
        
        $this->_sendRequestNote($message);
    }
    
    
    /**
     * Creates an associative array of variables/values
     * to send to apache request note
     * @param string $code Application's status code
     * @param string $message The message to log
     * @return void
     */
    protected function _createStatMessage($code, &$message, $tag = '')
    {
        $message = array('status' => $this->_resultStatus,
                        'ttr' => $this->_executionTime,
                        'code' => $code,
                        'id_req' => $this->_queryId,
                        'message' => $message);
    }
    
    
    /**
     * Checks if apache_notes is present 
     * if so, call it for each var of the given array.
     * @param array $notes associative array containing the notes 
     * keys and values
     * @return void
     * @throws Exception 
     */
    private function _sendRequestNote(array $notes)
    {
        if (!function_exists('apache_note')) {
            throw new Exception(
                'OPF_Logger: apache_note function not present'
            );
        }
        
        foreach ($notes as $key => $value) {
            apache_note($key, $value);
        }
    }
    
}

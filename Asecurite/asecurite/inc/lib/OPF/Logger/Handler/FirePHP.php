<?php 
/**
 * This file contains class definition for FirePHP log handler
 * 
 * @author  Leiha Selllier
 * @version $Revision-Id: slex-20111020132927-ccpcd214y95tvua4 $
 * @since   25/08/2011
 * @copyright France Telecom
 * @package OPF
 * @subpackage Logger
 * @filesource OPF/Logger/Handler/FirePHP.php
 */

/**
 * OPF_Logger_Handler_FirePHP extends the browser log handler
 */
include_once 'OPF/Logger/Handler/Browser.php';

/**
 * Used to encode/decode Json structures
 */
require_once('OPF/Json.php');


/**
 * <b>OPF_Logger_Handler_FirePHP</b>
 *
 * OPF_Log_Handler_FirePHP handle log events to be used into firePHP
 * module of fireBug
 *
 * @package OPF
 * @subpackage Logger
 *
 */
class OPF_Logger_Handler_FirePHP 
extends OPF_Logger_Handler_Browser
{
    /**
     * Holds the HTTP header
     * @var string
     */
    protected $_headers = array(
        'X-Wf-Protocol-1' => 
            'http://meta.wildfirehq.org/Protocol/JsonStream/0.2',
        'X-Wf-1-Plugin-1' => 
            'http://meta.firephp.org/Wildfire/Plugin/FirePHP/Library-FirePHPCore/0.2.1',
        'X-Wf-1-Structure-1' => 
            'http://meta.firephp.org/Wildfire/Structure/FirePHP/FirebugConsole/0.1'
    );

    /**
     * Class constructor
     * Writes the event for the QueryId
     * @param array $conf
     */
    public function __construct(array $conf)
    {
        parent::__construct($conf);

        $this->_setLogForRequestID();
    }

    /**
     * Returns the firePHP level correspoding to a OPF level
     * @param integer $opfLevel
     */
    protected function _getFirePHPLevel($opfLevel)
    {
        switch($opfLevel) {
            case OPF_EMERG:
                return 'ERROR';
                break;
            case OPF_ALERT:
                return 'ERROR';
                break;
            case OPF_CRIT:
                return 'ERROR';
                break;
            case OPF_ERR:
                return 'ERROR';
                break;
            case OPF_WARNING:
                return 'WARN';
                break;
            case OPF_NOTICE:
                return 'INFO';
                break;
            case OPF_INFO:
                return 'INFO';
                break;
            case OPF_DEBUG:
            default:
                return 'LOG';
                break;
        }
    }

    /**
     * Method for rendering all messages
     * @return void
     */
    protected function _render()
    {
        foreach ($this->_headers as $name => $value) {
            header($name . ': ' . $value);
        }
        
        $this->_alreadyRendered = true;
    }

    /**
     * Method for rendering one message
     * @param array $item Elements for building a complete item
     * @return void
     */
    protected function _renderItem($item)
    {
        // Envoi de l'header de groupe 
        $this->_setHeader('GROUP_START', ' ');

        // Envoi du header d'entete 
        if (isset($item['PHPLevel'])) {
            $literal = $this->_getLitteralPHPLevel($item['PHPLevel']);
        } else {
            $literal = $this->_getLitteralOPFLevel($item['OPFLevel']);
        }
        
        $tag = '';
        if (!empty($item['tag'])) {
            $tag = ' | Tag : ' . $item['tag'];
        }
        
        $element = $this->_buildElement($literal . $tag);

        $this->_setHeader($this->_getFirePHPLevel($item['OPFLevel']),
            '',
            $element);
            
        // Envoi du header de message
        $element = '';
        if (isset($item['msg']) && !empty($item['msg'])) {
            $element = $this->_buildElement($item['msg']);
            $this->_setHeader('LOG', 'Message', $element);    
        } 
        
        // Envoi des headers de message 
        $tag = 'Datas';
        if (!empty($item['tag']) && $item['tag'] == 'PHP') {
            $tag = 'Context';
        }
        
        $element = '';
        if (isset($item['data']) && !empty($item['data'])) {
            $element = $this->_buildElement($item['data']);
            $this->_setHeader('LOG', $tag, $element);
        }

        // Envoi de l'header des Backtraces
        $backtraces = $this->_getBackTraces();
        $btLine = 'BackTrace -> Line [ ' . $backtraces[0][2]
            . ' ] on File : [ '
            . addslashes($backtraces[0][1])
            . ' ]';
            
        $this->_setHeader('TABLE',
            $btLine,
            OPF_Json::encode($backtraces));

        // Envoi de l'header de fin du groupe
        $this->_setHeader('GROUP_END');
    }

    /**
     * 
     * Stack FirePHP header formatted
     * @param string $type
     * @param string $label
     * @param string $message
     * @return void
     */
    protected function _setHeader($type, $label = '', $message = '')
    {
        $txt = '';
        if (!empty($message)) {
            $txt = ',' . $message;
        }
        
        $message = '[{"Type":"'
            . $type
            . '","File":"","Line":"","Label":"'
            . $label.'"}'
            . $txt
            . ']';
                            
        $this->_headers['X-Wf-1-1-1-' . (++$this->_nbMessage)] = 
            strlen($message) . '|' . $message . '|';
    }
    
    
    /**
     * Builds a firePHP element
     * @param mixed 
     * @return string
     */
    protected function _buildElement($msg)
    {
        switch (true) {
            case is_array($msg):
            case is_object($msg):
                $msg = OPF_Json::encode($msg);
                break;
            case is_string($msg):
                $msg = '"' . addcslashes($msg, '"\\') . '"';
                break;
        }
        
        return $msg;
    }
}

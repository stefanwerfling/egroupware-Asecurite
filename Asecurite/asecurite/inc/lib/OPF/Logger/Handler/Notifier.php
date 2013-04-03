<?php
/**
 * This file contains class definition for the Notifier log handler
 * 
 * @author  Leiha Sellier
 * @version $Revision-Id: slex-20111020132927-ccpcd214y95tvua4 $
 * @since   25/08/2011
 * @copyright France Telecom
 * @package OPF
 * @filesource OPF/Logger/Handler/Notifier.php
 */

/**
 * Notifier extends browser handler
 */
include_once 'OPF/Logger/Handler/Browser.php';


/**
 * It will use the OPF's ObBuffer class 
 */
include_once 'OPF/ObBuffer.php';

/**
 * <b>OPF_Logger_Handler_Notifier</b>
 *
 * OPF_Log_Handler_Notifier sends the log to the browser using an HTML format
 * @package OPF
 * @subpackage Logger
 *
 */
class OPF_Logger_Handler_Notifier
extends OPF_Logger_Handler_Browser
{
    /**
     * Class constructor
     * Sets the queryId into the log stack 
     * @param array $conf
     */
    public function __construct(array $conf)
    {
        parent::__construct($conf);

        $this->_stack($this->_setLogForRequestID());
    }

    /**
     * Gets the color to the corresponding OPF level
     * @param integer $OPFlevel
     * @return string
     */
    protected function _getColorOPFLevel($opfLevel)
    {
        switch ($opfLevel) {
            case OPF_EMERG: 
                $ret = 'red';
                break;
            case OPF_ALERT:
                $ret = 'red';
                break;
            case OPF_CRIT:
                $ret = 'red';
                break;
            case OPF_ERR:
                $ret = 'red';
                break;
            case OPF_WARNING: 
                $ret = '#ff7f00';
                break;
            case OPF_NOTICE: 
                $ret = '#ff7f00';
                break;
            case OPF_INFO: 
                $ret = 'blue';
                break;
            case OPF_DEBUG: 
                $ret = 'blue';
                break;
            default: 
                $ret = 'black';
                break;
        }
        
        return $ret;
    }

    /**
     * Method for render all messages
     * 
     * it will display the result with an echo command
     * @return void
     */
    protected function _render()
    {
        $obBuffer = new OPF_ObBuffer();
        $obBuffer->set('content', implode('', $this->_stackLogs));
        $divLogs = $obBuffer->fetch('Logger/Handler/Notifier-popin.tpl');
        echo $divLogs;

        $this->_alreadyRendered = true;
    }

    /**
     * Method for render one message
     * @param array $item Log elements for building a complete item
     * @return string
     */
    protected function _renderItem($item)
    {
        // prepare the contents first
        $backtraces = $this->_getBackTraces();
        $lastBacktrace = $backtraces[0];

        if (isset($item['PHPLevel'])) {
            $literal = $this->_getLitteralPHPLevel($item['PHPLevel']);
        } else {
            $literal = $this->_getLitteralOPFLevel($item['OPFLevel']);
        }
        
        $tag = '';
        if (!empty($item['tag'])) {
            $tag = $item['tag'];
        }
        
        $msg = '';
        if (!empty($item['msg'])) {
            $msg = $item['msg'];
        }
        
        $data = '';
        $dataTitle = '';
        if (!empty($item['data'])) {
            if (!empty($item['tag']) && $item['tag'] == 'PHP') {
                $dataTitle = 'Context';
            } else {
                $dataTitle = 'Datas';
            }
            
            $data = $item['data'];
            if (is_array($data) || is_object($data)) {
                $data = $this->_code(print_r($data, true));
            }
        }
       
        $obBuffer = new OPF_ObBuffer();
        $obBuffer->set(array(
            'itemNumber'         => ++$this->_incrementer,
            'itemTitle'          => $literal,
            'itemTitleColor'     => $this->_getColorOPFLevel($item['OPFLevel']),
            'itemTag'            => $tag,
            'itemMessage'        => $msg,
            'itemData'           => $data,
            'itemDataTitle'      => $dataTitle,
            'itemBacktracesCount'=> count($backtraces) - 1,
            'itemBacktraces'     => $backtraces            
        ));
        $message = $obBuffer->fetch('Logger/Handler/Notifier-popin-body.tpl');
        return $message;
    }   

    /**
     * 
     * Format with colors PHP code
     * @param string $string
     * @return string
     */
    protected function _code($string)
    {
        $patterns = array(
            "\[(.*?)\][\s]{0,}(=>)[\s]{0,}(.*)"             => 
                '[ <span class="OPFLog_arrKey">${1}</span> ]'
                .' <span class="OPFLog_arrPunc">${2}</span>'
                .' <span class="OPFLog_arrValue">${3}</span>'
             ,"\[(.*?)\][\s]{0,}(=>)[\s]{0,}array"          => 
                '<span class="OPFLog_arrKey">${1}</span>'
                .'<span class="OPFLog_arrPunc">${2}</span>array'
             ,"\[(.*?)\][\s]{0,}(=>)[\s]{0,}(true|false)"   => 
                '<span class="OPFLog_arrKey">${1}</span>'
                .'<span class="OPFLog_arrPunc">${2}</span>${3}'
             ,"Array"                                       => 
                '<span class="OPFLog_arr">array</span>'
             ,"(true|false|null)"                           => 
                '<span class="OPFLog_arrBoolean">${1}</span>'
             ,"([,\(\)\[\]])"                               => 
                '<span class="OPFLog_arrPunc">${1}</span>'
        );

        foreach ($patterns as $pattern => $replace) {
            $string = preg_replace("/" . $pattern . "/", $replace, $string);
        }
        
        return $string;
    }
         
}

<?php

/**
 * <b>File class.bo_fwktools.inc.php</b>
 * Tools library for eGroupware applications
 * @author N'faly KABA
 * @since   02/04/2013
 * @version 1.0
 * @copyright France Telecom
 * @subpackage tools
 * @filesource  class.bo_fwktools.inc.php
 */

class bo_fwktools {

    /**
     * Constructor
     * Static Class, can't be instancied
     */
    private function __construct() {
        
    }

    /**
     * Transform to UTF8 only if string is not UTF8
     * 
     * @param string string to transform to UTF8
     * @return string UTF8 encoded string
     */
    public static function do_utf8($mystring) {
        if (!mb_check_encoding($mystring, 'UTF-8'))
            $mystring = utf8_encode($mystring);
        return $mystring;
    }

    /**
     * 
     * Enter description here ...
     */
    public static function random_color() {
        mt_srand((double) microtime() * 1000000);
        $c = '';
        while (strlen($c) < 6) {
            $c .= sprintf("%02X", mt_rand(0, 255));
        }
        return $c;
    }

    /**
     * create thumbnail for an image into a file
     * @param string $filename image filename
     * @param int $width thumnail $width
     * @param int $height thumnail $height
     * @param string $msg error message to display
     * @return bool true on success and false on failure
     */
    public static function create_thumb($filename, $width, $height, &$msg) {
        $pos = strripos($filename, '.');
        if ($pos !== false) {
            switch (strtolower(substr($filename, $pos + 1))) {
                case 'jpg':
                case 'jpeg':
                    $type = 'jpg';
                    $source = imagecreatefromjpeg($filename);
                    break;
                case 'png':
                    $type = 'png';
                    $source = imagecreatefrompng($filename);
                    break;
                case 'gif':
                    $type = 'gif';
                    $source = imagecreatefromgif($filename);
                    break;
                default:
                    $msg = 'unknown format';
                    return false;
            }
            $source_width = imagesx($source);
            $source_height = imagesy($source);
            $destination_width = $width;
            $destination_height = $height;

            if (!$destination = imagecreatetruecolor($width, $height)) {
                $msg = 'Unable to create thumbnail';
                return false;
            }
            if (!imagecopyresampled($destination, $source, 0, 0, 0, 0, $destination_width, $destination_height, $source_width, $source_height)) {
                $msg = lang('Error on') . ' imagecopyresampled';
                return false;
            }
            return self::print_img($type, $destination, $filename);
        } else {
            $msg = 'no extension found';
            return false;
        }
    }

    /**
     * output image to file
     * @param string $type image type
     * @param string $destination input destination
     * @param string $filename input filename (path to given image)
     * @return boolean true on success and false on error
     */
    public static function print_img($type, $destination, $filename) {
        switch ($type) {
            case 'jpg':
            case 'jpeg':
                if (!imagejpeg($destination, $filename)) {
                    $msg = lang('Error on') . ' imagejpeg';
                    return false;
                }
                break;
            case 'png':
                if (!imagepng($destination, $filename)) {
                    $msg = lang('Error on') . ' imagepng';
                    return false;
                }
                break;
            case 'gif':
                if (!imagegif($destination, $filename)) {
                    $msg = lang('Error on') . ' imagegif';
                    return false;
                }
                break;
            default:
                $msg = 'unknown format';
                return false;
        }
        return true;
    }

    /**
     * build an array of all mime type available in egroupware
     * @return array 
     */
    public static function build_mime() {
        return array(
            'jpg' => 'mime16_image_jpeg',
            'jpeg' => 'mime16_image_jpeg',
            'png' => 'mime16_image_png',
            'gif' => 'mime16_image_gif',
            'pdf' => 'mime16_application_pdf',
            'doc' => 'mime16_application_msword',
            'docx' => 'mime16_application_msword',
            'xls' => 'mime16_application_msexcel',
            'xlsx' => 'mime16_application_msexcel',
            'ppt' => 'mime16_application_vnd.ms-powerpoint',
            'pptx' => 'mime16_application_vnd.ms-powerpoint',
            'ps' => 'mime16_application_postscript',
            'zip' => 'mime16_application_zip'
        );
    }

    /**
     * retreive an icon for a given media
     * @param string $img_name
     * @return string 
     */
    public static function get_icon($img_name) {
        $pos = strripos($img_name, '.');

        if ($pos !== false) {
            $ext = strtolower(substr($img_name, $pos + 1));
            $mime = self::build_mime();
            return $GLOBALS['egw']->common->image('etemplate', $mime[$ext]);
        }
        return '';
    }

    /**
     * retreive egroupware next-match-table variables (num_rows and seach) in order to keep current page
     * state after any changement
     * @param array $display  variables to retreive
     * @param string $appname module (application) name
     * @return void
     */
    public static function retreive_display_sessions(&$display, $appname) {
        if (!get_var('home')) { // if no a clic on sidebox menu
            if ($GLOBALS['egw']->session->appsession('search', $appname)) {
                $display['search'] = $GLOBALS['egw']->session->appsession('search', $appname);
            }
        } else {
            $GLOBALS['egw']->session->appsession('search', $appname, '');
            //  $GLOBALS['egw']->session->appsession('num_rows', $this->currentapp, '');
        }
        if ($GLOBALS['egw']->session->appsession('num_rows', $appname)) {
            $display['num_rows'] = $GLOBALS['egw']->session->appsession('num_rows', $appname);
        }
    }

    /**
     * save egroupware next-match-table variables (num_rows and search) in order to keep current page
     * state after any changement
     * @param array $query variable to save
     * @param string $appname module (application) name
     * @return void
     */
    public static function save_display_sessions(&$query, $appname) {
        //save the number of rows to display
        $GLOBALS['egw']->session->appsession('num_rows', $appname, $query['num_rows']);
        //save search value 
        $GLOBALS['egw']->session->appsession('search', $appname, $query['search']);
    }

    /**
     * Return date+time formatted for the currently notified user (prefs in $GLOBALS['egw_info']['user']['preferences'])
     *
     * @param int|string|DateTime $timestamp in server-time
     * @param boolean $do_time=true true=allways (default), false=never print the time, null=print time if != 00:00
     *
     * @return string
     */
    public static function datetime($timestamp, $do_time = true) {
        if (!is_a($timestamp, 'DateTime')) {
            $timestamp = new egw_time($timestamp, egw_time::$server_timezone);
        }
        $timestamp->setTimezone(egw_time::$user_timezone);
        if ($do_time == null) {
            $do_time = ($timestamp->format('Hi') != '0000');
        }
        $format = $GLOBALS['egw_info']['user']['preferences']['common']['dateformat'];
        if ($do_time)
            $format .= ' ' . ($GLOBALS['egw_info']['user']['preferences']['common']['timeformat'] != 12 ? 'H:i' : 'h:i a');

        return $timestamp->format($format);
    }

    /**
     * Send String as stream
     * @param string content to send
     * @param string (optional) mime type to send.
     * @return void
     */
    public static function sendContentAsStream($content, $mimetype = "text/xml") {
        ob_end_clean();
        header("Content-type: " . $mimetype . ";charset=utf-8");
        header("Content-Transfer-Encoding: binary");
        // header("Content-Length: " . count($file)); // comment to solve issue with php-fpm
        //header('Cache-Control: no-store, no-cache, must-revalidate'); 
        //header('Cache-Control: post-check=0, pre-check=0', FALSE);
        header('Pragma: no-cache');
        header('Expires: 0');
        echo $content;
        exit;
    }

    /**
     * send (publish) via  scp (ssh2 using public key) an xml file on a specified server requiring an user connection
     * @param string $connection_type type of transfert
     * @param string $host the hostname
     * @param string $username username as login
     * @param string $pubkeyfile path to public key file
     * @param string $privkeyfile path to private key file
     * @param string $passphrase pass phrase
     * @param array local and remote location for file to transfert: array($local_file => $remote_file)
     * @param string $msg error or success message
     * @return throw an exception on error else true
     */
    public static function scp_pk_transfert($host, $username, $pubkeyfile, $privkeyfile, $passphrase, array $local_remote_file, &$msg) {

        if (!extension_loaded("ssh2"))
            throw new Exception("Extension ssh2 can't be loaded", OPF_CRIT);

        $connect_id = ssh2_connect($host);

        if (!$connect_id)
            throw new Exception('Unable to connect on the specified host !!!', OPF_ERR);

        if (!ssh2_auth_pubkey_file($connect_id, $username, $pubkeyfile, $privkeyfile, $passphrase))
            throw new Exception('private key file or public key file or passphrase is incorrect !!!', OPF_ERR);

        foreach ($local_remote_file as $local_file => $remote_file) {

            if (!file_exists($local_file))
                throw new Exception(lang("file to send doesn't exist on files system :" . $local_file));

            OPF_Logger::logDebug("filename :", $local_remote_file);
            if (!ssh2_scp_send($connect_id, $local_file, $remote_file)) {
                throw new Exception('scp (ssh2_scp_send): ' . lang('Unable to send file') . ' ' . $local_file, OPF_ERR);
                break;
            }
            $msg .= $local_file . ' ' . lang('has been successfuly transfered') . '.<br>';
            OPF_Logger::logDebug("tranfert OK", $msg);
            OPF_Logger::log('Files publication OK ', OPF_NOTICE);
        }
        return true;
    }

    /**
     * send (publish) via  scp (ssh) an xml file on a specified server requiring an user connection
     * @param string $connection_type type of transfert
     * @param string $host the hostname
     * @param string $username username as login
     * @param string $password user password
     * @param array local and remote location for file to transfert: array($local_file => $remote_file)
     * @param string $msg error or success message
     * @return throw an exception on error else true
     */
    public static function scp_transfert($host, $username, $password, array $local_remote_file, &$msg) {

        if (!extension_loaded("ssh2"))
            throw new Exception('Extension ssh2 can be loaded', OPF_CRIT);
        $connect_id = ssh2_connect($host);

        if (!$connect_id)
            throw new Exception('scp (ssh2_connect): ' . lang('Unable to connect on the specified host !!!', OPF_ERR));

        if (!ssh2_auth_password($connect_id, $username, $password))
            throw new Exception('scp (ssh2_auth_password): ' . lang('Username or password is incorrect !!!'), OPF_ERR);

        foreach ($local_remote_file as $local_file => $remote_file) {

            if (!file_exists($local_file))
                throw new Exception(lang("file to send doesn't exist on files system :" . $local_file));
            OPF_Logger::logDebug("filename :", $local_remote_file);
            if (!ssh2_scp_send($connect_id, $local_file, $remote_file)) {
                throw new Exception('scp (ssh2_scp_send): ' . lang('Unable to send file') . ' ' . $local_file, OPF_ERR);
                break;
            }
            $msg .= $local_file . ' ' . lang('has been successfuly transfered') . '.<br>';
            OPF_Logger::logDebug("tranfert OK", $msg);
            OPF_Logger::log('Files publication OK ', OPF_NOTICE);
        }
        return true;
    }

    /**
     * send (publish) via ftp an xml file on a specified server requiring an user connection
     * @param string $host the hostname
     * @param string $username username as login
     * @param string $password user password
     * @param array local and remote location for file to transfert: array($local_file => $remote_file)
     * @param string $msg error or success message
     * @return throw an exception on error else true
     */
    public static function ftp_transfert($host, $username, $password, array $local_remote_file, &$msg) {

        if (!extension_loaded("ftp"))
            throw new Exception("Extension ftp can't be loaded", OPF_CRIT);

        $connect_id = ftp_connect($host);

        if (!$connect_id)
            throw new Exception('ftp (ftp_connect): ' . lang('Unable to connect on the specified host !!!'), OPF_ERR);

        if (!ftp_login($connect_id, $username, $password))
            throw new Exception('ftp (ftp_login): ' . lang('Username or password is incorrect !!!'), OPF_ERR);

        foreach ($local_remote_file as $local_file => $remote_file) {
            if (!file_exists($local_file))
                throw new Exception(lang("file to send doesn't exist on files system :" . $local_file));
            OPF_Logger::logDebug("filename :", $local_remote_file);

            if (!ftp_put($connect_id, $remote_file, $local_file, FTP_BINARY)) {
                throw new Exception('ftp (ftp_put): ' . lang('Unable to send file') . ' ' . $local_file, OPF_ERR);
                break;
            }
            $msg .= $local_file . ' ' . lang('has been successfuly transfered') . '.<br>';

            OPF_Logger::logDebug("tranfert OK", $msg);
            OPF_Logger::log('Files publication OK ', OPF_NOTICE);
        }
        return true;
    }

    /**
     * send  (publish) via copy operation  file from a location to an other
     * @param array local and remote location for file to transfert: array($local_file => $remote_file)
     * @param string $msg error or success message
     * @return an Exception on error else true
     * @throws Exception
     */
    public static function local_transfert(array $local_remote_file, &$msg) {
        foreach ($local_remote_file as $local_file => $remote_file) {
            if (!file_exists($local_file))
                throw new Exception(lang("file to send doesn't exist on files system :" . $local_file));
            OPF_Logger::logDebug("filename :", $local_remote_file);

            if (!copy($local_file, $remote_file)) {
                throw new Exception('local (local_put): ' . lang('Unable to copy file') . ' ' . $local_file, OPF_ERR);
                break;
            }
            $msg .= $local_file . ' ' . lang('has been successfuly transfered') . '.<br>';

            OPF_Logger::logDebug("tranfer OK", $msg);
            OPF_Logger::log('Files publication OK ', OPF_NOTICE);
        }
        return true;
    }

    /**
     * remove accents from a string
     * @param string $str input string
     * @param string $charset input charset
     * @return string 
     */
    static function wd_remove_accents($str, $charset = 'utf-8') {
        $str = htmlentities($str, ENT_NOQUOTES, $charset);
        $str = preg_replace('#&([A-za-z])(?:acute|cedil|circ|grave|orn|ring|slash|th|tilde|uml);#', '$1', $str);
        $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '$1', $str); // for ligatures e.g. '&oelig;'
        $str = preg_replace('#&[^;]+;#', '', $str); // delete other characters 

        return $str;
    }

    /**
     * Parse a xml file to retreive the json content for a given code
     * @param string $xml_doc 
     * @param string $code_to_find the code to check
     * @param string $msg the message to display if the return value is false
     * @return boolean|string false if the code doesn't exist in the xml or if the file is not found, or the json content for the given code
     */
    public static function parse_xml_to_find_json($xml_doc, $code_to_find, &$msg, $context = '', $mimetype = '') {
            $offers = $xml_doc->getElementsByTagName('offer');

            foreach ($offers as $offer) {
                $product = $offer->getElementsByTagName('product');
                $codes = $product->item(0)->getElementsByTagName('codes');
                $codes = $product->item(0)->getElementsByTagName('code');
                $code = '';
                foreach ($codes as $c) {
                    $code .= $c->nodeValue . ',';
                }
                if ($code_to_find . ',' == $code) {
                    $links = $offer->getElementsByTagName('links');
                    $jsonTagEl = $links->item(0)->getElementsByTagName('json');
                    $jsonTagNb = $jsonTagEl->length;
                    if (!isset($context) || $context == '' || !isset($mimetype) || $mimetype == '') {
                        $json = $jsonTagEl->item(0)->nodeValue;
                        OPF_Logger::logDebug("get json from xml OK", $json);
                        OPF_Logger::log($json, OPF_NOTICE);
                        return $json;
                    } else {
                        for ($i = 1; $i < $jsonTagNb; $i++) {
                            $json = $jsonTagEl->item($i)->nodeValue;
                            $node = $jsonTagEl->item($i);
                            $atts = $node->attributes;
                            $nodeAtt1 = $atts->getNamedItem('mimetype');
                            $nodeAtt2 = $atts->getNamedItem('context');
                            if (isset($nodeAtt1) && $nodeAtt1->nodeValue == $mimetype && isset($nodeAtt2) && $nodeAtt2->nodeValue == $context) {
                                return $json;
                            }
                        }
                    }

                    return $json;
                }
            }
            $msg = 'No matching code found';
            OPF_Logger::logDebug("get json from xml KO", $msg);
            OPF_Logger::log($msg, OPF_WARNING);
            return false;
    }

    /**
     * Recursive Array Search in order to return all finding keys in an array 
     * usage : recursiveArraySearchAll($your-array,'your-needle-to match','your-array-index-to-search')
     * @param array $haystack the array
     * @param unknown_type $needle the needle to match
     * @param unknown_type the index to match
     * @return array
     */
    public static function recursiveArraySearchAll(array $haystack, $needle, $index = null) {
        $aIt = new RecursiveArrayIterator($haystack);
        $it = new RecursiveIteratorIterator($aIt);
        $resultkeys = array();

        while ($it->valid()) {
            if (((isset($index) AND ($it->key() == $index)) OR (!isset($index))) AND (strpos($it->current(), $needle) !== false)) { //$it->current() == $needle 
                $resultkeys[] = $aIt->key(); //return $aIt->key(); 
            }
            $it->next();
        }
        return $resultkeys;  // return all finding in an array 
    }

    /**
     * Validate an xml file with an xsd file
     * @param string $xmlFile xml file to validate
     * @param string $xsdFile xsd file to use for validation
     * @return boolean true on success
     * @throws Exception on error
     */
    public static function validateXmlFileWithXsd($xmlFile, $xsdFile){
        $dom = new DOMDocument('1.0', 'utf-8');
        if(!file_exists($xmlFile)){
            throw new Exception(lang("File not found: ") . $xmlFile, OPF_ERR);
        }
        if(!file_exists($xsdFile)){
            throw new Exception(lang("File not found: ") . $xsdFile, OPF_ERR);
        }
        $dom->load($xmlFile);
        $valid = $dom->schemaValidate($xsdFile);
        $schema_errors = libxml_get_errors();
        if (!$valid) {
            $exception_message = "Error during schema validation :\n ";
            foreach ($schema_errors as $ErrorObj) {
                $exception_message.=$ErrorObj->message;
            }
            throw new Exception($exception_message);
        }
        return true;
    }
    /**
     * Validate an xml string with an xsd file
     * @param string $xmlString xml string to validate
     * @param string $xsdFile xsd file to use for validation
     * @return boolean true on success
     * @throws Exception on error
     */
    public static function validateXmlStringWithXsd($xmlString, $xsdFile){
        $dom = new DOMDocument('1.0', 'utf-8');
        if(!file_exists($xsdFile)){
            throw new Exception(lang("File not found: ") . $xsdFile, OPF_ERR);
        }
        $dom->loadXML($xmlString);
        $valid = $dom->schemaValidate($xsdFile);
        $schema_errors = libxml_get_errors();
        if (!$valid) {
            $exception_message = "Error during schema validation :\n ";
            foreach ($schema_errors as $ErrorObj) {
                $exception_message.=$ErrorObj->message;
            }
            throw new Exception($exception_message);
        }
        return true;
    }
}

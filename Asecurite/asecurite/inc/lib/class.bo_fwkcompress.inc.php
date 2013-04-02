<?php

/**
 * <b>File class.bo_fwkcompress.inc.php</b>
 * Compression library for eGroupware applications
 * @author N'faly KABA
 * @author Stephan Acquatella
 * @since   21/11/2011
 * @version 1.0
 * @copyright France Telecom
 * @package egw-enp-framework
 * @subpackage 
 * @filesource  class.o_fwkcompress.inc.php
 */
class bo_fwkcompress {

    /**
     * Constructor
     * Static Class, can't be instancied
     */
    private function __construct() {
        
    }

    /**
     * <b>pns_gzdecode</b> decode string encoded with gzencode
     * Note: * gzencode() only adds a  10 byte header. gzdecode will be available in PHP6. Based on code provided on php.net
     * @param string string to uncompress
     * @return string uncompressed string compressed with gzencode.
     */
    public static function pns_gzdecode($string = "") {
        $string = substr($string, 10);
        return gzinflate($string);
    }

    /**
     * <b>uncompress_b64gzip</b> return the clear value of compressed base64 string 
     *  @param string gzip base 64 string to uncompress  
     *  @return return the clear value of compressed base64 string 
     */
    public static function uncompress_b64gzip($compressedstr) {
        $_return_string = $compressedstr;
        if (self::checkBase64Encoded($compressedstr)) {
            $_return_string_bin = base64_decode($compressedstr, true);
            $_return_string = self::pns_gzdecode($_return_string_bin);
        }
        return $_return_string;
    }

    /**
     * Check a string of base64 encoded data to make sure it has actually
     * been encoded.
     *
     * @param $encodedString string Base64 encoded string to validate.
     * @return Boolean Returns true when the given string only contains
     * base64 characters; returns false if there is even one non-base64 character.
     */
  static function checkBase64Encoded($encodedString) {
        $length = strlen($encodedString);

        // Check every character.
        for ($i = 0; $i < $length; ++$i) {
            $c = $encodedString[$i];
            if (
                    ($c < '0' || $c > '9')
                    && ($c < 'a' || $c > 'z')
                    && ($c < 'A' || $c > 'Z')
                    && ($c != '+')
                    && ($c != '/')
                    && ($c != '=')
            ) {
                // Bad character found.
                return false;
            }
        }
        // Only good characters found.
        return true;
    }

    /**
     * <b>compress_b64gzip</b> return string as compressed base64 string 
     *  @param string string to compress  
     *  @return return compressed base64 string 
     */
    public static function compress_b64gzip($strtocompress) {        
        $_return_string_bin = gzencode($strtocompress);
        $_return_string = base64_encode($_return_string_bin);
        return $_return_string;
    }

}
<?php

/**
 * <b>File class.bo_agent.inc.php</b>
 * asecurite's business-object
 * @author N'faly KABA
 * @since   2/08/2011
 * @version 1.0
 * @copyright KABANFALY
 * @package egroupware
 * @subpackage asecurite/inc/
 * @filesource  class.bo_agent.inc.php
 */

include_once(EGW_INCLUDE_ROOT . '/asecurite/inc/class.bo_asecurite.inc.php');

class bo_agent extends bo_asecurite {

   function __construct() {
        parent::__construct('egw_asecurite_agent');
    }
}
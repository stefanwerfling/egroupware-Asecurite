<?php

/**
 * <b>File class.bo_horaire_ville.inc.php</b>
 * asecurite's agent planning business-object
 * @author N'faly KABA
 * @since   26/08/2011
 * @version 2.0
 * @copyright KABANFALY
 * @package egroupware
 * @subpackage asecurite/inc/
 * @filesource  class.bo_horaire_ville.inc.php
 */
include_once(EGW_INCLUDE_ROOT . '/asecurite/inc/class.bo_asecurite.inc.php');

class bo_horaires_ville extends bo_asecurite {

    function __construct() {

        parent::__construct('egw_asecurite_horaires_agent');

    }
}
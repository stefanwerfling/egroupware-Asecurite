<?php

/**
 * <b>File class.bo_ville.inc.php</b>
 * asecurite's ville business-object
 * @author N'faly KABA
 * @since   26/08/2011
 * @version 1.0
 * @copyright KABANFALY
 * @package egroupware
 * @subpackage asecurite/inc/
 * @filesource  class.bo_ville.inc.php
 */
include_once(EGW_INCLUDE_ROOT . '/asecurite/inc/class.bo_asecurite.inc.php');

class bo_ville extends bo_asecurite {

    function __construct() {
        parent::__construct('egw_asecurite_ville');
    }

    /**
     * delete a city
     * @param int $id_ville city id
     * @throws Exception on error
     */
    public function delete_ville($id_ville) {
        $this->setup_table(APP_NAME, 'egw_asecurite_ville');
        if (!$this->delete(array('idasecurite_ville' => $id_ville))) {
            throw new Exception(lang('Enable to delete the city'));
        }
        $this->setup_table(APP_NAME, 'egw_asecurite_site');
        if (!$this->delete(array('idasecurite_ville' => $id_ville))) {
            throw new Exception(lang("Enable to delete sites of the city"));
        }
        $this->setup_table(APP_NAME, 'egw_asecurite_agent');
        if (!$this->delete(array('idasecurite_ville' => $id_ville))) {
            throw new Exception(lang("Enable to delete agents of the city"));
        }
        $this->setup_table(APP_NAME, 'egw_asecurite_horaires_agent');
        if (!$this->delete(array('idasecurite_ville' => $id_ville))) {
            throw new Exception(lang("Enable to delete plannings of the city"));
        }
    }

}
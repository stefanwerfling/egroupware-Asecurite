<?php

/**
 * <b>File class.bo_site.inc.php</b>
 * asecurite's site business-object
 * @author N'faly KABA
 * @since   6/08/2011
 * @version 1.0
 * @copyright KABANFALY
 * @package egroupware
 * @subpackage asecurite/inc/
 * @filesource  class.bo_site.inc.php
 */
include_once(EGW_INCLUDE_ROOT . '/asecurite/inc/class.bo_asecurite.inc.php');

class bo_site extends bo_asecurite {

    function __construct() {
        parent::__construct('egw_asecurite_site');
    }

    /**
     * delete a site
     * @param int $id_agent agent id
     * @throws Exception on error
     */
    public function delete_site($id_site) {
        $this->setup_table(APP_NAME, 'egw_asecurite_site');
        if (!$this->delete(array('idasecurite_site' => $id_site))) {
            throw new Exception(lang('Enable to delete site'));
        }
        $this->setup_table(APP_NAME, 'egw_asecurite_horaires_agent');
        if ($this->search(array('idasecurite_site' => $id_site))) {
            if (!$this->delete(array('idasecurite_site' => $id_site))) {
                throw new Exception(lang("Enable to delete plannings of the site"));
            }
        }
    }

}
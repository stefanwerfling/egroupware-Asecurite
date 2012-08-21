<?php

/**
 * <b>File class.bo_ferie.inc.php</b>
 * asecurite's ferie business-object
 * @author N'faly KABA
 * @since   31/08/2011
 * @version 2.0
 * @copyright KABANFALY
 * @package egroupware
 * @subpackage asecurite/inc/
 * @filesource  class.bo_ferie.inc.php
 */

include_once(EGW_INCLUDE_ROOT . '/asecurite/inc/class.bo_asecurite.inc.php');

class bo_ferie extends bo_asecurite {

    function __construct() {
        parent::__construct('egw_asecurite_ferie');
    }
    
    /**
     * delete a agent
     * @param int $id_agent agent id
     * @throws Exception on error
     */
    public function delete_jour_ferie($id_ferie) {
        $this->setup_table(APP_NAME, 'egw_asecurite_ferie');

        if (!$this->delete(array('idasecurite_ferie' => $id_ferie))) {
            throw new Exception(lang('Enable to delete this day'));
        }
        return true;
    }
   
}
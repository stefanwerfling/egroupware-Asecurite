<?php

/**
 * <b>File class.bo_horaires_agent.inc.php</b>
 * asecurite's agent planning business-object
 * @author N'faly KABA
 * @since   6/08/2011
 * @version 1.0
 * @copyright KABANFALY
 * @package egroupware
 * @subpackage asecurite/inc/
 * @filesource  class.bo_horaires_agent.inc.php
 */
include_once(EGW_INCLUDE_ROOT . '/asecurite/inc/class.bo_asecurite.inc.php');

class bo_horaires_agent extends bo_asecurite {

    

    function __construct() {

        parent::__construct('egw_asecurite_horaires_agent');

    }
/*
    function edit(&$content) {

        $table_name = 'egw_asecurite_horaires_agent';

        $name = 'Agent schedule';

        $col = array('heure_arrivee', 'heure_depart', 'pause', 'heures_jour', 'heures_nuit', 'idasecurite_agent', 'idasecurite_site');

        $extra_param = array('menuaction' => APP_NAME . '.ui_horaires_agent.index');

        $id = get_var('id');

        $save_ok = false;

        $no_button['add'] = false;
        $no_button['edit'] = false;

        if ($id != '') {

            $content['mois'] = $this->current_month;
            $content['annee'] = $this->current_year;
            $GLOBALS['egw']->session->appsession('idasecurite_agent', APP_NAME, $id);
        }

        $this->setup_table(APP_NAME, 'egw_asecurite_agent');

        $f_agent = $this->search(array('idasecurite_agent' => $GLOBALS['egw']->session->appsession('idasecurite_agent', APP_NAME)), false);
        if (count($f_agent) == 1) {
            $content['agent'] = $f_agent[0]['nom'] . ' ' . $f_agent[0]['prenom'];
        }
        $save = get_var('save', array('GET'));

        if (isset($content['add_horaire'])) {


            $compute = $this->compute_hour((int) $content['heure_arrivee'], (int) $content['heure_depart']);


            $content['heures_jour'] = $compute['day'];

            $content['heures_nuit'] = $compute['night'];
          

            $content['idasecurite_agent'] = $GLOBALS['egw']->session->appsession('idasecurite_agent', APP_NAME);

            $save_ok = $this->save_data($name, $table_name, $content, $col, $msg);



            $ave = $save_ok ? 'success' : 'error';

            if ($save_ok) {

                $content['add_edit'] = '<span class="title">' . $content['title'] . '<span> - <span id="edit">Modifier</span>';

                $content['msg_horaire'] = "<span id='success'>" . lang($msg) . " </span>";

                $no_button['add'] = true;
                $no_button['edit'] = false;
            } else {
                $content['msg_horaire'] = "<span id='error'>" . lang($msg) . " </span>";
            }
        } elseif (isset($content['close'])) {

            self::close_popup($extra_param);
        }

        $this->setup_table(APP_NAME, $table_name);
    }
*/
}
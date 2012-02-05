<?php

/**
 * <b>File class.ui_planning_global.inc.php</b>
 * asecurite's global planning user interface
 * @author N'faly KABA
 * @since   11/08/2011
 * @version 1.0
 * @copyright KABANFALY
 * @package egroupware
 * @subpackage asecurite/inc/
 * @filesource  class.ui_planning_global.inc.php
 */
include_once(EGW_INCLUDE_ROOT . '/asecurite/inc/class.bo_planning_global.inc.php');

class ui_planning_global extends bo_planning_global {

    /**
     * Public functions callable via menuaction
     *
     * @var array
     */
    var $public_functions = array(
        'index' => True,
        'change_planning' => True,
    );

    function __construct() {

        parent::__construct();
        $this->init_template(lang('Planning global'));
    }

    /**
     * Display the application home content
     */
    public function index($content = NULL) {

        if (isset($content['nm']['delete'])) {
            list($del) = each($content['nm']['delete']);

            $this->delete(array('idasecurite_horaires_agent' => $del));
        } elseif (isset($content['delete_selected'])) {

            for ($i = 0; $i < count($content['nm']['checkbox']); $i++) {

                $this->delete(array('idasecurite_horaires_agent' => $content['nm']['checkbox'][$i]));
            }
        } elseif (isset($content['print'])) {

            $link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_imprime.print_planning_global'));
            $this->js_content .= '<script type="text/javascript">
                                open_popup(\'' . $link . '\', 800,700) ;
                            </script>';

            $GLOBALS['egw']->session->appsession('current_month', APP_NAME, $content['mois']);
            $GLOBALS['egw']->session->appsession('current_year', APP_NAME, $content['annee']);
            $GLOBALS['egw']->session->appsession('current_agent', APP_NAME, $content['idasecurite_agent']);
            $GLOBALS['egw']->session->appsession('current_ville', APP_NAME, $content['idasecurite_ville']);
            $GLOBALS['egw']->session->appsession('current_site', APP_NAME, $content['idasecurite_site']);
            $GLOBALS['egw']->session->appsession('planning_to_print', APP_NAME, $GLOBALS['egw']->session->appsession('all_planning_global', APP_NAME));
        }

        $this->update_lists($content['idasecurite_ville']);

        $select_option = array(
            'idasecurite_site' => $this->sites,
            'idasecurite_agent' => $this->agents,
            'idasecurite_ville' => $this->cities,
            'mois' => $this->monthes,
            'annee' => $this->years,
        );
        parent::edit_planning($content, 'egw_asecurite_horaires_agent', 'Global planning', array('heure_arrivee', 'heure_depart', 'pause', 'heures_jour', 'heures_nuit', 'idasecurite_agent', 'idasecurite_site', 'idasecurite_ville', 'heures_jour_dimanche', 'heures_nuit_dimanche'), array('menuaction' => APP_NAME . '.ui_planning_global.index'));

        if (!$this->sites) {
            $content['msg_horaire'] = "<span id='error' style='font-weight:bold'>" . lang('Threre is no site !') . " </span>";
        }
        if (!$this->agents) {
            $content['msg_horaire'] = "<span id='error' style='font-weight:bold'>" . lang('Threre is no agent !') . " </span>";
        }

        $GLOBALS['egw']->session->appsession('all_planning_global', APP_NAME, $this->get_mensual_planning($content['mois'], $content['annee'], $content['idasecurite_agent'], $content['idasecurite_site'], $content['idasecurite_ville']));

        $content['stat'] = '<div class="stat">' . $this->draw_stat($GLOBALS['egw']->session->appsession('all_planning_global', APP_NAME)) . '</div>';

        $content['nm'] = $this->get_rows();
        
        $content['paniers'] = $this->nb_baskets;

        $this->tmpl->read(APP_NAME . '.planning'); //APP_NAME defined in asecurite/inc/class.bo_asecurite.inc.php

        $this->tmpl->exec(APP_NAME . '.ui_planning_global.index', $content, $select_option, $readonlys, '');

        $this->create_footer();
    }

    /**
     * query rows for the nextmatch widget
     *
     * @param array $query with keys 'start', 'search', 'order', 'sort', 'col_filter'
     * 	For other keys like 'filter', 'cat_id' you have to reimplement this method in a derived class.
     * @param array &$rows returned rows/competitions
     * @param array &$readonlys eg. to disable buttons based on acl, not use here, maybe in a derived class
     * @return int total number of rows
     */
    public function get_rows() {

        $rows = $GLOBALS['egw']->session->appsession('all_planning_global', APP_NAME);
        foreach ($rows as $i => &$row) {

            $this->setup_table(APP_NAME, 'egw_asecurite_site');
            if ($row['idasecurite_site'] != '') {
                $f_site_name = $this->search(array('idasecurite_site' => $row['idasecurite_site']), false);
                if (count($f_site_name) == 1) {
                    $row['site'] = $f_site_name[0]['nom'];
                }
            }
            $this->setup_table(APP_NAME, 'egw_asecurite_agent');
            if ($row['idasecurite_agent'] != '') {
                $f_agent = $this->search(array('idasecurite_agent' => $row['idasecurite_agent']), false);
                if (count($f_agent) == 1) {
                    $row['agent'] = $f_agent[0]['nom'] . ' ' . $f_agent[0]['prenom'];
                }
            }
            $this->setup_table(APP_NAME, 'egw_asecurite_ville');
            if ($row['idasecurite_agent'] != '') {
                $f_ville = $this->search(array('idasecurite_ville' => $row['idasecurite_ville']), false);
                if (count($f_ville) == 1) {
                    $row['ville'] = $f_ville[0]['nom'];
                }
            }
            /* $this->setup_table(APP_NAME, 'egw_asecurite_horaires_agent');
              $row['heures_jour'] = (int)$row['heures_jour'] - (int)$row['heures_jour_dimanche'];
              $row['heures_nuit'] = (int)$row['heures_nuit'] - (int)$row['heures_nuit_dimanche'];
              $this->update(array('heures_jour' => $row['heures_jour'], 'heures_nuit' => $row['heures_nuit']), array('idasecurite_horaires_agent' => $row['idasecurite_horaires_agent'])); */
            $this->manage_display($row);
        }
        $this->setup_table(APP_NAME, 'egw_asecurite_horaires_agent');
        @array_unshift($rows, false);
        return $rows;
    }

    /**
     * Give an agent planning to another one for a choosen month
     * @param array $content contains processing data
     * @return void
     */
    function change_planning($content = NULL) {

        if (isset($content['submit'])) {

            $nb_update = $this->give_planning($content['mois'], $content['annee'], $content['agent_from'], $content['agent_to'], $content['idasecurite_ville'], $content['idasecurite_site']);
            if ($nb_update != 0) {
                $content['msg'] = '<span id="success">' . lang('Attribution OK') . '</span>';
            } else {
                $content['msg'] = '<span id="success">' . lang("Il n'y a aucun planning à attribuer pour le mois choisi.") . '</span>';
            }
        }
        $this->update_lists($content['idasecurite_ville']);
        $select_option = array(
            'idasecurite_site' => $this->sites,
            'agent_from' => $this->agents,
            'agent_to' => $this->agents,
            'idasecurite_ville' => $this->cities,
            'mois' => $this->monthes,
            'annee' => $this->years,
        );
        $current = get_var('current');
        if ($current) {
            $content['mois'] = $this->current_month;
            $content['annee'] = $this->current_year;
        }
        $this->tmpl->read(APP_NAME . '.planning.copy'); //APP_NAME defined in asecurite/inc/class.bo_asecurite.inc.php

        $this->tmpl->exec(APP_NAME . '.ui_planning_global.change_planning', $content, $select_option, $readonlys, '');
        $this->create_footer();
    }

}
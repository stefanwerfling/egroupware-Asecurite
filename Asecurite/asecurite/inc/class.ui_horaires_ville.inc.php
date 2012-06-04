<?php

/**
 * <b>File class.ui_horaires_ville.inc.php</b>
 * asecurite's agent planning user-interface
 * @author N'faly KABA
 * @since   25/08/2011
 * @version 1.0
 * @copyright KABANFALY
 * @package egroupware
 * @subpackage asecurite/inc/
 * @filesource  class.ui_horaires_ville.inc.php
 */
include_once(EGW_INCLUDE_ROOT . '/asecurite/inc/class.bo_horaires_ville.inc.php');

class ui_horaires_ville extends bo_horaires_ville {

    /**
     * Public functions callable via menuaction
     *
     * @var array
     */
    var $public_functions = array(
        'index' => True,
    );

    function __construct() {

        parent::__construct();
        $this->init_template(lang('Gestion des horaires'));
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
            $GLOBALS['egw']->session->appsession('current_ville', APP_NAME, $GLOBALS['egw']->session->appsession('idasecurite_ville', APP_NAME));
            $GLOBALS['egw']->session->appsession('current_site', APP_NAME, $content['idasecurite_site']);
            $GLOBALS['egw']->session->appsession('planning_to_print', APP_NAME, $GLOBALS['egw']->session->appsession('all_planning_city', APP_NAME));
        }

        $id = get_var('id');
        if ($id != '') {
            $GLOBALS['egw']->session->appsession('idasecurite_ville', APP_NAME, $id);
        }
        $this->setup_table(APP_NAME, 'egw_asecurite_ville');

        $f_city = $this->search(array('idasecurite_ville' => $GLOBALS['egw']->session->appsession('idasecurite_ville', APP_NAME)), false);
        if (count($f_city) == 1) {
            $content['ville'] = $f_city[0]['nom'];
        }
        $content['idasecurite_ville'] = $GLOBALS['egw']->session->appsession('idasecurite_ville', APP_NAME);

        $this->update_lists($content['idasecurite_ville']);

        $select_option = array(
            'idasecurite_site' => $this->sites,
            'idasecurite_agent' => $this->agents,
            'mois' => $this->monthes,
            'annee' => $this->years,
        );

        parent::edit_planning($content, 'egw_asecurite_horaires_agent', 'City schedule', array('heure_arrivee', 'heure_depart', 'pause', 'heures_jour', 'heures_nuit', 'idasecurite_agent', 'idasecurite_site', 'idasecurite_ville', 'heures_jour_dimanche', 'heures_nuit_dimanche'), array('menuaction' => APP_NAME . '.ui_horaire_ville.index'));

        if (!$this->agents) {
            $content['msg_horaire'] = "<span id='error' style='font-weight:bold'>" . lang('Threre is no agent !') . " </span>";
        }
        $GLOBALS['egw']->session->appsession('all_planning_city', APP_NAME, $this->get_mensual_planning($content['mois'], $content['annee'], $content['idasecurite_agent'], $content['idasecurite_site'], $content['idasecurite_ville']));

        $content['stat'] = '<div class="stat">' . $this->draw_stat($GLOBALS['egw']->session->appsession('all_planning_city', APP_NAME)) . '</div>';

        //$content['nm'] = $this->get_rows();
        $content['nm'] = $this->nm + array('get_rows' => 'asecurite.ui_horaires_ville.get_rows', 'order' => 'heure_arrivee');
        $content['paniers'] = $this->nb_baskets;
        $this->tmpl->read(APP_NAME . '.ville.planning'); //APP_NAME defined in asecurite/inc/class.bo_asecurite.inc.php
        
        $this->tmpl->exec(APP_NAME . '.ui_horaires_ville.index', $content, $select_option, $readonlys, '', 2);
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
    public function get_rows($query, &$rows, &$readonlys) {
        $this->setup_table(APP_NAME, 'egw_asecurite_horaires_agent');
        /*$rows = $GLOBALS['egw']->session->appsession('all_planning_city', APP_NAME);
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

            $this->manage_display($row);
        }

        $this->setup_table(APP_NAME, 'egw_asecurite_horaires_agent');

        @array_unshift($rows, false);
        return $rows;*/
        //$query['colf']
        $total = parent::get_rows($query, $rows, $readonlys);
        //$rows = $GLOBALS['egw']->session->appsession('all_planning_city', APP_NAME);
        
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

            $this->manage_display($row);
        }
        return $total;
        
        
    }

}
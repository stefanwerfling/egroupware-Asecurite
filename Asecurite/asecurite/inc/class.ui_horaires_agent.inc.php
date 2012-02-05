<?php

/**
 * <b>File class.ui_horaires_agent.inc.php</b>
 * asecurite's agent planning user-interface
 * @author N'faly KABA
 * @since   11/08/2011
 * @version 1.0
 * @copyright KABANFALY
 * @package egroupware
 * @subpackage asecurite/inc/
 * @filesource  class.ui_horaires_agent.inc.php
 */
include_once(EGW_INCLUDE_ROOT . '/asecurite/inc/class.bo_horaires_agent.inc.php');

class ui_horaires_agent extends bo_horaires_agent {

    /**
     * Public functions callable via menuaction
     *
     * @var array
     */
    var $public_functions = array(
        'index' => True,
        'redirect_to_edit' => True,
        'edit' => True,
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
            $GLOBALS['egw']->session->appsession('current_agent', APP_NAME, $GLOBALS['egw']->session->appsession('idasecurite_agent', APP_NAME));
            $GLOBALS['egw']->session->appsession('current_ville', APP_NAME, $GLOBALS['egw']->session->appsession('idasecurite_ville', APP_NAME));
            $GLOBALS['egw']->session->appsession('current_site', APP_NAME, $content['idasecurite_site']);
            $GLOBALS['egw']->session->appsession('planning_to_print', APP_NAME, $GLOBALS['egw']->session->appsession('all_planning_agent', APP_NAME));
        }

        $id = get_var('id');
        if ($id != '') {
            $GLOBALS['egw']->session->appsession('idasecurite_agent', APP_NAME, $id);
        }
        $this->setup_table(APP_NAME, 'egw_asecurite_agent');

        $f_agent = $this->search(array('idasecurite_agent' => $GLOBALS['egw']->session->appsession('idasecurite_agent', APP_NAME)), false);
        if (count($f_agent) == 1) {
            $content['agent'] = $f_agent[0]['nom'] . ' ' . $f_agent[0]['prenom'];
            $content['idasecurite_ville'] = $f_agent[0]['idasecurite_ville'];
            $GLOBALS['egw']->session->appsession('idasecurite_ville', APP_NAME, $content['idasecurite_ville']);
        }
        $content['idasecurite_agent'] = $GLOBALS['egw']->session->appsession('idasecurite_agent', APP_NAME);

        $this->update_lists($content['idasecurite_ville']);
        $select_option = array(
            'idasecurite_site' => $this->sites,
            'mois' => $this->monthes,
            'annee' => $this->years,
        );

        parent::edit_planning($content, 'egw_asecurite_horaires_agent', 'Agent schedule', array('heure_arrivee', 'heure_depart', 'pause', 'heures_jour', 'heures_nuit', 'idasecurite_agent', 'idasecurite_site', 'idasecurite_ville', 'heures_jour_dimanche', 'heures_nuit_dimanche'), array('menuaction' => APP_NAME . '.ui_horaires_agent.index'));

        if (!$this->sites) {
            $content['msg_horaire'] = "<span id='error' style='font-weight:bold'>" . lang('Threre is no site !') . " </span>";
        }
        $GLOBALS['egw']->session->appsession('all_planning_agent', APP_NAME, $this->get_mensual_planning($content['mois'], $content['annee'], $GLOBALS['egw']->session->appsession('idasecurite_agent', APP_NAME), $content['idasecurite_site'], $content['idasecurite_ville']));

        $content['stat'] = '<div class="stat">' . $this->draw_stat($GLOBALS['egw']->session->appsession('all_planning_agent', APP_NAME)) . '</div>';

        $content['nm'] = $this->get_rows();
        $content['paniers'] = $this->nb_baskets;
        $this->tmpl->read(APP_NAME . '.agent.planning'); //APP_NAME defined in asecurite/inc/class.bo_asecurite.inc.php

        $this->tmpl->exec(APP_NAME . '.ui_horaires_agent.index', $content, $select_option, $readonlys, '', 2);

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

        $rows = $GLOBALS['egw']->session->appsession('all_planning_agent', APP_NAME);


        foreach ($rows as $i => &$row) {

            $this->setup_table(APP_NAME, 'egw_asecurite_site');
            if ($row['idasecurite_site'] != '') {
                $f_site_name = $this->search(array('idasecurite_site' => $row['idasecurite_site']), false);

                if (count($f_site_name) == 1) {
                    $row['site'] = $f_site_name[0]['nom'];
                }
                $this->setup_table(APP_NAME, 'egw_asecurite_ville');
                if ($row['idasecurite_agent'] != '') {
                    $f_ville = $this->search(array('idasecurite_ville' => $row['idasecurite_ville']), false);
                    if (count($f_ville) == 1) {
                        $row['ville'] = $f_ville[0]['nom'];
                    }
                }
               $this->manage_display($row);
            }
        }

        $this->setup_table(APP_NAME, 'egw_asecurite_horaires_agent');

        @array_unshift($rows, false);
        return $rows;
    }

    /**
     * redirect to edit page and unset session value in order to do add operation
     * @return void
     */
    function redirect_to_edit() {

        parent::redirect_to_edit('idasecurite_site', array('menuaction' => APP_NAME . '.ui_site.edit'));
    }

    /**
     * add or edit an site
     * @param int $content contains processing data
     * @return void
     */
    public function edit($content = NULL) {


        parent::edit($content, $no_button, 'idasecurite_site', 'site', 'egw_asecurite_site', array('nom', 'prenom', 'adresse', 'code_postal', 'ville', 'telephone'), array('menuaction' => APP_NAME . '.ui_site.index'));

        $this->tmpl->read(APP_NAME . '.site.edit');
        $this->tmpl->exec(APP_NAME . '.ui_site.edit', $content, $sel_options, $no_button, '', 2);
    }

}
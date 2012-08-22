<?php

/**
 * <b>File class.ui_planning_global.inc.php</b>
 * asecurite's global planning user interface
 * @author N'faly KABA
 * @since   11/08/2011
 * @version 2.0
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
        'delete_planning' => True,
        'get_data' => True,
        'change_planning' => True
    );

    function __construct() {

        parent::__construct();
        $this->init_template(lang('Planning global'));
        $this->init_template(lang('Gestion des horaires'));
        $this->current_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_planning_global.index',
            'id' => $GLOBALS['egw']->session->appsession('idasecurite_agent', APP_NAME),
            'current' => 'true',
            'month' => $GLOBALS['egw']->session->appsession('current_month', APP_NAME),
            'year' => $GLOBALS['egw']->session->appsession('current_year', APP_NAME),
            'ville' => $GLOBALS['egw']->session->appsession('current_ville', APP_NAME),
            'site' => $GLOBALS['egw']->session->appsession('current_site', APP_NAME),
            'agent' => $GLOBALS['egw']->session->appsession('current_agent', APP_NAME)));
    }

    /**
     * Display the application home content
     */
    public function index($content = NULL) {

        if (get_var('month')) {
            $this->current_month = get_var('month');
            $this->current_year = get_var('year');
            $content['idasecurite_site'] = get_var('site');
            $content['idasecurite_ville'] = get_var('ville');
            $content['idasecurite_agent'] = get_var('agent');
        }

        if (isset($content['nm']['delete'])) {
            list($del) = each($content['nm']['delete']);

            $this->delete(array('idasecurite_horaires_agent' => $del));
        } elseif (isset($content['delete_selected'])) {

            for ($i = 0; $i < count($content['nm']['checkbox']); $i++) {

                $this->delete(array('idasecurite_horaires_agent' => $content['nm']['checkbox'][$i]));
            }
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
        $msg = get_var('msg', array('GET'));
        $save = get_var('save', array('GET'));
        $this->compute_paniers($GLOBALS['egw']->session->appsession('all_planning_global', APP_NAME));
        $data_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_planning_global.get_data'));
        $delete_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_horaires_ville.delete_planning'));
        $tpl_content = file_get_contents(EGW_INCLUDE_ROOT . '/' . APP_NAME . '/templates/default/all_planning.html');
        $tpl_content = str_replace('DATA_LINK', $data_link, $tpl_content);
        $tpl_content = str_replace('MSG', "<span id=\"$save\">" . lang($msg) . " </span>", $tpl_content);
        $tpl_content = str_replace('DELETE_LINK', $delete_link, $tpl_content);
        $tpl_content = str_replace('INDEX_LINK', $this->current_link, $tpl_content);
        $tpl_content = str_replace('DELETE_BUTTON', $this->html->image(APP_NAME, 'delete', lang('Supprimer les plannings sélectionnés?')), $tpl_content);
        $tpl_content = str_replace('SELECT_ALL', $this->html->image(APP_NAME, 'arrow_ltr', lang('Tout cocher/décocher'), 'onclick="check_all(); return false;"'), $tpl_content);
        $content['data'] = $tpl_content;
        $content['paniers'] = $this->nb_baskets;

        $GLOBALS['egw']->session->appsession('current_month', APP_NAME, $content['mois']);
        $GLOBALS['egw']->session->appsession('current_year', APP_NAME, $content['annee']);
        $GLOBALS['egw']->session->appsession('current_agent', APP_NAME, $content['idasecurite_agent']);
        $GLOBALS['egw']->session->appsession('current_ville', APP_NAME, $content['idasecurite_ville']);
        $GLOBALS['egw']->session->appsession('current_site', APP_NAME, $content['idasecurite_site']);
        $GLOBALS['egw']->session->appsession('planning_to_print', APP_NAME, $GLOBALS['egw']->session->appsession('all_planning_global', APP_NAME));

        $this->tmpl->read(APP_NAME . '.planning'); //APP_NAME defined in asecurite/inc/class.bo_asecurite.inc.php
        $this->tmpl->exec(APP_NAME . '.ui_planning_global.index', $content, $select_option, $readonlys, '');
        $this->create_footer();
    }
    /**
     * get all planning for site
     */
    public function get_data() {
        $rows = $GLOBALS['egw']->session->appsession('all_planning_global', APP_NAME);
        $output = array(
            "sEcho" => intval($_GET['sEcho']),
            "iTotalRecords" => count($rows),
            "iTotalDisplayRecords" => count($rows),
            "aaData" => array()
        );

        if ($rows) {
            foreach ($rows as &$row) {

                $this->setup_table(APP_NAME, 'egw_asecurite_site');
                if ($row['idasecurite_site'] != '') {
                    $f_site_name = $this->search(array('idasecurite_site' => $row['idasecurite_site']), false);
                    if (count($f_site_name) == 1) {
                        $planning_site_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_horaires_site.index', 'id' => $row['idasecurite_site'], 'current' => 'true'));
                        $row['site'] = '<span style="cursor:pointer; color:blue;" onclick="egw_openWindowCentered2(\'' . $planning_site_link . '\', \'_blank\', 1000, 700, \'yes\'); return false;">' . $f_site_name[0]['nom'] . '</span>';
                    }
                }
                $this->setup_table(APP_NAME, 'egw_asecurite_agent');
                if ($row['idasecurite_agent'] != '') {
                    $f_agent = $this->search(array('idasecurite_agent' => $row['idasecurite_agent']), false);
                    if (count($f_agent) == 1) {
                        $planning_agent_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_horaires_agent.index', 'id' => $row['idasecurite_agent'], 'current' => 'true'));
                        $row['agent'] = '<span style="cursor:pointer; color:blue;" onclick="egw_openWindowCentered2(\'' . $planning_agent_link . '\', \'_blank\', 1000, 700, \'yes\'); return false;">' . $f_agent[0]['nom'] . ' ' . $f_agent[0]['prenom'] . '</span>';
                    }
                }
                $this->setup_table(APP_NAME, 'egw_asecurite_ville');
                if ($row['idasecurite_agent'] != '') {
                    $f_ville = $this->search(array('idasecurite_ville' => $row['idasecurite_ville']), false);
                    if (count($f_ville) == 1) {
                        $planning_ville_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_horaires_ville.index', 'id' => $row['idasecurite_ville'], 'current' => 'true'));
                        $row['ville'] = '<span style="cursor:pointer; color:blue;" onclick="egw_openWindowCentered2(\'' . $planning_ville_link . '\', \'_blank\', 1100, 700, \'yes\'); return false;">' . $f_ville[0]['nom'] . '</span>';
                    }
                }


                $id = $row['idasecurite_horaires_agent'];
                $delete_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_horaires_ville.delete_planning'));
                $row['operation'] = '<span style="float:right">';
                $row['operation'] .= '<a href="' . $this->current_link . '&editId=' . $id . '">' . $this->html->image(APP_NAME, 'edit', lang("Modifier"), 'style="cursor:pointer"') . '</a>';
                $row['operation'] .= '&nbsp;' . $this->html->image(APP_NAME, 'delete', lang("Supprimer la ligne"), 'style="cursor:pointer" id="' . $id . '" onclick="deleteElement(\'' . $id . '\', \'' . lang('Voulez vous les planning sélectionnés?') . '\', \'' . $delete_link . '\', \'' . $this->current_link . '\' );"');
                $row['operation'] .= '&nbsp;' . $this->html->input('checkbox[' . $id . ']', $id, 'checkbox', 'id="checkbox[' . $id . ']"') . '</span>';

                $this->manage_display($row);
                $planning_row['idasecurite_horaires_agent'] = $row['idasecurite_horaires_agent'];
                $planning_row['agent'] = $row['agent'];
                $planning_row['heure_arrivee'] = $row['heure_arrivee'];
                $planning_row['pause'] = $row['pause'];
                $planning_row['heure_depart'] = $row['heure_depart'];
                $planning_row['nombre_heures'] = $row['nombre_heures'];
                $planning_row['panier'] = $row['panier'];
                $planning_row['heures_jour'] = $row['heures_jour'];
                $planning_row['heures_nuit'] = $row['heures_nuit'];
                $planning_row['heures_jour_dimanche'] = $row['heures_jour_dimanche'];
                $planning_row['heures_nuit_dimanche'] = $row['heures_nuit_dimanche'];
                $planning_row['site'] = $row['site'];
                $planning_row['ville'] = $row['ville'];
                $planning_row['operation'] = $row['operation'];
                $output['aaData'][] = $planning_row;
            }
        }
        $return = json_encode($output);
        echo $return;
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
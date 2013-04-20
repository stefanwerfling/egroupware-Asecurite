<?php

/**
 * <b>File class.ui_horaires_agent.inc.php</b>
 * asecurite's agent planning user-interface
 * @author N'faly KABA
 * @since   11/08/2011
 * @version 2.0
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
        'delete_planning' => True,
        'get_data' => True,
        'edit' => True
    );

    function __construct() {
        parent::__construct();
        $this->init_template(lang('Gestion des horaires'));
        $this->current_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_horaires_agent.index',
            'id' => $GLOBALS['egw']->session->appsession('idasecurite_agent', APP_NAME),
            'current' => 'true',
            'month' => $GLOBALS['egw']->session->appsession('current_month', APP_NAME),
            'year' => $GLOBALS['egw']->session->appsession('current_year', APP_NAME),
            'site' => $GLOBALS['egw']->session->appsession('current_site', APP_NAME)));
    }

    /**
     * Display the application home content
     */
    public function index($content = NULL) {
        //Get current agent id
        $id = get_var('id');
        if ($id != '') {
            $GLOBALS['egw']->session->appsession('idasecurite_agent', APP_NAME, $id);
        }
        //Get request in case of modification
        if (get_var('month')) {
            $this->current_month = get_var('month');
            $this->current_year = get_var('year');
            $content['idasecurite_site'] = get_var('site');
        }
        //Manage deletions
        if (isset($content['nm']['delete'])) {
            list($del) = each($content['nm']['delete']);

            $this->delete(array('idasecurite_horaires_agent' => $del));
        } elseif (isset($content['delete_selected'])) {
            for ($i = 0; $i < count($content['nm']['checkbox']); $i++) {
                $this->delete(array('idasecurite_horaires_agent' => $content['nm']['checkbox'][$i]));
            }
        }
        //Get current agent name
        $this->setup_table(APP_NAME, 'egw_asecurite_agent');
        $f_agent = $this->search(array('idasecurite_agent' => $GLOBALS['egw']->session->appsession('idasecurite_agent', APP_NAME)), false);
        if (count($f_agent) == 1) {
            $content['agent'] = $f_agent[0]['nom'] . ' ' . $f_agent[0]['prenom'];
            $content['idasecurite_ville'] = $f_agent[0]['idasecurite_ville'];
            $GLOBALS['egw']->session->appsession('idasecurite_ville', APP_NAME, $content['idasecurite_ville']);
        }
        $content['idasecurite_agent'] = $GLOBALS['egw']->session->appsession('idasecurite_agent', APP_NAME);
        //Update select options inputs for sites
        $this->update_lists($content['idasecurite_ville']);
        $select_option = array(
            'idasecurite_site' => $this->sites,
            'mois' => $this->monthes,
            'annee' => $this->years,
        );
        //Manage add or edit (modification) operation
        parent::edit_planning($content, 'egw_asecurite_horaires_agent', 'Agent schedule', array('menuaction' => APP_NAME . '.ui_horaires_agent.index'));

        //In case of any existing sites
        if (!$this->sites) {
            $content['msg_horaire'] = "<span id='error' style='font-weight:bold'>" . lang('Threre is no site !') . " </span>";
        }
        //Get all plannings following the values containing in select inputs and the save them into a session
        $GLOBALS['egw']->session->appsession('all_planning_agent', APP_NAME, $this->get_mensual_planning($content['mois'], $content['annee'], $GLOBALS['egw']->session->appsession('idasecurite_agent', APP_NAME), $content['idasecurite_site'], $content['idasecurite_ville'], $content['print_from'], $content['print_to']));
        //Draw stat graphes
        $content['stat'] = '<div class="stat">' . $this->draw_stat($GLOBALS['egw']->session->appsession('all_planning_agent', APP_NAME)) . '</div>';

        //Get add or edit result
        $msg = get_var('msg', array('GET'));
        $save = get_var('save', array('GET'));

        //-----------  fill planning table ---------------
        $t = & CreateObject('phpgwapi.Template', EGW_APP_TPL);
        $t->set_file(array(
            'T_planning_agents' => 'planning_agents.tpl'
        ));
        $t->set_block('T_planning_agents', 'planning_agents');

        $data_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_horaires_agent.get_data'));
        $delete_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_horaires_agent.delete_planning'));
        $t->set_var('DATA_LINK', $data_link);
        $t->set_var('MSG', "<span id=\"$save\">" . lang($msg) . " </span>");
        $t->set_var('DELETE_LINK', $delete_link);
        $t->set_var('INDEX_LINK', $this->current_link);
        $t->set_var('DELETE_BUTTON', $this->html->image(APP_NAME, 'delete', lang('Supprimer les plannings sélectionnés?')));
        $t->set_var('SELECT_ALL', $this->html->image(APP_NAME, 'arrow_ltr', lang('Tout cocher/décocher'), 'onclick="check_all(); return false;"'));
        $content['data'] = $t->parse('out', 'planning_agents');
        //------------ END of filling ---------------------------

        $this->compute_paniers($GLOBALS['egw']->session->appsession('all_planning_agent', APP_NAME));
        $content['paniers'] = $this->nb_baskets;
        //Set sessions values
        $GLOBALS['egw']->session->appsession('current_month', APP_NAME, $content['mois']);
        $GLOBALS['egw']->session->appsession('current_year', APP_NAME, $content['annee']);
        $GLOBALS['egw']->session->appsession('current_agent', APP_NAME, $GLOBALS['egw']->session->appsession('idasecurite_agent', APP_NAME));
        $GLOBALS['egw']->session->appsession('current_ville', APP_NAME, $GLOBALS['egw']->session->appsession('idasecurite_ville', APP_NAME));
        $GLOBALS['egw']->session->appsession('current_site', APP_NAME, $content['idasecurite_site']);
        //Session for print
        $GLOBALS['egw']->session->appsession('planning_to_print', APP_NAME, $GLOBALS['egw']->session->appsession('all_planning_agent', APP_NAME));
        $GLOBALS['egw']->session->appsession('print_from', APP_NAME, $GLOBALS['egw']->session->appsession('all_planning_agent', APP_NAME));

        $this->tmpl->read(APP_NAME . '.agent.planning'); //APP_NAME defined in asecurite/inc/class.bo_asecurite.inc.php
        $this->tmpl->exec(APP_NAME . '.ui_horaires_agent.index', $content, $select_option, '', '', 2);
    }

    /**
     * get all planning for agent
     */
    public function get_data() {
        $rows = $GLOBALS['egw']->session->appsession('all_planning_agent', APP_NAME);
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
                        $row['site'] = '<span style="cursor:pointer; color:blue;" onclick="egw_openWindowCentered2(\'' . $planning_site_link . '\', \'_blank\', '. $this->planning_width. ', '. $this->planning_height. ', \'yes\'); return false;">' . $f_site_name[0]['nom'] . '</span>';
                    }
                    $this->setup_table(APP_NAME, 'egw_asecurite_ville');
                    if ($row['idasecurite_agent'] != '') {
                        $f_ville = $this->search(array('idasecurite_ville' => $row['idasecurite_ville']), false);
                        if (count($f_ville) == 1) {
                            $planning_ville_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_horaires_ville.index', 'id' => $row['idasecurite_ville'], 'current' => 'true'));
                            $row['ville'] = '<span style="cursor:pointer; color:blue;" onclick="egw_openWindowCentered2(\'' . $planning_ville_link . '\', \'_blank\', '. $this->planning_width. ', '. $this->planning_height. ', \'yes\'); return false;">' . $f_ville[0]['nom'] . '</span>';
                        }
                    }
                    $id = $row['idasecurite_horaires_agent'];
                    $delete_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_horaires_agent.delete_planning'));

                    $row['operation'] = '<span style="float:right">';
                    $row['operation'] .='<a href="' . $this->current_link . '&editId=' . $id . '">' . $this->html->image(APP_NAME, 'edit', lang("Modifier"), 'style="cursor:pointer"') . '</a>';
                    $row['operation'] .='&nbsp;' . $this->html->image(APP_NAME, 'delete', lang("Supprimer la ligne"), 'style="cursor:pointer" id="' . $id . '" onclick="deleteElement(\'' . $id . '\', \'' . lang('Voulez vous supprimer les planning sélectionnés?') . '\', \'' . $delete_link . '\', \'' . $this->current_link . '\' );"');
                    $row['operation'] .= '&nbsp;' . $this->html->input('checkbox[' . $id . ']', $id, 'checkbox', 'id="checkbox[' . $id . ']"') . '</span>';
                    $this->manage_display($row);

                    $planning_row['idasecurite_horaires_agent'] = $row['idasecurite_horaires_agent'];
                    $planning_row['heure_arrivee'] = $row['heure_arrivee'];
                    $planning_row['pause'] = $row['pause'];
                    $planning_row['heure_depart'] = $row['heure_depart'];
                    $planning_row['nombre_heures'] = $row['nombre_heures'];
                    $planning_row['panier'] = $row['panier'];
                    $planning_row['heures_jour'] = $row['heures_jour'];
                    $planning_row['heures_nuit'] = $row['heures_nuit'];
                    $planning_row['heures_jour_dimanche'] = $row['heures_jour_dimanche'];
                    $planning_row['heures_nuit_dimanche'] = $row['heures_nuit_dimanche'];
                    $planning_row['ville'] = $row['ville'];
                    $planning_row['site'] = $row['site'];
                    $planning_row['operation'] = $row['operation'];
                    $output['aaData'][] = $planning_row;
                }
            }
        }
        $return = json_encode($output);
        echo $return;
    }

}
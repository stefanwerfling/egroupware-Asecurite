<?php

/**
 * <b>File class.ui_horaires_ville.inc.php</b>
 * asecurite's agent planning user-interface
 * @author N'faly KABA
 * @since   25/08/2011
 * @version 2.0
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
        'delete_planning' => True,
        'get_data' => True
    );

    function __construct() {

        parent::__construct();
        $this->init_template(lang('Gestion des horaires'));
        $this->current_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_horaires_ville.index',
            'id' => $GLOBALS['egw']->session->appsession('idasecurite_ville', APP_NAME),
            'current' => 'true',
            'month' => $GLOBALS['egw']->session->appsession('current_month', APP_NAME),
            'year' => $GLOBALS['egw']->session->appsession('current_year', APP_NAME),
            'agent' => $GLOBALS['egw']->session->appsession('current_agent', APP_NAME),
            'site' => $GLOBALS['egw']->session->appsession('current_site', APP_NAME)));
    }
    /**
     * Display the application home content
     */
    public function index($content = NULL) {
        $id = get_var('id');
        if ($id != '') {
            $GLOBALS['egw']->session->appsession('idasecurite_ville', APP_NAME, $id);
        }
        // Get request parameters
        if (get_var('month')) {
            $this->current_month = get_var('month');
            $this->current_year = get_var('year');
            $content['idasecurite_agent'] = get_var('agent');
            $content['idasecurite_site'] = get_var('site');
        }
        //Deletion management
        if (isset($content['nm']['delete'])) {
            list($del) = each($content['nm']['delete']);
            $this->delete(array('idasecurite_horaires_agent' => $del));
        } elseif (isset($content['delete_selected'])) {
            for ($i = 0; $i < count($content['nm']['checkbox']); $i++) {
                $this->delete(array('idasecurite_horaires_agent' => $content['nm']['checkbox'][$i]));
            }
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
        //Add
        parent::edit_planning($content, 'egw_asecurite_horaires_agent', 'City schedule',  array('menuaction' => APP_NAME . '.ui_horaire_ville.index'));
        if (!$this->agents) {
            $content['msg_horaire'] = "<span id='error' style='font-weight:bold'>" . lang('Threre is no agent !') . " </span>";
        }
        //Get plannings by using filter
        $GLOBALS['egw']->session->appsession('all_planning_city', APP_NAME, $this->get_mensual_planning($content['mois'], $content['annee'], $content['idasecurite_agent'], $content['idasecurite_site'], $content['idasecurite_ville']));
        
        //Draw stat
        $content['stat'] = '<div class="stat">' . $this->draw_stat($GLOBALS['egw']->session->appsession('all_planning_city', APP_NAME)) . '</div>';
        $msg = get_var('msg', array('GET'));
        $save = get_var('save', array('GET'));

        $this->compute_paniers($GLOBALS['egw']->session->appsession('all_planning_city', APP_NAME));
        $data_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_horaires_ville.get_data'));
        $delete_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_horaires_ville.delete_planning'));
        $tpl_content = file_get_contents(EGW_INCLUDE_ROOT . '/' . APP_NAME . '/templates/default/planning_villes.html');
        $tpl_content = str_replace('DATA_LINK', $data_link, $tpl_content);
        $tpl_content = str_replace('MSG', "<span id=\"$save\">" . lang($msg) . " </span>", $tpl_content);
        $tpl_content = str_replace('DELETE_LINK', $delete_link, $tpl_content);
        $tpl_content = str_replace('INDEX_LINK', $this->current_link, $tpl_content);
        $tpl_content = str_replace('DELETE_BUTTON', $this->html->image(APP_NAME, 'delete', lang('Supprimer les plannings sélectionnés?')), $tpl_content);
        $tpl_content = str_replace('SELECT_ALL', $this->html->image(APP_NAME, 'arrow_ltr', lang('Tout cocher/décocher'), 'onclick="check_all(); return false;"'), $tpl_content);
        $content['data'] = $tpl_content;
        $content['paniers'] = $this->nb_baskets;
        $this->tmpl->read(APP_NAME . '.ville.planning'); //APP_NAME defined in asecurite/inc/class.bo_asecurite.inc.php

        $GLOBALS['egw']->session->appsession('current_month', APP_NAME, $content['mois']);
        $GLOBALS['egw']->session->appsession('current_year', APP_NAME, $content['annee']);
        $GLOBALS['egw']->session->appsession('current_agent', APP_NAME, $content['idasecurite_agent']);
        $GLOBALS['egw']->session->appsession('current_ville', APP_NAME, $GLOBALS['egw']->session->appsession('idasecurite_ville', APP_NAME));
        $GLOBALS['egw']->session->appsession('current_site', APP_NAME, $content['idasecurite_site']);
        $GLOBALS['egw']->session->appsession('planning_to_print', APP_NAME, $GLOBALS['egw']->session->appsession('all_planning_city', APP_NAME));

        $this->tmpl->exec(APP_NAME . '.ui_horaires_ville.index', $content, $select_option, $readonlys, '', 2);
        $this->create_footer();
    }
    /**
     * get all planning for site
     */
    public function get_data() {
        $rows = $GLOBALS['egw']->session->appsession('all_planning_city', APP_NAME);
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
                        $row['site'] = '<span style="cursor:pointer; color:blue;" onclick="egw_openWindowCentered2(\'' . $planning_site_link . '\', \'_blank\','. $this->planning_width. ', '. $this->planning_height. ', \'yes\'); return false;">' . $f_site_name[0]['nom'] . '</span>';
                    }
                }
                $this->setup_table(APP_NAME, 'egw_asecurite_agent');
                if ($row['idasecurite_agent'] != '') {
                    $f_agent = $this->search(array('idasecurite_agent' => $row['idasecurite_agent']), false);
                    if (count($f_agent) == 1) {
                        $planning_agent_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_horaires_agent.index', 'id' => $row['idasecurite_agent'], 'current' => 'true'));
                        $row['agent'] = '<span style="cursor:pointer; color:blue;" onclick="egw_openWindowCentered2(\'' . $planning_agent_link . '\', \'_blank\', '. $this->planning_width. ', '. $this->planning_height. ', \'yes\'); return false;">' . $f_agent[0]['nom'] . ' ' . $f_agent[0]['prenom'] . '</span>';
                    }
                }
                $id = $row['idasecurite_horaires_agent'];
                $delete_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_horaires_ville.delete_planning'));
                $row['operation'] = '<span style="float:right">';
                $row['operation'] .= '<a href="' . $this->current_link . '&editId=' . $id . '">' . $this->html->image(APP_NAME, 'edit', lang("Modifier"), 'style="cursor:pointer"') . '</a>';
                $row['operation'] .= '&nbsp;' . $this->html->image(APP_NAME, 'delete', lang("Supprimer la ligne"), 'style="cursor:pointer" id="' . $id . '" onclick="deleteElement(\'' . $id . '\', \'' . lang('Voulez vous supprimer les planning sélectionnés?') . '\', \'' . $delete_link . '\', \'' . $this->current_link . '\' );"');
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
                $planning_row['operation'] = $row['operation'];
                $output['aaData'][] = $planning_row;
            }
        }
        $return = json_encode($output);
        echo $return;
    }

}
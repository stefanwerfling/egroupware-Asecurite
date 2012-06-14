<?php

/**
 * <b>File class.ui_horaire_sites.inc.php</b>
 * asecurite's agent planning user-interface
 * @author N'faly KABA
 * @since   25/08/2011
 * @version 1.0
 * @copyright KABANFALY
 * @package egroupware
 * @subpackage asecurite/inc/
 * @filesource  class.ui_horaire_sites.inc.php
 */
include_once(EGW_INCLUDE_ROOT . '/asecurite/inc/class.bo_horaires_site.inc.php');

class ui_horaires_site extends bo_horaires_site {

    /**
     * Public functions callable via menuaction
     *
     * @var array
     */
    var $public_functions = array(
        'index' => true,
        'get_rows' => true,
        'delete_planning' => true,
        'get_data' => true
    );

    function __construct() {

        parent::__construct();
        $this->init_template(lang('Gestion des horaires'));
        $this->current_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_horaires_site.index',
            'id' => $GLOBALS['egw']->session->appsession('idasecurite_site', APP_NAME),
            'current' => 'true',
            'month' => $GLOBALS['egw']->session->appsession('current_month', APP_NAME),
            'year' => $GLOBALS['egw']->session->appsession('current_year', APP_NAME),
            'agent' => $GLOBALS['egw']->session->appsession('current_agent', APP_NAME)));
    }

    /**
     * Display the application home content
     */
    public function index($content = NULL) {

        $id = get_var('id');
        if ($id != '') {
            $GLOBALS['egw']->session->appsession('idasecurite_site', APP_NAME, $id);
        }
        if (get_var('month')) {
            $this->current_month = get_var('month');
            $this->current_year = get_var('year');
            $content['idasecurite_agent'] = get_var('agent');
        }
        if (isset($content['nm']['delete'])) {
            list($del) = each($content['nm']['delete']);

            $this->delete(array('idasecurite_horaires_agent' => $del));
        } elseif (isset($content['delete_selected'])) {

            for ($i = 0; $i < count($content['nm']['checkbox']); $i++) {

                $this->delete(array('idasecurite_horaires_agent' => $content['nm']['checkbox'][$i]));
            }
        }/* elseif (isset($content['print'])) {
            $link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_imprime.print_planning_global'));
            $this->js_content .= '<script type="text/javascript">
                                open_popup(\'' . $link . '\', 800,700) ;
                            </script>';

            $GLOBALS['egw']->session->appsession('current_month', APP_NAME, $content['mois']);
            $GLOBALS['egw']->session->appsession('current_year', APP_NAME, $content['annee']);
            $GLOBALS['egw']->session->appsession('current_agent', APP_NAME, $content['idasecurite_agent']);
            $GLOBALS['egw']->session->appsession('current_ville', APP_NAME, $GLOBALS['egw']->session->appsession('idasecurite_ville', APP_NAME));
            $GLOBALS['egw']->session->appsession('current_site', APP_NAME, $GLOBALS['egw']->session->appsession('idasecurite_site', APP_NAME));
            $GLOBALS['egw']->session->appsession('planning_to_print', APP_NAME, $GLOBALS['egw']->session->appsession('all_planning_site', APP_NAME));
        }*/

        $this->setup_table(APP_NAME, 'egw_asecurite_site');

        $f_site = $this->search(array('idasecurite_site' => $GLOBALS['egw']->session->appsession('idasecurite_site', APP_NAME)), false);
        if (count($f_site) == 1) {
            $content['site'] = $f_site[0]['nom'];
            $content['idasecurite_ville'] = $f_site[0]['idasecurite_ville'];
            $GLOBALS['egw']->session->appsession('idasecurite_ville', APP_NAME, $content['idasecurite_ville']);
        }
        $content['idasecurite_site'] = $GLOBALS['egw']->session->appsession('idasecurite_site', APP_NAME);

        $this->update_lists($content['idasecurite_ville']);

        $select_option = array(
            'idasecurite_agent' => $this->agents,
            'mois' => $this->monthes,
            'annee' => $this->years,
        );

        parent::edit_planning($content, 'egw_asecurite_horaires_agent', 'Site schedule', array('heure_arrivee', 'heure_depart', 'pause', 'heures_jour', 'heures_nuit', 'idasecurite_agent', 'idasecurite_site', 'idasecurite_ville', 'heures_jour_dimanche', 'heures_nuit_dimanche'), array('menuaction' => APP_NAME . '.ui_horaire_site.index'));

        if (!$this->agents) {
            $content['msg_horaire'] = "<span id='error' style='font-weight:bold'>" . lang('Threre is no agent !') . " </span>";
        }
        $GLOBALS['egw']->session->appsession('all_planning_site', APP_NAME, $this->get_mensual_planning($content['mois'], $content['annee'], $content['idasecurite_agent'], $GLOBALS['egw']->session->appsession('idasecurite_site', APP_NAME), $content['idasecurite_ville']));

        $content['stat'] = '<div class="stat">' . $this->draw_stat($GLOBALS['egw']->session->appsession('all_planning_site', APP_NAME)) . '</div>';

        $msg = get_var('msg', array('GET'));
        $save = get_var('save', array('GET'));
        $data_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_horaires_site.get_data'));
        $delete_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_horaires_site.delete_planning'));
        $tpl_content = file_get_contents(EGW_INCLUDE_ROOT . '/' . APP_NAME . '/templates/default/planning_sites.html');
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
        $GLOBALS['egw']->session->appsession('current_ville', APP_NAME, $GLOBALS['egw']->session->appsession('idasecurite_ville', APP_NAME));
        $GLOBALS['egw']->session->appsession('current_site', APP_NAME, $GLOBALS['egw']->session->appsession('idasecurite_site', APP_NAME));
        $GLOBALS['egw']->session->appsession('planning_to_print', APP_NAME, $GLOBALS['egw']->session->appsession('all_planning_site', APP_NAME));

        $this->tmpl->read(APP_NAME . '.site.planning'); //APP_NAME defined in asecurite/inc/class.bo_asecurite.inc.php
        $this->tmpl->exec(APP_NAME . '.ui_horaires_site.index', $content, $select_option, $readonlys, '', 2);
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
    /*public function get_rows() {
        $rows = $GLOBALS['egw']->session->appsession('all_planning_site', APP_NAME);
        foreach ($rows as $i => &$row) {
            $this->setup_table(APP_NAME, 'egw_asecurite_agent');
            if ($row['idasecurite_agent'] != '') {
                $f_agent_name = $this->search(array('idasecurite_agent' => $row['idasecurite_agent']), false);
                if (count($f_agent_name) == 1) {
                    $row['agent'] = $f_agent_name[0]['nom'] . ' ' . $f_agent_name[0]['prenom'];
                }
                $this->manage_display($row);
            }
        }
        return count($rows);
    }*/

    /**
     * get all planning for site
     */
    public function get_data() {
        $rows = $GLOBALS['egw']->session->appsession('all_planning_site', APP_NAME);
        $output = array(
            "sEcho" => intval($_GET['sEcho']),
            "iTotalRecords" => count($rows),
            "iTotalDisplayRecords" => count($rows),
            "aaData" => array()
        );

        foreach ($rows as &$row) {
            $this->setup_table(APP_NAME, 'egw_asecurite_agent');
            if ($row['idasecurite_agent'] != '') {

                $f_agent_name = $this->search(array('idasecurite_agent' => $row['idasecurite_agent']), false);
                if (count($f_agent_name) == 1) {
                    $row['agent'] = $f_agent_name[0]['nom'] . ' ' . $f_agent_name[0]['prenom'];
                }
                $id = $row['idasecurite_horaires_agent'];
                $delete_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_horaires_site.delete_planning'));

                $row['operation'] = '<span style="float:right">';
                $row['operation'] .= '<a href="'.$this->current_link.'&editId='.$id.'">'. $this->html->image(APP_NAME, 'edit', lang("Modifier"), 'style="cursor:pointer"').'</a>';
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
                $planning_row['operation'] = $row['operation'];
                $output['aaData'][] = $planning_row;
            }
        }
        $return = json_encode($output);
        echo $return;
    }

}
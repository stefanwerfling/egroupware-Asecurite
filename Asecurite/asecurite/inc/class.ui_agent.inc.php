<?php

/**
 * <b>File class.ui_agent.inc.php</b>
 * asecurite's agent user interface
 * @author N'faly KABA
 * @since   2/08/2011
 * @version 2.0
 * @copyright KABANFALY
 * @package egroupware
 * @subpackage asecurite/inc/
 * @filesource  class.ui_agent.inc.php
 */
include_once(EGW_INCLUDE_ROOT . '/asecurite/inc/class.bo_agent.inc.php');
include_once(EGW_INCLUDE_ROOT . '/asecurite/inc/lib/phpToPDF.php');

class ui_agent extends bo_agent {

    var $type_contrat = array();

    /**
     * Public functions callable via menuaction
     *
     * @var array
     */
    var $public_functions = array(
        'index' => True,
        'redirect_to_edit' => True,
        'edit' => True,
        'get_agent_info' => True,
        'delete_agent' => True,
        'get_data' => True
    );

    function __construct() {

        parent::__construct();
        $this->init_template(lang('Agents management'));
        $this->type_contrat = array(
            '' => lang('Choisissez ...'),
            'CDD' => 'CDD',
            'CDI' => 'CDI',
        );
        $this->height = 800;
        $this->current_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_agent.index'));
    }

    public function index() {
        $this->createHeader();
        $GLOBALS['egw']->js->set_onload("include('" . $GLOBALS['egw_info']['server']['webserver_url'] . "/phpgwapi/inc/jscalendar-setup.php?dateformat=d.m.Y&amp;lang=fr');");
        $msg = get_var('msg', array('GET'));
        $save = get_var('save', array('GET'));
        $msg = "<span id=\"$save\">" . lang($msg) . " </span>";
        print $this->setup_index($msg);
    }

    public function setup_index($msg = '') {

        $t = & CreateObject('phpgwapi.Template', EGW_APP_TPL);
        $t->set_file(array(
            'T_agents' => 'agents.tpl'
        ));
        $t->set_block('T_agents', 'agents');

        $title_bar = lang(APP_NAME) . ' - ' . lang("Gestion des agents") . ' - ' . lang('Add');
        $add_link = bo_fwkpopin::draw_button(APP_NAME . '.ui_agent.ajax_edit', 'Add', $this->width, $this->height, 0, $title_bar);
        // $add_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_agent.redirect_to_edit'));
        $data_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_agent.get_data'));
        $delete_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_agent.delete_agent'));

        $t->set_var('ADD_LINK', $add_link);
        $t->set_var('DATA_LINK', $data_link);
        $t->set_var('MSG', $msg);
        $t->set_var('DELETE_LINK', $delete_link);
        $t->set_var('INDEX_LINK', $this->current_link);
        $t->set_var('DELETE_BUTTON', $this->html->image(APP_NAME, 'delete', lang('Supprimer les agents sélectionnés?')));
        $t->set_var('SELECT_ALL', $this->html->image(APP_NAME, 'arrow_ltr', lang('Tout cocher/décocher'), 'onclick="check_all(); return false;"'));
        return $t->parse('out', 'agents');
    }

    /**
     * Display the application home content
     */
    public function ajax_index($msg) {
        $_response = new xajaxResponse();
        $content = $this->setup_index($msg);
        OPF_Logger::logDebug('content', $content);
        $_response->addAssign('divAppbox', 'innerHTML', $content);
        return $_response->getXML();
    }

    /**
     * delete an agent
     */
    public function delete_agent() {
        $id_agent = get_var('id');
        if ($id_agent !== '') {
            $explode = explode('-', $id_agent);
            $count = count($explode);
            if ($count == 1) {
                parent::delete_agent($id_agent);
            } else {
                for ($i = 0; $i < $count; $i++) {
                    parent::delete_agent($explode[$i]);
                }
            }
        }
    }

    /**
     * get all agents to display 
     */
    public function get_data() {
        $rows = $this->search('', false);
        $this->setup_table(APP_NAME, 'egw_asecurite_ville');
        $output = array(
            "sEcho" => intval($_GET['sEcho']),
            "iTotalRecords" => count($rows),
            "iTotalDisplayRecords" => count($rows),
            "aaData" => array()
        );
        if ($rows) {
            foreach ($rows as &$row) {
                $f_city_name = $this->search(array('idasecurite_ville' => $row['idasecurite_ville']), false);
                if (count($f_city_name) == 1) {
                    $row['idasecurite_ville'] = '<span id="ville">' . $f_city_name[0]['nom'] . '</span>';
                }
                $id = $row['idasecurite_agent'];
                $row['date_debut_contrat'] = $row['date_debut_contrat'] == '' ? '' : $this->format_date($row['date_debut_contrat']);
                $style = 'success';
                if (self::is_expired($row['date_fin_contrat'])) {
                    $style = 'error';
                }
                $row['date_fin_contrat'] = $row['date_fin_contrat'] == '' ? '' : '<span id="' . $style . '">' . $this->format_date($row['date_fin_contrat']) . '</span>';

                $planning_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_horaires_agent.index', 'id' => $id, 'current' => 'true'));
                //$edit_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_agent.edit', 'id' => $id));
                $edit_link = APP_NAME . '.ui_agent.ajax_edit';
                $delete_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_agent.delete_agent'));
                $row['nom'] = '<span style="cursor:pointer; color:blue;" onclick="egw_openWindowCentered2(\'' . $planning_link . '\', \'_blank\', 1100, 700, \'yes\'); return false;">' . $row['nom'] . ' ' . $row['prenom'] . '</span>';

                $row['piece_identite'] = '--';
                $style = 'success';
                if ($row['type_piece_identite'] != '') {

                    if (self::is_expired($row['date_fin_piece_identite'])) {
                        $style = 'error';
                    }
                    $row['piece_identite'] = $row['type_piece_identite'] . ' N&deg;: ' . $row['numero_piece_identite'] . '<br>';
                    if ($row['date_fin_piece_identite'] != '') {
                        $row['piece_identite'] .= '<span id="' . $style . '">Fin: ' . $this->format_date($row['date_fin_piece_identite']) . '</span>';
                    }
                }
                $title_bar = lang(APP_NAME) . ' - ' . lang("Gestion des agents") . ' - ' . lang('Edit');
                $agent_info_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_agent.get_agent_info', 'id' => $id));
                $agent_info = $this->html->image(APP_NAME, 'view', lang("Afficher les infos"), 'style="cursor:pointer" onclick="egw_openWindowCentered2(\'' . $agent_info_link . '\', \'_blank\', 450, 400, \'yes\'); return false;"');
                $row['piece_identite'] .= '&nbsp; ' . $agent_info;
                $row['operation'] = '<span style="float:right">';
                //$row['operation'] .= $this->html->image(APP_NAME, 'edit', lang("Modifier l'agent"), 'style="cursor:pointer" onclick="egw_openWindowCentered2(\'' . $edit_link . '\', \'_blank\', 600, 700, \'yes\'); return false;"');
                $row['operation'] .= bo_fwkpopin::draw_icon_button($edit_link, $GLOBALS['egw']->common->image(APP_NAME, 'edit'), $this->width, $this->height, $id, 'id="edit2__' . $id . '" title="' . lang('Edit') . '" style="cursor:pointer;"', $title_bar);
                $row['operation'] .='&nbsp;' . $this->html->image(APP_NAME, 'delete', lang("Supprimer l'agent"), 'style="cursor:pointer" id="' . $id . '" onclick="deleteElement(\'' . $id . '\', \'' . lang('Voulez vous supprimer cet agent?') . '\', \'' . $delete_link . '\', \'' . $this->current_link . '\' );"');
                $row['operation'] .= '&nbsp;' . $this->html->input('checkbox[' . $id . ']', $id, 'checkbox', 'id="checkbox[' . $id . ']"') . '</span>';

                $output['aaData'][] = $row;
            }
        }
        $return = json_encode($output);
        echo $return;
    }

    /**
     * find a agent and display all information about him
     * @param in $agent_id 
     * @return html
     */
    function get_agent_info() {
        $pdf = new phpToPDF();
        $pdf->AddPage();
        $pdf->SetFont('Times', 'B', 14);
        $pdf->Image(EGW_INCLUDE_ROOT . '/' . APP_NAME . '/templates/default/images/asecurite.png', 5, 6);
        $pdf->Ln(30);
        $pdf->Cell(0, 15, lang("Fiche d'information"), 1, 1, 'C');
        $pdf->Ln(5);

        $this->setup_table(APP_NAME, 'egw_asecurite_agent');
        $agent_id = get_var('id');

        if ($agent_id) {
            $f_agent = $this->search(array('idasecurite_agent' => $agent_id), false);
            if ($f_agent) {
                $this->setup_table(APP_NAME, 'egw_asecurite_ville');
                $f_city_name = $this->search(array('idasecurite_ville' => $f_agent[0]['idasecurite_ville']), false);
                if (count($f_city_name) == 1) {
                    $f_agent[0]['idasecurite_ville'] = $f_city_name[0]['nom'];
                }
                $f_agent[0]['date_debut_contrat'] = $f_agent[0]['date_debut_contrat'] == '' ? '' : $this->format_date($f_agent[0]['date_debut_contrat']);
                $f_agent[0]['date_fin_contrat'] = $f_agent[0]['date_fin_contrat'] == '' ? '' : $this->format_date($f_agent[0]['date_fin_contrat']);
                $f_agent[0]['date_debut_piece_identite'] = $f_agent[0]['date_debut_piece_identite'] == '' ? '' : $this->format_date($f_agent[0]['date_debut_piece_identite']);
                $f_agent[0]['date_fin_piece_identite'] = $f_agent[0]['date_fin_piece_identite'] == '' ? '' : $this->format_date($f_agent[0]['date_fin_piece_identite']);

                $this->_write_info($pdf, lang("Agent"), strtoupper($f_agent[0]['nom']) . ' ' . ucwords($f_agent[0]['prenom']), 15);
                $this->_write_info($pdf, lang("Date de naissance"), $f_agent[0]['date_naissance'], 35);
                $this->_write_info($pdf, lang("Adresse"), $f_agent[0]['adresse'] . ' ' . $f_agent[0]['code_postal'] . ', ' . $f_agent[0]['idasecurite_ville'], 20);
                $this->_write_info($pdf, lang("Téléphone"), $f_agent[0]['telephone'], 23);
                $this->_write_info($pdf, lang("Email"), $f_agent[0]['email'], 18);
                $this->_write_info($pdf, lang("Type de contrat"), $f_agent[0]['type_contrat'], 33);
                $this->_write_info($pdf, lang("Date de début de contrat"), $f_agent[0]['date_debut_contrat'], 50);
                $this->_write_info($pdf, lang("Date de fin de contrat"), $f_agent[0]['date_fin_contrat'], 45);
                $this->_write_info($pdf, lang("Type de pièce d'identité"), $f_agent[0]['type_piece_identite'], 47);
                $this->_write_info($pdf, lang("Numéro"), $f_agent[0]['numero_piece_identite'], 20);
                $this->_write_info($pdf, lang("Date de début de validité"), $f_agent[0]['date_debut_piece_identite'], 50);
                $this->_write_info($pdf, lang("Date de fin de validité"), $f_agent[0]['date_fin_piece_identite'], 45);
                $this->_write_info($pdf, lang("Commune/Préfecture"), $f_agent[0]['commune_piece_identite'], 43);
                $this->_write_info($pdf, lang("Pays"), $f_agent[0]['pays_piece_identite'], 12);
            }
        }
        //$t->pparse('out', 'info_agent');
        $pdf->Output();
    }

    private function _write_info(&$pdf, $label, $info, $label_w = 10, $info_w = 10) {
        $pdf->SetFont('Times', 'B', 12);
        $pdf->Cell($label_w, 10, utf8_decode($label) . ':', 0, 0);
        $pdf->SetFont('Times', '', 12);
        $pdf->Cell($info_w, 10, utf8_decode($info), 0, 1);
    }

    /**
     * redirect to edit page and unset session value in order to do add operation
     * @return void
     */
    function redirect_to_edit() {
        parent::redirect_to_edit('idasecurite_agent', array('menuaction' => APP_NAME . '.ui_agent.edit'));
    }

    /**
     * add or edit an agent
     * @param int $content contains processing data
     * @return void
     */
    public function edit($content = NULL) {

        $GLOBALS['egw']->js->set_onload('disable_enable_fin_contrat();');
        $content['title'] = 'Asecurite' . ' - ' . lang("Agents management");

        if ($content['type_contrat'] == 'CDI') {
            $content['date_fin_contrat'] = '';
        }
        $sel_options = array(
            'idasecurite_ville' => $this->cities,
            'type_contrat' => $this->type_contrat,
            'type_piece_identite' => array('' => lang('Choisissez ...'), 'Titre de séjour' => 'Titre de séjour', 'passeport' => 'Passeport', 'Permis de conduire' => 'Permis de conduire')
        );
        if (!$this->cities) {
            $js = "opener.location.href='" . ($link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_ville.index'))) . "';window.close();";
            $content['no_ville_msg'] = "<span id='error'>" . lang('Aucune ville trouvée') . ' <a><button onclick="' . $js . '">' . lang('Créer en ici') . '</button></a>' . " </span>";
        }
        parent::edit($content, $no_button, 'idasecurite_agent', 'Agent', 'egw_asecurite_agent', array('nom', 'prenom', 'date_naissance', 'adresse', 'code_postal', 'idasecurite_ville', 'type_contrat', 'telephone', 'date_debut_contrat', 'date_fin_contrat', 'type_piece_identite', 'numero_piece_identite', 'date_debut_piece_identite', 'date_fin_piece_identite', 'commune_piece_identite', 'pays_piece_identite', 'email'), array('menuaction' => APP_NAME . '.ui_agent.index'));
        $this->tmpl->read(APP_NAME . '.agent.edit');
        $this->tmpl->exec(APP_NAME . '.ui_agent.edit', $content, $sel_options, $no_button, '', 2);
    }

    public function ajax_edit($id, $dialog = 'dialog') {
        $content['id'] = $id;
        $GLOBALS['egw']->session->appsession('idasecurite_agent', APP_NAME, $id);
        $_response = new xajaxResponse();

        $content['title'] = 'Asecurite' . ' - ' . lang("Agents management");

        if ($content['type_contrat'] == 'CDI') {
            $content['date_fin_contrat'] = '';
        }
        $sel_options = array(
            'idasecurite_ville' => $this->cities,
            'type_contrat' => $this->type_contrat,
            'type_piece_identite' => array('' => lang('Choisissez ...'), 'Titre de séjour' => 'Titre de séjour', 'passeport' => 'Passeport', 'Permis de conduire' => 'Permis de conduire')
        );
        if (!$this->cities) {
            $js = "opener.location.href='" . ($link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_ville.index'))) . "';window.close();";
            $content['no_ville_msg'] = "<span id='error'>" . lang('Aucune ville trouvée') . ' <a><button onclick="' . $js . '">' . lang('Créer en ici') . '</button></a>' . " </span>";
        }
        parent::edit($content, $dialog);

        $this->tmpl->read(APP_NAME . '.agent.edit');
        $_edit = $this->tmpl->exec('', $content, $sel_options, '', '', 1);
        $calendarCSS = '/phpgwapi/js/jscalendar/calendar-blue.css';
        $calendarCSS .= '?' . filemtime(EGW_SERVER_ROOT . $calendarCSS);
        $_response->addIncludeCSS($GLOBALS['egw_info']['server']['webserver_url'] . $calendarCSS);
        $_response->addAssign($dialog, 'innerHTML', $_edit);
        $_response->addScript('disable_enable_fin_contrat();');
        $_response->addScript("addDatePopup('date_naissance');");
        $_response->addScript("addDatePopup('date_debut_contrat');");
        $_response->addScript("addDatePopup('date_fin_contrat');");
        $_response->addScript('Calendar.setup(
            {
                inputField  : "exec[date_naissance][str]",
                button      : "exec[date_naissance][str]-trigger"
            }
        );');
        $_response->addScript('Calendar.setup(
            {
                inputField  : "exec[date_debut_contrat][str]",
                button      : "exec[date_debut_contrat][str]-trigger"
            }
        );');
        $_response->addScript('Calendar.setup(
            {
                inputField  : "exec[date_fin_contrat][str]",
                button      : "exec[date_fin_contrat][str]-trigger"
            }
        );');
        return $_response->getXML();
    }

    /**
     * Saves a form content to database
     * @param array $content input data
     * @return string XML
     */
    public function ajax_save($content) {
        $_response = new xajaxResponse();

        $id = $GLOBALS['egw']->session->appsession('idasecurite_agent', APP_NAME);
        OPF_Logger::logDebug('id', $id);
        OPF_Logger::logDebug('id', $content);
        if ($id) {
            $save_ok = $this->save_data('Agent', 'egw_asecurite_agent', $content, $msg, array('idasecurite_agent' => $id));
        } else {
            $save_ok = $this->save_data('Agent', 'egw_asecurite_agent', $content, $msg);
        }
        $save = $save_ok ? 'success' : 'error';
        $msg = "<span id=\"$save\">" . lang($msg) . "</span>";
        $_response->addScript("ajaxCall('" . APP_NAME . ".ui_agent.ajax_index', '');");
        $_response->addScript(bo_fwkpopin::add_close_script());
        
        return $_response->getXML();
    }

}
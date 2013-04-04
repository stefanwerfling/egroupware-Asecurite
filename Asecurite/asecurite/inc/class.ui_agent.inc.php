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
        $this->width = 600;
        $this->height = 550;
        $this->type_contrat = array(
            '' => lang('Choisissez ...'),
            'CDD' => 'CDD',
            'CDI' => 'CDI',
        );
        $this->current_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_agent.index'));
    }

    /**
     * Display the application home content
     */
    public function index($content = NULL) {
        $this->createHeader();
        $t = & CreateObject('phpgwapi.Template', EGW_APP_TPL);
        $t->set_file(array(
            'T_agents' => 'agents.tpl'
        ));
        $t->set_block('T_agents', 'agents');

        $msg = get_var('msg', array('GET'));
        $save = get_var('save', array('GET'));
        $add_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_agent.redirect_to_edit'));
        $data_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_agent.get_data'));
        $delete_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_agent.delete_agent'));

        $t->set_var('ADD_LINK', $add_link);
        $t->set_var('WIDTH', $this->width);
        $t->set_var('HEIGHT', $this->height);
        $t->set_var('DATA_LINK', $data_link);
        $t->set_var('MSG', "<span id=\"$save\">" . lang($msg) . " </span>");
        $t->set_var('DELETE_LINK', $delete_link);
        $t->set_var('INDEX_LINK', $this->current_link);
        $t->set_var('DELETE_BUTTON', $this->html->image(APP_NAME, 'delete', lang('Supprimer les agents sélectionnés?')));
        $t->set_var('SELECT_ALL', $this->html->image(APP_NAME, 'arrow_ltr', lang('Tout cocher/décocher'), 'onclick="check_all(); return false;"'));
        $t->pparse('out', 'agents');
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
                $edit_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_agent.edit', 'id' => $id));
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
                $agent_info_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_agent.get_agent_info', 'id' => $id));
                $agent_info = $this->html->image(APP_NAME, 'view', lang("Afficher les infos"), 'style="cursor:pointer" onclick="egw_openWindowCentered2(\'' . $agent_info_link . '\', \'_blank\', 450, 400, \'yes\'); return false;"');
                $row['piece_identite'] .= '&nbsp; ' . $agent_info;
                $row['operation'] = '<span style="float:right">';
                $row['operation'] .= $this->html->image(APP_NAME, 'edit', lang("Modifier l'agent"), 'style="cursor:pointer" onclick="egw_openWindowCentered2(\'' . $edit_link . '\', \'_blank\', '. $this->width. ', '. $this->height. ', \'yes\'); return false;"');
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

        /* $t = & CreateObject('phpgwapi.Template', EGW_APP_TPL);
          $t->set_file(array(
          'T_info_agent' => 'info_agent.tpl'
          ));
          $t->set_block('T_info_agent', 'info_agent'); */

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

                //$f_agent[0] = array_map(array('bo_asecurite', 'convert_to_html'), $f_agent[0]);
                /* $t->set_var('agent_name', $f_agent[0]['nom'] . ' ' . $f_agent[0]['prenom']);
                  $t->set_var('email', $f_agent[0]['email']);
                  $t->set_var('date_naissance', $f_agent[0]['date_naissance']);
                  $t->set_var('adresse', $f_agent[0]['adresse'] . ' ' . $f_agent[0]['code_postal'] . ', ' . $f_agent[0]['idasecurite_ville']);
                  $t->set_var('telephone', $f_agent[0]['telephone']);
                  $t->set_var('type_contrat', $f_agent[0]['type_contrat']);
                  $t->set_var('date_debut_contrat', $f_agent[0]['date_debut_contrat']);
                  $t->set_var('date_fin_contrat', $f_agent[0]['date_fin_contrat']);
                  $t->set_var('type_piece_identite', $f_agent[0]['type_piece_identite']);
                  $t->set_var('numero_piece_identite', $f_agent[0]['numero_piece_identite']);
                  $t->set_var('date_debut_piece_identite', $f_agent[0]['date_debut_piece_identite']);
                  $t->set_var('date_fin_piece_identite', $f_agent[0]['date_fin_piece_identite']);
                  $t->set_var('commune_piece_identite', $f_agent[0]['commune_piece_identite']);
                  $t->set_var('pays_piece_identite', $f_agent[0]['pays_piece_identite']); */

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

}
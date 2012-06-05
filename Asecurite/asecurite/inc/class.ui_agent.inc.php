<?php

/**
 * <b>File class.ui_agent.inc.php</b>
 * asecurite's agent user interface
 * @author N'faly KABA
 * @since   2/08/2011
 * @version 1.0
 * @copyright KABANFALY
 * @package egroupware
 * @subpackage asecurite/inc/
 * @filesource  class.ui_agent.inc.php
 */
include_once(EGW_INCLUDE_ROOT . '/asecurite/inc/class.bo_agent.inc.php');

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
        
        $t->set_var('ADD_LINK', $add_link, $tpl_content);
        $t->set_var('SCRIPT_JS', EGW_INCLUDE_ROOT . '/' . APP_NAME . '/js/dataTables/script.js');
        $t->set_var('DATA_LINK', $data_link, $tpl_content);
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
        foreach ($rows as $i => &$row) {
            $f_city_name = $this->search(array('idasecurite_ville' => $row['idasecurite_ville']), false);
            if (count($f_city_name) == 1) {
                $row['idasecurite_ville'] = '<span id="ville">'.$f_city_name[0]['nom'].'</span>';
            }
            $id = $row['idasecurite_agent'];
            $row['date_debut_contrat'] = $row['date_debut_contrat'] == '' ? '--' : $this->format_date($row['date_debut_contrat']);
            $row['date_fin_contrat'] = $row['date_fin_contrat'] == '' ? '--' : $this->format_date($row['date_fin_contrat']);

            $planning_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_horaires_agent.index', 'id' => $id, 'current' => 'true'));
            $edit_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_agent.edit', 'id' => $id));
            $delete_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_agent.delete_agent'));
            $row['nom'] = '<span style="cursor:pointer; color:blue;" onclick="egw_openWindowCentered2(\'' . $planning_link . '\', \'_blank\', 1000, 700, \'yes\'); return false;">' . $row['nom'] . ' ' . $row['prenom'] . '</span>';

            $row['operation'] = '<span style="float:right">';
            $row['operation'] .= $this->html->image(APP_NAME, 'edit', lang("Modifier l'agent"), 'style="cursor:pointer" onclick="egw_openWindowCentered2(\'' . $edit_link . '\', \'_blank\', 450, 400, \'yes\'); return false;"');            
            $row['operation'] .='&nbsp;' . $this->html->image(APP_NAME, 'delete', lang("Supprimer l'agent"), 'style="cursor:pointer" id="'.$id.'" onclick="deleteElement(\'' . $id . '\', \''.lang('Voulez vous supprimer cet agent?').'\', \'' . $delete_link . '\', \'' . $this->current_link . '\' );"');
            $row['operation'] .= '&nbsp;' . $this->html->input('checkbox[' . $id . ']', $id, 'checkbox', 'id="checkbox[' . $id . ']"') . '</span>';

            $output['aaData'][] = $rows[$i];
        }
        $return = json_encode($output);
        echo $return;
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
        );
        if (!$this->cities) {
            $js = "opener.location.href='" . ($link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_ville.index'))) . "';window.close();";

            $content['no_ville_msg'] = "<span id='error'>" . lang('Aucune ville trouvée') . ' <a><button onclick="' . $js . '">' . lang('Créer en ici') . '</button></a>' . " </span>";
        }
        parent::edit($content, $no_button, 'idasecurite_agent', 'Agent', 'egw_asecurite_agent', array('nom', 'prenom', 'date_naissance', 'adresse', 'code_postal', 'idasecurite_ville', 'type_contrat', 'telephone', 'date_debut_contrat', 'date_fin_contrat'), array('menuaction' => APP_NAME . '.ui_agent.index'));
        $this->tmpl->read(APP_NAME . '.agent.edit');
        $this->tmpl->exec(APP_NAME . '.ui_agent.edit', $content, $sel_options, $no_button, '', 2);
    }

}
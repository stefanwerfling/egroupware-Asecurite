<?php

/**
 * <b>File class.ui_asecurite.inc.php</b>
 * asecurite's business-object
 * @author N'faly KABA
 * @since   2/08/2011
 * @version 2.0
 * @copyright KABANFALY
 * @package egroupware
 * @subpackage asecurite/inc/
 * @filesource  class.ui_asecurite.inc.php
 */
include_once(EGW_INCLUDE_ROOT . '/asecurite/inc/class.bo_asecurite.inc.php');

class ui_asecurite extends bo_asecurite {

    var $type_contrat = array();

    /**
     * Public functions callable via menuaction
     *
     * @var array
     */
    var $public_functions = array(
        'index' => True,
        'redirect_to_edit' => True,
        'delete_planning' => True,
        'edit' => True,
    );

    function __construct() {

        parent::__construct('egw_asecurite_agent');
        $this->init_template(lang('Agents management'));
        $this->type_contrat = array(
            '' => lang('Choisissez ...'),
            'CDD' => 'CDD',
            'CDI' => 'CDI',
        );
    }

    /**
     * Display the application home content
     */
    public function index($content=NULL) {
        $msg = get_var('msg', array('GET'));
        $save = get_var('save', array('GET'));
       

        if (isset($content['nm']['rows']['delete'])) {
            list($del) = each($content['nm']['rows']['delete']);

            $this->delete(array('idasecurite_agent' => $del));
        } elseif (isset($content['delete_selected'])) {

            for ($i = 0; $i < count($content['nm']['rows']['checkbox']); $i++) {

                $this->delete(array('idasecurite_agent' => $content['nm']['rows']['checkbox'][$i]));
            }
        }
        $content['msg'] = "<span id=\"$save\">" . lang($msg) . " </span>";

        $content['nm'] = $this->nm + array('get_rows' => APP_NAME . '.ui_' . APP_NAME . '.get_rows', 'order' => 'nom');
        $this->tmpl->read(APP_NAME . '.agent'); //APP_NAME defined in asecurite/inc/class.bo_asecurite.inc.php
        $this->tmpl->exec(APP_NAME . '.ui_asecurite.index', $content, '', $readonlys);
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
        $total = parent::get_rows($query, $rows, $readonlys);
        
        foreach ($rows as $i => &$row) {
          
            $row['date_debut_contrat'] = $row['date_debut_contrat'] == '' ? '--' :$this->format_date($row['date_debut_contrat']);
            $row['date_fin_contrat'] = $row['date_fin_contrat'] == '' ? '--' : $this->format_date($row['date_fin_contrat']);
            $row['nom'] = '<span style="cursor:pointer">' . $row['nom'] . ' ' . $row['prenom'] . '</span>';
            $row['adresse'] = $row['adresse'] . '<br>' . $row['code_postal'] . ' ' . $row['ville'];
        }

        return $total;
    }

    /**
     * redirect to edit page and unset session value in order to do add operation
     * @return void
     */
    function redirect_to_edit() {

        parent::redirect_to_edit('idasecurite_agent', array('menuaction' => APP_NAME . '.ui_asecurite.edit'));
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
   
        parent::edit($content, $no_button, 'idasecurite_agent', 'Agent', 'egw_asecurite_agent', array('nom', 'prenom', 'date_naissance', 'adresse', 'code_postal', 'ville', 'type_contrat', 'telephone', 'date_debut_contrat', 'date_fin_contrat'), array('menuaction' => APP_NAME . '.ui_asecurite.index'));

        $sel_options = array(
            'type_contrat' => $this->type_contrat,
        );
        $this->tmpl->read(APP_NAME . '.agent.edit');
        $this->tmpl->exec(APP_NAME . '.ui_asecurite.edit', $content, $sel_options, $no_button, '', 2);
    }   
    
}
<?php

/**
 * <b>File class.ui_ferie.inc.php</b>
 * asecurite's ferie user interface
 * @author N'faly KABA
 * @since   31/08/2011
 * @version 1.0
 * @copyright KABANFALY
 * @package egroupware
 * @subpackage asecurite/inc/
 * @filesource  class.ui_ferie.inc.php
 */
include_once(EGW_INCLUDE_ROOT . '/asecurite/inc/class.bo_ferie.inc.php');

class ui_ferie extends bo_ferie {

    /**
     * Public functions callable via menuaction
     *
     * @var array
     */
    var $public_functions = array(
        'index' => True,
        'redirect_to_edit' => True,
        'delete_jour_ferie' => True,
        'edit' => True,
        'get_data' => True
    );

    function __construct() {

        parent::__construct();
        $this->init_template(lang('Gestion des jours fériés'));
        $this->current_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_ferie.index'));
    }

    /**
     * Display the application home content
     */
    public function index() {
        $this->createHeader();
        $t = & CreateObject('phpgwapi.Template', EGW_APP_TPL);
        $t->set_file(array(
            'T_feries' => 'feries.tpl'
        ));
        $t->set_block('T_feries', 'feries');

        $msg = get_var('msg', array('GET'));
        $save = get_var('save', array('GET'));
        $add_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_ferie.redirect_to_edit'));
        $data_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_ferie.get_data'));
        $delete_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_ferie.delete_jour_ferie'));

        $t->set_var('ADD_LINK', $add_link);
        $t->set_var('DATA_LINK', $data_link);
        $t->set_var('MSG', "<span id=\"$save\">" . lang($msg) . " </span>");
        $t->set_var('DELETE_LINK', $delete_link);
        $t->set_var('INDEX_LINK', $this->current_link);
        $t->set_var('DELETE_BUTTON', $this->html->image(APP_NAME, 'delete', lang('Supprimer les jours sélectionnés?')));
        $t->set_var('SELECT_ALL', $this->html->image(APP_NAME, 'arrow_ltr', lang('Tout cocher/décocher'), 'onclick="check_all(); return false;"'));
        $t->pparse('out', 'feries');
    }

    /**
     * delete a bank holiday
     */
    public function delete_jour_ferie() {
        $id_ferie = get_var('id');
        if ($id_ferie !== '') {
            $explode = explode('-', $id_ferie);
            $count = count($explode);
            if ($count == 1) {
                parent::delete_jour_ferie($id_ferie);
            } else {
                for ($i = 0; $i < $count; $i++) {
                    parent::delete_jour_ferie($explode[$i]);
                }
            }
        }
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
            $row['jour'] = $this->datetime($row['jour'], false);
        }
        return $total;
    }

    public function get_data() {
        $rows = $this->search('', false);
        $output = array(
            "sEcho" => intval($_GET['sEcho']),
            "iTotalRecords" => count($rows),
            "iTotalDisplayRecords" => count($rows),
            "aaData" => array()
        );
        if ($rows) {
            foreach ($rows as &$row) {

                $id = $row['idasecurite_ferie'];
                $edit_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_ferie.edit', 'id' => $id));
                $delete_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_ferie.delete_jour_ferie'));
                $row['operation'] = '<span style="float:right">';
                $row['operation'] .= $this->html->image(APP_NAME, 'edit', lang("Modifier l'agent"), 'style="cursor:pointer" onclick="egw_openWindowCentered2(\'' . $edit_link . '\', \'_blank\', 350, 200, \'yes\'); return false;"');
                $row['operation'] .='&nbsp;' . $this->html->image(APP_NAME, 'delete', lang("Supprimer l'agent"), 'style="cursor:pointer" id="' . $id . '" onclick="deleteElement(\'' . $id . '\', \'' . lang('Voulez vous supprimer ce jour férie?') . '\', \'' . $delete_link . '\', \'' . $this->current_link . '\' );"');
                $row['operation'] .= '&nbsp;' . $this->html->input('checkbox[' . $id . ']', $id, 'checkbox', 'id="checkbox[' . $id . ']"') . '</span>';
                $row['jour'] = $this->datetime($row['jour'], false);
                $output['aaData'][] = $row;
            }
        }
        $return = json_encode($output);
        echo $return;
    }

    /**
     * redirect to edit page and unset session value in order to do add operation
     * @return void
     */
    function redirect_to_edit() {
        parent::redirect_to_edit('idasecurite_ferie', array('menuaction' => APP_NAME . '.ui_ferie.edit'));
    }

    /**
     * add or edit an ferie
     * @param int $content contains processing data
     * @return void
     */
    public function edit($content = NULL) {

        $content['title'] = 'Asecurite' . ' - ' . lang("Working feries management");

        parent::edit($content, $no_button, 'idasecurite_ferie', 'Bank holiday', 'egw_asecurite_ferie', array('jour', 'description'), array('menuaction' => APP_NAME . '.ui_ferie.index'));

        $this->tmpl->read(APP_NAME . '.ferie.edit');
        $this->tmpl->exec(APP_NAME . '.ui_ferie.edit', $content, $sel_options, $no_button, '', 2);
    }

}
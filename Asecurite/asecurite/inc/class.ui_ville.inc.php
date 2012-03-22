<?php

/**
 * <b>File class.ui_ville.inc.php</b>
 * asecurite's ville user interface
 * @author N'faly KABA
 * @since   26/08/2011
 * @version 1.0
 * @copyright KABANFALY
 * @package egroupware
 * @subpackage asecurite/inc/
 * @filesource  class.ui_ville.inc.php
 */
include_once(EGW_INCLUDE_ROOT . '/asecurite/inc/class.bo_ville.inc.php');

class ui_ville extends bo_ville {

    /**
     * Public functions callable via menuaction
     *
     * @var array
     */
    var $public_functions = array(
        'index' => True,
        'redirect_to_edit' => True,
        'edit' => True,
        'delete_ville' => True,
        'get_data' => True
    );

    function __construct() {

        parent::__construct();
        $this->init_template(lang('Gestion des villes de travail'));
        $this->current_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_ville.index'));
    }

    /**
     * Display the application home content
     */
    public function index() {
        $this->createHeader();

        $msg = get_var('msg', array('GET'));
        $save = get_var('save', array('GET'));
        $add_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_ville.redirect_to_edit'));
        $data_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_ville.get_data'));
        $delete_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_ville.delete_ville'));
        $tpl_content = file_get_contents(EGW_INCLUDE_ROOT . '/' . APP_NAME . '/templates/villes.html');
        $tpl_content = str_replace('ADD_LINK', $add_link, $tpl_content);
        $tpl_content = str_replace('SCRIPT_JS', EGW_INCLUDE_ROOT . '/' . APP_NAME . '/js/dataTables/script.js', $tpl_content);
        $tpl_content = str_replace('DATA_LINK', $data_link, $tpl_content);
        $tpl_content = str_replace('MSG', "<span id=\"$save\">" . lang($msg) . " </span>", $tpl_content);
        $tpl_content = str_replace('DELETE_LINK', $delete_link, $tpl_content);
        $tpl_content = str_replace('INDEX_LINK', $this->current_link, $tpl_content);
        $tpl_content = str_replace('DELETE_BUTTON', $this->html->image(APP_NAME, 'delete', lang('Voulez vous supprimer les villes sélectionnées?')), $tpl_content);
        $tpl_content = str_replace('SELECT_ALL', $this->html->image(APP_NAME, 'arrow_ltr', lang('Tout cocher/décocher'), 'onclick="check_all(); return false;"'), $tpl_content);
        echo $tpl_content;      
    }

    /**
     * delete a city
     */
    public function delete_ville() {
        $id_ville = get_var('id');
        if ($id_ville !== '') {
            $explode = explode('-', $id_ville);
            $count = count($explode);
            if ($count == 1) {
                parent::delete_ville($id_ville);
            } else {
                for ($i = 0; $i < $count; $i++) {
                    parent::delete_ville($explode[$i]);
                }
            }
        }
    }

    /**
     * get all cities to display 
     */
    public function get_data() {
        $rows = $this->search('', false);
        $output = array(
            "sEcho" => intval($_GET['sEcho']),
            "iTotalRecords" => count($rows),
            "iTotalDisplayRecords" => count($rows),
            "aaData" => array()
        );
        foreach ($rows as $i => &$row) {

            $id = $row['idasecurite_ville'];
            $planning_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_horaires_ville.index', 'id' => $id, 'current' => true));
            $edit_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_ville.edit', 'id' => $id));
            $delete_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_ville.delete_ville', 'id' => $id));
            $row['nom'] = '<span style="cursor:pointer; color:blue;" onclick="egw_openWindowCentered2(\'' . $planning_link . '\', \'_blank\', 1000, 700, \'yes\'); return false;">' . $row['nom']. '</span>';

            $row['operation'] = '<span style="float:right">';
            $row['operation'] .= $this->html->image(APP_NAME, 'edit', lang("Modifier la ville"), 'style="cursor:pointer" onclick="egw_openWindowCentered2(\'' . $edit_link . '\', \'_blank\', 400, 150, \'yes\'); return false;"');
            $row['operation'] .='&nbsp;' . $this->html->image(APP_NAME, 'delete', lang("Supprimer la ville"), 'style="cursor:pointer" onclick="deleteElement(\'' . $id . '\', \''.lang('Voulez vous supprimer cette ville?').'\', \'' . $delete_link . '\', \'' . $this->current_link . '\' );"');
            $row['operation'] .= '&nbsp;' . $this->html->input('checkbox[' . $id . ']', $id, 'checkbox', 'id="checkbox[' . $id . ']"') . '</span>';

            $output['aaData'][] = $rows[$i];
        }
        $return = json_encode($output);
        echo $return;
        
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
        return $total;
    }

    /**
     * redirect to edit page and unset session value in order to do add operation
     * @return void
     */
    function redirect_to_edit() {

        parent::redirect_to_edit('idasecurite_ville', array('menuaction' => APP_NAME . '.ui_ville.edit'));
    }

    /**
     * add or edit an ville
     * @param int $content contains processing data
     * @return void
     */
    public function edit($content = NULL) {

        $content['title'] = 'Asecurite' . ' - ' . lang("Working villes management");

        parent::edit($content, $no_button, 'idasecurite_ville', 'City', 'egw_asecurite_ville', array('nom'), array('menuaction' => APP_NAME . '.ui_ville.index'));

        $this->tmpl->read(APP_NAME . '.ville.edit');
        $this->tmpl->exec(APP_NAME . '.ui_ville.edit', $content, '', $no_button, '', 2);
    }

}
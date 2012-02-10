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
    );

    function __construct() {

        parent::__construct();
        $this->init_template(lang('Gestion des villes de travail'));
    }

    /**
     * Display the application home content
     */
    public function index($content = NULL) {
        $msg = get_var('msg', array('GET'));
        $save = get_var('save', array('GET'));

        if (isset($content['nm']['rows']['delete'])) {
            list($id_ville) = each($content['nm']['rows']['delete']);
            try {
                $this->delete_ville($id_ville);
            } catch (Exception $e) {
                $msg = $e->getMessage();
                $save = 'error';
            }
        } elseif (isset($content['delete_selected'])) {

            for ($i = 0; $i < count($content['nm']['rows']['checkbox']); $i++) {
                try {
                    $this->delete_ville($content['nm']['rows']['checkbox'][$i]);
                } catch (Exception $e) {
                    $msg = $e->getMessage();
                    $save = 'error';
                }
            }
        }
        $this->setup_table(APP_NAME, 'egw_asecurite_ville');
        $content['msg'] = "<span id=\"$save\">" . lang($msg) . " </span>";
        $content['nm'] = $this->nm + array('get_rows' => APP_NAME . '.ui_ville' . '.get_rows', 'order' => 'nom');
        $select_option = array(
            'idasecurite_ville' => $this->cities,
        );
        $readonlys['nm']['export'] = true;
        $this->tmpl->read(APP_NAME . '.ville'); //APP_NAME defined in asecurite/inc/class.bo_asecurite.inc.php
        $this->tmpl->exec(APP_NAME . '.ui_ville.index', $content, $select_option, $readonlys);
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
        $this->tmpl->exec(APP_NAME . '.ui_ville.edit', $content, $sel_options, $no_button, '', 2);
    }

}
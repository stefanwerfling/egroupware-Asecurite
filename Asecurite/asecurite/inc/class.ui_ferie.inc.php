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
        'edit' => True,
    );

    function __construct() {

        parent::__construct();
        $this->init_template(lang('Gestion des jours fériés'));
    }

    /**
     * Display the application home content
     */
    public function index($content = NULL) {
        $msg = get_var('msg', array('GET'));
        $save = get_var('save', array('GET'));

        if (isset($content['nm']['rows']['delete'])) {
            list($del) = each($content['nm']['rows']['delete']);

            $this->delete(array('idasecurite_ferie' => $del));
        } elseif (isset($content['delete_selected'])) {

            for ($i = 0; $i < count($content['nm']['rows']['checkbox']); $i++) {

                $this->delete(array('idasecurite_ferie' => $content['nm']['rows']['checkbox'][$i]));
            }
        }

        $content['msg'] = "<span id=\"$save\">" . lang($msg) . " </span>";

        $content['nm'] = $this->nm + array('get_rows' => APP_NAME . '.ui_ferie' . '.get_rows', 'order' => 'jour');
        $select_option = array(
            'idasecurite_ferie' => $this->cities,
        );
        $this->tmpl->read(APP_NAME . '.ferie'); //APP_NAME defined in asecurite/inc/class.bo_asecurite.inc.php
        $this->tmpl->exec(APP_NAME . '.ui_ferie.index', $content, $select_option, $readonlys);
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
        foreach ($rows as $i => &$row){
            $row['jour'] = $this->datetime($row['jour'], false);
        }
        return $total;
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
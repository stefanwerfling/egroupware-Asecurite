<?php

/**
 * <b>File class.ui_site.inc.php</b>
 * asecurite's site user interface
 * @author N'faly KABA
 * @since   6/08/2011
 * @version 1.0
 * @copyright KABANFALY
 * @package egroupware
 * @subpackage asecurite/inc/
 * @filesource  class.ui_site.inc.php
 */
include_once(EGW_INCLUDE_ROOT . '/asecurite/inc/class.bo_site.inc.php');

class ui_site extends bo_site {

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
        $this->init_template(lang('Gestion des sites de travail'));
    }

    /**
     * Display the application home content
     */
    public function index($content = NULL) {
        $msg = get_var('msg', array('GET'));
        $save = get_var('save', array('GET'));

        if (isset($content['nm']['rows']['delete'])) {
            list($id_site) = each($content['nm']['rows']['delete']);
            try {
                $this->delete_site($id_site);
            } catch (Exception $e) {
                $msg = $e->getMessage();
                $save = 'error';
            }
        } elseif (isset($content['delete_selected'])) {

            for ($i = 0; $i < count($content['nm']['rows']['checkbox']); $i++) {
                try {
                    $this->delete_site($content['nm']['rows']['checkbox'][$i]);
                } catch (Exception $e) {
                    $msg = $e->getMessage();
                    $save = 'error';
                }
            }
        }
        $this->setup_table(APP_NAME, 'egw_asecurite_site');
        $content['msg'] = "<span id=\"$save\">" . lang($msg) . " </span>";
        $readonlys['nm']['export'] = true;
        $content['nm'] = $this->nm + array('get_rows' => APP_NAME . '.ui_site' . '.get_rows', 'order' => 'nom');
        $this->tmpl->read(APP_NAME . '.site'); //APP_NAME defined in asecurite/inc/class.bo_asecurite.inc.php
        $this->tmpl->exec(APP_NAME . '.ui_site.index', $content, '', $readonlys);
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

        $this->setup_table(APP_NAME, 'egw_asecurite_ville');

        foreach ($rows as $i => &$row) {
            $f_city_name = $this->search(array('idasecurite_ville' => $row['idasecurite_ville']), false);

            if (count($f_city_name) == 1) {
                $row['idasecurite_ville'] = $f_city_name[0]['nom'];
            }
        }


        return $total;
    }

    /**
     * redirect to edit page and unset session value in order to do add operation
     * @return void
     */
    function redirect_to_edit() {

        parent::redirect_to_edit('idasecurite_site', array('menuaction' => APP_NAME . '.ui_site.edit'));
    }

    /**
     * add or edit an site
     * @param int $content contains processing data
     * @return void
     */
    public function edit($content = NULL) {

        $content['title'] = 'Asecurite' . ' - ' . lang("Working sites management");

        $sel_options = array(
            'idasecurite_ville' => $this->cities,
        );

        if (!$this->cities) {
            $js = "opener.location.href='" . ($link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_ville.index'))) . "';window.close();";

            $content['no_ville_msg'] = "<span id='error'>" . lang('Aucune ville trouvée') . ' <a><button onclick="' . $js . '">' . lang('Créer en ici') . '</button></a>' . " </span>";
        }
        parent::edit($content, $no_button, 'idasecurite_site', 'site', 'egw_asecurite_site', array('nom', 'prenom', 'adresse', 'code_postal', 'idasecurite_ville', 'telephone'), array('menuaction' => APP_NAME . '.ui_site.index'));

        $this->tmpl->read(APP_NAME . '.site.edit');
        $this->tmpl->exec(APP_NAME . '.ui_site.edit', $content, $sel_options, $no_button, '', 2);
    }

}
<?php

/**
 * <b>File class.ui_site.inc.php</b>
 * asecurite's site user interface
 * @author N'faly KABA
 * @since   6/08/2011
 * @version 2.0
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
        'get_data' => True,
        'delete_site' => True
    );

    function __construct() {

        parent::__construct();
        $this->init_template(lang('Gestion des sites de travail'));
        $this->current_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_site.index'));
    }

    /**
     * Display the application home content
     */
    public function index() {
        $this->createHeader();
        $t = & CreateObject('phpgwapi.Template', EGW_APP_TPL);
        $t->set_file(array(
            'T_sites' => 'sites.tpl'
        ));
        $t->set_block('T_sites', 'sites');

        $msg = get_var('msg', array('GET'));
        $save = get_var('save', array('GET'));
        $add_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_site.redirect_to_edit'));
        $data_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_site.get_data'));
        $delete_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_site.delete_site'));

        $t->set_var('ADD_LINK', $add_link);
        $t->set_var('DATA_LINK', $data_link);
        $t->set_var('MSG', "<span id=\"$save\">" . lang($msg) . " </span>");
        $t->set_var('DELETE_LINK', $delete_link);
        $t->set_var('INDEX_LINK', $this->current_link);
        $t->set_var('DELETE_BUTTON', $this->html->image(APP_NAME, 'delete', lang('Voulez vous supprimer les sites sélectionnés?')));
        $t->set_var('SELECT_ALL', $this->html->image(APP_NAME, 'arrow_ltr', lang('Tout cocher/décocher'), 'onclick="check_all(); return false;"'));
        $t->pparse('out', 'sites');
    }

    /**
     * delete a site
     */
    public function delete_site() {
        $id_site = get_var('id');
        if ($id_site !== '') {
            $explode = explode('-', $id_site);
            $count = count($explode);
            if ($count == 1) {
                parent::delete_site($id_site);
            } else {
                for ($i = 0; $i < $count; $i++) {
                    parent::delete_site($explode[$i]);
                }
            }
        }
    }

    /**
     * get all sites to display 
     */
    public function get_data() {
        $rows = $this->search('', false, 'idasecurite_site DESC');
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
                $id = $row['idasecurite_site'];

                $planning_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_horaires_site.index', 'id' => $id, 'current' => 'true'));
                $edit_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_site.edit', 'id' => $id));
                $delete_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_site.delete_site'));
                $row['nom'] = '<span style="cursor:pointer; color:blue;" onclick="egw_openWindowCentered2(\'' . $planning_link . '\', \'_blank\', 1000, 700, \'yes\'); return false;">' . $row['nom'] . '</span>';

                $row['operation'] = '<span style="float:right">';
                $row['operation'] .= $this->html->image(APP_NAME, 'edit', lang("Modifier le site"), 'style="cursor:pointer" onclick="egw_openWindowCentered2(\'' . $edit_link . '\', \'_blank\', 450, 400, \'yes\'); return false;"');
                $row['operation'] .='&nbsp;' . $this->html->image(APP_NAME, 'delete', lang("Supprimer le site"), 'style="cursor:pointer" id="' . $id . '" onclick="deleteElement(\'' . $id . '\', \'' . lang('Voulez vous supprimer ce site?') . '\', \'' . $delete_link . '\', \'' . $this->current_link . '\' );"');
                $row['operation'] .= '&nbsp;' . $this->html->input('checkbox[' . $id . ']', $id, 'checkbox', 'id="checkbox[' . $id . ']"') . '</span>';

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
            $js = "opener.location.href='" . ($link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_site.index'))) . "';window.close();";

            $content['no_ville_msg'] = "<span id='error'>" . lang('Aucune ville trouvée') . ' <a><button onclick="' . $js . '">' . lang('Créer en ici') . '</button></a>' . " </span>";
        }
        parent::edit($content, $no_button, 'idasecurite_site', 'site', 'egw_asecurite_site', array('nom', 'prenom', 'adresse', 'code_postal', 'idasecurite_ville', 'telephone'), array('menuaction' => APP_NAME . '.ui_site.index'));

        $this->tmpl->read(APP_NAME . '.site.edit');
        $this->tmpl->exec(APP_NAME . '.ui_site.edit', $content, $sel_options, $no_button, '', 2);
    }

}
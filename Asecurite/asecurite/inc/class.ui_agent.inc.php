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
        'get_stream_data' => True,
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
    }

    /**
     * Display the application home content
     */
    public function index($content = NULL) {
        $msg = get_var('msg', array('GET'));
        $save = get_var('save', array('GET'));


        if (isset($content['nm']['rows']['delete'])) {
            list($id_agent) = each($content['nm']['rows']['delete']);
            try {
                $this->delete_agent($id_agent);               
            } catch (Exception $e) {
                $msg = $e->getMessage();
                $save = 'error';
            }
        } elseif (isset($content['delete_selected'])) {
            for ($i = 0; $i < count($content['nm']['rows']['checkbox']); $i++) {
                try {
                    $this->delete_agent($content['nm']['rows']['checkbox'][$i]);
                } catch (Exception $e) {
                    $msg = $e->getMessage();
                    $save = 'error';
                }
            }
        }
        $data_link = $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_agent.get_data'));        
        $tpl_content = file_get_contents(EGW_INCLUDE_ROOT . '/' . APP_NAME . '/templates/agents.html');
        $tpl_content = str_replace('LINK', $data_link, $tpl_content);
        $content['data'] = $tpl_content;
       
        $this->setup_table(APP_NAME, 'egw_asecurite_agent');
        $content['msg'] = "<span id=\"$save\">" . lang($msg) . " </span>";
        $readonlys['nm']['export'] = true;
       // $content['nm'] = $this->nm + array('get_rows' => APP_NAME . '.ui_agent.get_rows', 'order' => 'nom');
        $this->tmpl->read(APP_NAME . '.agent'); //APP_NAME defined in asecurite/inc/class.bo_agent.inc.php
        $this->tmpl->exec(APP_NAME . '.ui_agent.index', $content, '', $readonlys);
    }

    /**
     * get a file as a stream
     * @param string $file filepath
     * @throws Exception if file's not found
     */
    function get_stream_data($file) {
        $file = get_var('file');
        if (!file_exists($file))
            throw new Exception("File not found");
        ob_end_clean();
        header("Content-type:text/html;charset=utf-8");
        header("Content-Transfer-Encoding: binary");
        header('Pragma: no-cache');
        header('Expires: 0');
        echo file_get_contents($file);
        exit;
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
            $row['date_debut_contrat'] = $row['date_debut_contrat'] == '' ? '--' : $this->format_date($row['date_debut_contrat']);
            $row['date_fin_contrat'] = $row['date_fin_contrat'] == '' ? '--' : $this->format_date($row['date_fin_contrat']);
            $row['nom'] = '<span style="cursor:pointer">' . $row['nom'] . ' ' . $row['prenom'] . '</span>';           
        }

        return $total;
    }
    /**
     * get all agent to display 
     */
    public function get_data(){
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
                $row['idasecurite_ville'] = $f_city_name[0]['nom'];
            }
            $id = $row['idasecurite_agent'];
            $row['date_debut_contrat'] = $row['date_debut_contrat'] == '' ? '--' : $this->format_date($row['date_debut_contrat']);
            $row['date_fin_contrat'] = $row['date_fin_contrat'] == '' ? '--' : $this->format_date($row['date_fin_contrat']);
            
            $planning_link= $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_horaires_agent.index', 'id' => $id));      
            $row['nom'] = '<span style="cursor:pointer">' . $row['nom'] . ' ' . $row['prenom'] . '</span>';
            $row['nom'] .= '<a href="" onclick="egw_openWindowCentered2(\''.$planning_link.'\', \'_blank\', 1000, 700, \'yes\'); return false;"><img style="float:right" src="'.$GLOBALS['egw']->common->image(APP_NAME, 'view').'" border="0" title="'.lang("Afficher le planning de l'agent").'"></a>';
            
            $edit_link= $GLOBALS['egw']->link('/index.php', array('menuaction' => APP_NAME . '.ui_agent.edit', 'id' => $id));      
            $row['operation'] = '<span style="float:right">';            
            $row['operation'] .= '<a href="" onclick="egw_openWindowCentered2(\''.$edit_link.'\', \'_blank\', 450, 400, \'yes\'); return false;"><img src="'.$GLOBALS['egw']->common->image(APP_NAME, 'edit').'" border="0" title="'.lang("Modifier l'agent").'"></a>';            
            $row['operation'] .= '&nbsp;<a href="" onclick="if ( confirm(\''.lang('Voulez vous supprimer cet agent?').'\')) return submitit(eTemplate,\'exec[nm][rows][delete][' . $id . ']\');; return false;" id="exec[nm][rows][delete][' . $id . ']"><img src="'.$GLOBALS['egw']->common->image(APP_NAME, 'delete').'" border="0" title="'.lang("Supprimer l'agent").'"></a>';            
            $row['operation'] .= '&nbsp;<input type="checkbox" name="exec[nm][rows][checkbox][]" value="' . $id . '" id="exec[nm][rows][checkbox][' . $id . ']"></span>';
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
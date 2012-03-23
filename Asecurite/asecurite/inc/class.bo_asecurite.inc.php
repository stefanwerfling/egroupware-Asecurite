<?php

/**
 * <b>File class.bo_asecurite.inc.php</b>
 * asecurite's business-object
 * @author N'faly KABA
 * @since   2/08/2011
 * @version 1.0
 * @copyright KABANFALY
 * @package egroupware
 * @subpackage asecurite/inc/
 * @filesource  class.bo_asecurite.inc.php
 */
if (!defined('APP_NAME')) {
    define('APP_NAME', 'asecurite');
}

include_once(EGW_INCLUDE_ROOT . '/asecurite/inc/class.bo_tools.inc.php');

class bo_asecurite extends so_sql {

    /**
     * current template
     * @var string
     */
    public $tmpl;

    /**
     * script JS (javaScript) 
     * @var string
     */
    public $js_content;

    /**
     * Image directory to display icons
     * @var string
     */
    public $nm;

    /**
     * years to choose
     * @var array
     */
    var $years = array();

    /**
     * monthes to choose
     * @var array
     */
    var $monthes = array();

    /**
     * working sites
     * @var array
     */
    var $sites = array();

    /**
     * all agents
     * @var array 
     */
    var $agents = array();

    /**
     * all cities
     * @var array 
     */
    var $cities = array();

    /**
     * current month
     * @var int 
     */
    var $current_month;

    /**
     * current year
     * @var int
     */
    var $current_year;
    var $img_src;
    var $nb_baskets = 0;
    var $current_link;
    /**
     * Constructor
     * @param string default table name
     */
    public function __construct($table = '') {

        parent::__construct(APP_NAME, $table);

        //$_main_conf = bo_var_setting::get_all_conf_var();

        $this->nm = array(
            //'get_rows' => 'asecurite.ui_grouptype.get_rows', // I  method/callback to request the data for the rows eg. 'notes.bo.get_rows'
            'no_filter' => True, // I  disable the 1. filter
            'no_filter2' => True, // I  disable the 2. filter (params are the same as for filter)
            'no_cat' => True, // I  disable the cat-selectbox
            'never_hide' => True, // I  never hide the nextmatch-line if less then maxmatch entrie
            'lettersearch' => False, // I  show a lettersearch
            'searchletter' => '', // I0 active letter of the lettersearch or false for [all]
            //'order' => 'weight', // IO name of the column to sort after (optional for the sortheaders)
            'sort' => 'DESC', // IO direction of the sort: 'ASC' or 'DESC'
            'col_filter' => array(), // IO array of column-name value pairs (optional for the filterheaders)
        );


        $this->monthes = array(
            0 => lang('Tous les mois'),
            1 => lang('Janvier'),
            2 => lang('Février'),
            3 => lang('Mars'),
            4 => lang('Avril'),
            5 => lang('Mai'),
            6 => lang('Juin'),
            7 => lang('Juillet'),
            8 => lang('Août'),
            9 => lang('Septembre'),
            10 => lang('Octobre'),
            11 => lang('Novembre'),
            12 => lang('Décembre'),
        );

        $this->current_year = intval(date('Y'));
        $this->current_month = intval(date('m'));

        $this->years[0] = lang('Toutes les années');

        for ($i = 2011; $i <= $this->current_year + 1; $i++) {

            $this->years[$i] = $i;
        }
        $this->setup_table(APP_NAME, 'egw_asecurite_site');

        $f_sites = $this->search('', false);

        if ($f_sites) {
            $this->sites[''] = lang('Tous les sites');

            foreach ($f_sites as $key => $value) {

                $this->sites[$value['idasecurite_site']] = $value['nom'];
            }
        }
        $this->setup_table(APP_NAME, 'egw_asecurite_agent');

        $f_agents = $this->search('', false);

        if ($f_agents) {
            $this->agents[''] = lang('Tous les agents');

            foreach ($f_agents as $key => $value) {

                $this->agents[$value['idasecurite_agent']] = $value['nom'] . ' ' . $value['prenom'];
            }
        }
        $this->setup_table(APP_NAME, 'egw_asecurite_ville');

        $f_cities = $this->search('', false);

        if ($f_cities) {
            $this->cities[''] = lang('Toutes les villes');

            foreach ($f_cities as $key => $value) {

                $this->cities[$value['idasecurite_ville']] = $value['nom'];
            }
        }

        $this->setup_table(APP_NAME, $table);
        $this->img_src = $GLOBALS['egw']->accounts->config['webserver_url'] . '/' . APP_NAME . '/templates/images';
    }

    /**
     * Set up default table
     * @see so_sql::setup_table()
     */
    public function setup_table($app, $table) {
        parent::setup_table($app, $table);
        $this->init();
    }

    /**
     * construct objects for user interface
     * @param string page name 
     */
    public function init_template($pagename = "") {
        if ($pagename != "")
            $pagename = " - " . $pagename;
        $GLOBALS['egw_info']['flags']['app_header'] = lang(APP_NAME) . $pagename;

        $this->tmpl = new etemplate();

        $this->html = & $GLOBALS['egw']->html;
        if (!@is_object($GLOBALS['egw']->js)) {
            $GLOBALS['egw']->js = & CreateObject('phpgwapi.javascript');
        }
        //javascript files
        $GLOBALS['egw']->js->files = array(
            APP_NAME => array(
                '' => array(
                    'app' => '', // file name: asecurite/js/app.js
                    'jquery-1.7' => '', // file name: asecurite/js/jquery-1.5.min.js
                    'tooltip' => '', // file name: asecurite/js/jquery.js
                    'dataTables' => 'dataTables', // file name: asecurite/js//dataTables/dataTables.js
                    'jscharts' => '', // file name: asecurite/js/jscharts.js
                    'flexigrid' => 'flexigrid', // file name: asecurite/js/flexigrid/flexigrid.js
                    'thickbox' => 'thickbox', // file name: asecurite/js/flexigrid/flexigrid.js
            )));
        $GLOBALS['egw_info']['flags']['include_xajax'] = true;
    }

    /**
     * Return date+time formatted for the currently notified user (prefs in $GLOBALS['egw_info']['user']['preferences'])
     *
     * @param int|string|DateTime $timestamp in server-time
     * @param boolean $do_time=true true=allways (default), false=never print the time, null=print time if != 00:00
     *
     * @return string
     */
    public function datetime($timestamp, $do_time = true) {
        if (!is_a($timestamp, 'DateTime')) {
            $timestamp = new egw_time($timestamp, egw_time::$server_timezone);
        }
        $timestamp->setTimezone(egw_time::$user_timezone);
        if ($do_time == null) {
            $do_time = ($timestamp->format('Hi') != '0000');
        }
        $format = $GLOBALS['egw_info']['user']['preferences']['common']['dateformat'];
        if ($do_time)
            $format .= ' ' . ($GLOBALS['egw_info']['user']['preferences']['common']['timeformat'] != 12 ? 'H:i' : 'h:i a');

        return $timestamp->format($format);
    }

    /**
     * Return date+time formatted for the currently notified user (prefs in $GLOBALS['egw_info']['user']['preferences'])
     *
     * @param int|string|DateTime $timestamp in server-time
     * @param boolean $do_time=true true=allways (default), false=never print the time, null=print time if != 00:00
     *
     * @return string
     */
    public function time($timestamp) {

        if (!is_a($timestamp, 'DateTime')) {
            $timestamp = new egw_time($timestamp, egw_time::$server_timezone);
        }
        $timestamp->setTimezone(egw_time::$user_timezone);

        $format = ($GLOBALS['egw_info']['user']['preferences']['common']['timeformat'] != 12 ? 'H:i' : 'h:i a');

        return $timestamp->format($format);
    }

    /**
     * convert time to seconde.      
     * @param int $hour hour
     * @param int $min minute
     * @return int converted time 
     */
    public function convert_time_to_sec($hour, $min) {

        /**
         * PHP date function return 01:00 if the second parameter (timestamp) egal to 0 i.e $hour=0. so we need an algorithm
         * in order to to display the user chosen hour from UI
         */
        switch ($hour) {
            case 0:
                $hour_to_sec = 0; // 1h
                break;
            case 1:
                $hour_to_sec = 3600; // 2h
                break;
            default :
                $hour_to_sec = 3600 * $hour;
        }
        $time = $hour_to_sec == 0 ? 0 : $hour_to_sec;

        if ($min != 0 && $time == 0) {

            $time = 60 * $min;
        } else {
            $time += 60 * $min;
        }

        return $time;
    }

    /**
     * Update  data
     * @param array $data update data
     * @param array $where i.e array('id' => 1)
     * @return ADORecordSet or false, if the query fails
     */
    public function update($data, $where) {
        return $this->db->update($this->table_name, $data, $where, __LINE__, __FILE__);
    }

    /**
     * Moves an element
     *
     * @param int $up_down the direction to move: -1 up, 1 down
     * @param int $id id of the grouptype
     * @param array $where where clause in sql query
     * @return void
     */
    public function move_up_down($up_down, $id, $where, $extra = '') {

        if ($this->read($id)) {
            foreach ($this->data as $db_col => $col) {
                $content[$db_col] = $col;
            }
            $weight = (int) $content['weight'] + $up_down;
            $newWeight = $weight <= 0 ? 1 : $weight;

            if (is_array($extra)) {
                $this->update(array('weight' => $content['weight']), array('weight' => $newWeight) + $extra);

                $content['weight'] = $newWeight;

                $this->update($content, $where + $extra);
            } else {
                $this->update(array('weight' => $content['weight']), array('weight' => $newWeight));

                $content['weight'] = $newWeight;

                $this->update($content, $where);
            }
        }
    }

    /**
     * save (or update) data to the given table into the database
     * @param string $name to custum the return message
     * @param string $table_name the table concerning by the saving or the update
     * @param array $data data to store or for update
     * @param array $col the table column names
     * @param string $msg message regarding to saving or updating
     * @param boolean $is_catalog=false if catalog is the current table
     * @param string|array $where='' the referer column for update
     * @return boolean true if save|update is OK else false
     */
    public function save_data($name, $table_name, $data, $col, &$msg, $where = '') {

        $this->setup_table(APP_NAME, $table_name);
        $return_value = false;

        foreach ($col as $c => $val) {
            $this->data[$val] = $data[$val];
        }

        if ($where == '') { // save
            if (isset($data['weight'])) {
                unset($this->data['weight']);
            }
            $found = $this->search($this->data, False); // searches by using the no-empty fields

            if (!$found) {
                if (isset($data['weight'])) {
                    $this->data['weight'] = $data['weight'];
                }
                if (!$this->save($this->data)) {
                    $msg = $name . ' entry saved';
                    $return_value = true;
                } else {
                    $msg = 'Error: while saving !!!';
                }
            } else {
                $msg = lang('This') . ' ' . lang($name) . ' ' . lang('already exists');
                return false;
            }
        } else {// update            
            if ($this->update($this->data, $where)) {
                $msg = $name . ' entry modified';
                $return_value = true;
            } else {
                $msg = 'update KO';
            }
        }
        return $return_value;
    }

    /**
     * redirect to edit page and unset session value in order to do add operation
     * @param string $session_name session to destroy
     * @param array $link_to_edit parameters to link to edit page
     * @return void
     */
    function redirect_to_edit($session_name, $link_to_edit) {

        $GLOBALS['egw']->session->appsession($session_name, APP_NAME, '');

        $GLOBALS['egw']->redirect_link('/index.php', $link_to_edit);
    }

    /**
     * edit or save an element (used from user interface)
     * @param array $content contains processing data
     * @param array $no_button with button-names as keys for buttons which should be readonly
     * @param string $pk primary key for the current table
     * @param string $name module name (ex: group, push_context, ...)
     * @param string $table_name table name to save or to update data
     * @param array $col the table column names
     * @param array $extra_param contains menuaction value (the redirect link value) and other value such as message
     * @return void
     */
    public function edit(&$content, &$no_button, $pk, $name, $table_name, $col, $extra_param) {

        $id = get_var('id', array('GET'));

        $save_ok = false;
        if ($id != '') {

            $GLOBALS['egw']->session->appsession($pk, APP_NAME, $id);

            if ($this->read($id)) {
                foreach ($this->data as $db_col => $col) {
                    $content[$db_col] = $col;
                }
            }
            $content['add_edit'] = '<span class="title">' . $content['title'] . '<span> - <span id="edit">Modifier</span>';
        } else {
            if ($id == '' && $GLOBALS['egw']->session->appsession($pk, APP_NAME)) {
                $content['add_edit'] = '<span class="title">' . $content['title'] . '<span> - <span id="edit">Modifier</span>';
            } else {
                $content['add_edit'] = '<span class="title">' . $content['title'] . '<span> - <span id="add">Ajouter</span>';
            }
        }


        if (isset($content['add'])) {
            $save_ok = $this->save_data($name, $table_name, $content, $col, $msg);
        } elseif (isset($content['edit'])) {

            $id = $GLOBALS['egw']->session->appsession($pk, APP_NAME);

            $save_ok = $this->save_data($name, $table_name, $content, $col, $msg, array($pk => $id));


            $content['add_edit'] = '<span id="edit">Modifier</span>';
        }

        if ($content['edit'] || $content['add']) {
            switch ($name) {
                case 'Group':
                    $extra_param['msg_group'] = $msg;
                    break;
                case 'Grouptype':
                    $extra_param['msg_grouptype'] = $msg;
                    break;
                case 'Context':
                    $extra_param['msg_context'] = $msg;
                    break;
                case 'Publish_servers':
                    $extra_param['msg_publish'] = $msg;
                    break;
                default :
                    $extra_param['msg'] = $msg;
            }
            $extra_param['save'] = $save_ok ? 'success' : 'error';
            self::close_popup($extra_param);
        }

        $no_button = array(
            'add' => $content[$pk],
            'edit' => !$content[$pk],
        );


        if ($content['weight'] == '') {
            $find_all = $this->search('', false);

            $content['weight'] = count($find_all) + 1;
        }
    }

    /**
     * edit or save a planning
     * @param array $content contains processing data
     * @param string $name module name (ex: site, agent, ...)
     * @param string $table_name table name to save or to update data
     * @param array $col the table column names
     * @param array $extra_param contains menuaction value (the redirect link value) and other value such as message
     * @return void
     */
    function edit_planning(&$content, $table_name, $name, $col, $extra_param) {

        $save_ok = false;
        $current = get_var('current');
        if ($current) {
            $content['mois'] = $this->current_month;
            $content['annee'] = $this->current_year;
        }
        $save = get_var('save', array('GET'));

        if (isset($content['add_horaire'])) {
            $compute = $this->compute_hour((int) $content['heure_arrivee'], (int) $content['heure_depart']);
            $content['heures_jour'] = $compute['day'];
            $content['heures_nuit'] = $compute['night'];
            $content['heures_jour_dimanche'] = $compute['sunday'];
            $content['heures_nuit_dimanche'] = $compute['sunnight'];

            if ($compute['night'] != 0) {
                $diff = $compute['night'] - $content['pause'];
                if ($diff >= 0) {
                    $content['heures_nuit'] = $diff;
                } else {
                    $content['heures_nuit'] = 0;
                    $content['heures_jour'] = $compute['day'] + $diff;
                }
            }

            // sunday
            elseif ($compute['sunnight'] != 0) {
                $diff = $compute['sunnight'] - $content['pause'];
                if ($diff >= 0) {
                    $content['heures_nuit_dimanche'] = $diff;
                } else {
                    $content['heures_nuit_dimanche'] = 0;
                    $content['heures_jour_dimanche'] = $compute['sunday'] + $diff;
                }
            } elseif ($compute['day'] != 0) {
                $content['heures_jour'] = $compute['day'] - $content['pause'];
                if ($content['heures_jour'] < 0) {
                    $content['heures_jour'] = 0;
                }
            } else {
                if ($compute['sunday'] != 0) {
                    $content['heures_jour_dimanche'] = $compute['sunday'] - $content['pause'];
                    if ($content['heures_jour_dimanche'] < 0) {
                        $content['heures_jour_dimanche'] = 0;
                    }
                }
            }
            //$content['heures_jour'] -= $content['heures_jour_dimanche'];
            //$content['heures_nuit'] -= $content['heures_nuit_dimanche'];
            $save_ok = $this->save_data($name, $table_name, $content, $col, $msg);
            $ave = $save_ok ? 'success' : 'error';

            if ($save_ok) {
                $content['add_edit'] = '<span class="title">' . $content['title'] . '<span> - <span id="edit">Modifier</span>';
                $content['msg_horaire'] = "<span id='success'>" . lang($msg) . " </span>";
                $no_button['add'] = true;
                $no_button['edit'] = false;
            } else {
                $content['msg_horaire'] = "<span id='error'>" . lang($msg) . " </span>";
            }
        } elseif (isset($content['close'])) {
            self::close_popup($extra_param);
        }
        $this->setup_table(APP_NAME, $table_name);
    }

    /**
     * update agents and sites list for current city
     * @param type $city 
     */
    function update_lists($city) {

        if ($city != '') {
            $this->setup_table(APP_NAME, 'egw_asecurite_site');
            $f_sites = $this->search(array('idasecurite_ville' => $city), false);
            if (count($f_sites) != 0) {
                $this->sites = array();
                $this->sites[''] = lang('Tous les sites');
                foreach ($f_sites as $key => $value) {
                    $this->sites[$value['idasecurite_site']] = $value['nom'];
                }
            } else {
                $this->sites = array();
            }
            $this->setup_table(APP_NAME, 'egw_asecurite_agent');
            $f_agents = $this->search(array('idasecurite_ville' => $city), false);
            if (count($f_agents) != 0) {
                $this->agents = array();
                $this->agents[''] = lang('Tous les agents');
                foreach ($f_agents as $key => $value) {
                    $this->agents[$value['idasecurite_agent']] = $value['nom'] . ' ' . $value['prenom'];
                }
            } else {
                $this->agents = array();
            }
        }
    }

    /**
     * close a popup opened windows and redirect (refresh) the popup parent opener
     * @param array $param link to the opener location
     * @return void
     */
    public static function close_popup($param) {
        $js = "opener.location.href='" . ($link = $GLOBALS['egw']->link('/index.php', $param)) . "';";
        $js .= 'window.close();';
        echo '<html><body onload="' . $js . '"></body></html>';
        $GLOBALS['egw']->common->egw_exit();
    }

    /**
     * Creation of the header page if need be
     * (Avoid it with a nexmatch widget)
     */
    public function createHeader() {
        $GLOBALS['egw']->common->egw_header();
        echo parse_navbar();
    }

    /**
     * 
     * create application's footer
     */
    public function create_footer() {
        include_once EGW_INCLUDE_ROOT . '/' . APP_NAME . '/js/jquery.php';
        echo $this->js_content;
        $GLOBALS['egw']->common->egw_footer();
    }

    /**
     * reset all weight containing in a given table (weight start from 1 to table content length)
     * @param string $table_name, table to use
     * @$extra_param array filter for where clause
     * @param string $pk primary key column name
     */
    public function reset_all_weight($table_name, $pk, $extra_param = '') {

        $this->setup_table(APP_NAME, $table_name);
        $f_all = array();
        if ($extra_param == '') {
            $f_all = $this->search('', false, 'weight DESC');
        } else {
            $f_all = $this->search($extra_param, false, 'weight DESC');
        }
        if ($f_all) {
            $i = count($f_all);
            foreach ($f_all as $k => $v) {
                if ($v['weight'] != '-1') {
                    if (is_array($extra_param)) {
                        $this->update(array('weight' => $i), array($pk => $v[$pk]) + $extra_param);
                    } else {
                        $this->update(array('weight' => $i), array($pk => $v[$pk]));
                    }
                }
                $i--;
            }
        }
    }

    function format_date($date) {
        return $this->datetime($date, false);
    }

    /**
     * Get mensual planning content either for all agents if agent id is not applicable or for a specified agent
     * and either for all working site or for a specified working site and either for all cities or for a specified city
     * @param int $month needed month
     * @param int $year needed year
     * @param int $agent_id agent id
     * @param int $id_site site id
     * @param int $id_city city id
     * @return array all matching rows 
     */
    function get_mensual_planning($month, $year, $agent_id = '', $id_site = '', $id_city = '') {
        $this->setup_table(APP_NAME, 'egw_asecurite_horaires_agent');
        $where = '';
        if ($agent_id) {
            $where['idasecurite_agent'] = $agent_id;
        }
        if ($id_site) {
            $where['idasecurite_site'] = $id_site;
        }
        if ($id_city) {
            $where['idasecurite_ville'] = $id_city;
        }
        $f_planning = $this->search($where, false, 'heure_arrivee ASC');
        $result = array();
        $i = 0;
        if ($f_planning) {
            foreach ($f_planning as $key => $value) {
                $m = $month != 0 ? date('m', $value['heure_arrivee']) : 0;
                $y = $year != 0 ? date('Y', $value['heure_arrivee']) : 0;
                if (($m == $month && $y == $year)) {
                    $result[$i++] = $value;
                }
            }
        }

        return $result;
    }

    function filter(&$query, $col) {
        if ($col['idasecurite_agent']) {
            $query['col_filter']['idasecurite_agent'] = $col['idasecurite_agent'];
        }
        if ($col['idasecurite_site']) {
            $query['col_filter']['idasecurite_site'] = $col['idasecurite_site'];
        }
        if ($col['idasecurite_ville']) {
            $query['col_filter']['idasecurite_ville'] = $col['idasecurite_ville'];
        }
    }

    /**
     * Give an agent planning to another one for a given month and a given year
     * @param int $month needed month
     * @param int $year needed year
     * @param int $agent_from 
     * @param int $agent_to 
     */
    function give_planning($month, $year, $agent_from, $agent_to, $ville_id, $site_id) {
        $this->setup_table(APP_NAME, 'egw_asecurite_horaires_agent');
        $f_planning = $this->search(array('idasecurite_agent' => $agent_from), false, 'heure_arrivee ASC');
        $update = 0;
        if ($f_planning) {

            foreach ($f_planning as $key => $value) {
                $m = $month != 0 ? date('m', $value['heure_arrivee']) : 0;
                $y = $year != 0 ? date('Y', $value['heure_arrivee']) : 0;
                if (($m == $month && $y == $year)) {
                    if ($this->update(array('idasecurite_agent' => $agent_to), array('idasecurite_agent' => $agent_from, 'heure_arrivee' => $value['heure_arrivee'], 'idasecurite_site' => $site_id, 'idasecurite_ville' => $ville_id))) {
                        $update++;
                    }
                }
            }
        }
        return $update;
    }

    /**
     * draw js chart for stat
     * @param int $nb_total
     * @param int $nb_day
     * @param int $nb_night
     * @param int $nb_sun_day
     * @param int $nb_sun_night
     * @return string js chart 
     */
    function draw_chart($char_id, $nb_total, $nb_day, $nb_night, $nb_sun_day, $nb_sun_night) {
        $return = '<table border="1" bgcolor="white"><caption id="segment_name"></caption>';
        $return .= '<tr><td><div id="' . $char_id . '"></div><script type="text/javascript">';
        $return .= "var myData = new Array(";
        $return .= "['" . lang('Heures jour') . "',$nb_day],";
        $return .= "['" . lang('Heures nuit') . "',$nb_night],";
        $return .= "['" . lang('Heures jour dimanche') . "',$nb_sun_day],";
        $return .= "['" . lang('Heures nuit dimanche') . "',$nb_sun_night]);";

        $day_color = $this->random_color();
        $night_color = $this->random_color();
        $sun_day_color = $this->random_color();
        $sun_night_color = $this->random_color();

        $return .= "var colors = ['#{$day_color}', '#{$night_color}', '#{$sun_day_color}', '#{$sun_night_color}'];";

        $day_percent = round($nb_day * 100. / $nb_total, 2);
        $night_percent = round($nb_night * 100. / $nb_total, 2);
        $sun_day_percent = round($nb_sun_day * 100. / $nb_total, 2);
        $sun_night_percent = round($nb_sun_night * 100. / $nb_total, 2);


        $total_in_time = $this->get_time($nb_total);
        $total_day_in_time = $this->get_time($nb_day);
        $total_sun_day_in_time = $this->get_time($nb_sun_day);
        $total_night_in_time = $this->get_time($nb_night);
        $total_sun_night_in_time = $this->get_time($nb_sun_night);

        $title = lang("Statistiques globales") . ' ' . lang('pour') . " $char_id";
        if ($char_id == lang('Total global')) {
            $title = lang('Statistiques globales');
        }

        $display_percent .= "&nbsp;&nbsp;<span>&nbsp;&nbsp;&nbsp;&nbsp;</span>" . lang("Nombre d'heures totales travaillées") . " : &nbsp; $total_in_time ( 100% )<br>";
        $display_percent .= "&nbsp;&nbsp;<span style='background-color: #{$day_color}'>&nbsp;&nbsp;&nbsp;&nbsp;</span>" . lang("Total du nombre d'heures jour") . " : &nbsp; $total_day_in_time &nbsp; ( $day_percent% )<br>";
        $display_percent .= "&nbsp;&nbsp;<span style='background-color: #{$night_color}'>&nbsp;&nbsp;&nbsp;&nbsp;</span>" . lang("Total du nombre d'heures nuit") . " : &nbsp; $total_night_in_time &nbsp; ( $night_percent% )<br>";
        $display_percent .= "&nbsp;&nbsp;<span style='background-color: #{$sun_day_color}; font-weight: bold'>&nbsp;&nbsp;&nbsp;&nbsp;</span><span style='font-weight: bold;'>" . lang("Total du nombre d'heures jour dimanche") . " : &nbsp; $total_sun_day_in_time &nbsp; ( $sun_day_percent% )</span><br>";
        $display_percent .= "&nbsp;&nbsp;<span style='background-color: #{$sun_night_color}; font-weight: bold'>&nbsp;&nbsp;&nbsp;&nbsp;</span><span style='font-weight: bold;'>" . lang("Total du nombre d'heures nuit dimanche") . " : &nbsp; $total_sun_night_in_time &nbsp; ( $sun_night_percent% )</span><br>";
        // $display_percent .= "&nbsp;&nbsp;<span style='font-weight: bold;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . lang("Total du nombre d'heures jour dimanche") . " : &nbsp; $total_sun_day_in_time <br>";
        //$display_percent .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . lang("Total du nombre d'heures nuit dimanche") . " : &nbsp; $total_sun_night_in_time <br></span>";
        $return .= "var myChart = new JSChart('$char_id', 'pie'); 
                        myChart.setDataArray(myData);
                        myChart.colorizePie(colors);
                        myChart.setTitle('$title');
                        myChart.setTitleColor('#0101DF');
                        myChart.draw();
                        </script> $display_percent </td></tr></table>";

        return $return;
    }

    /**
     * compute day and night hours worked for each working site
     * @param array $all_planning contains all data 
     * @return array associated array (date and night) for each working site
     */
    function get_hours_stat_by_site($all_planning) {

        $return = array();

        if (is_array($all_planning) && count($all_planning) != 0) {

            $return['total']['day'] = 0;
            $return['total']['night'] = 0;
            $return['total']['sun_day'] = 0;
            $return['total']['sun_night'] = 0;


            foreach ($all_planning as $key => $value) {
                if (!is_array($return[$value['idasecurite_site']])) {
                    $return[$value['idasecurite_site']]['day'] = 0;
                    $return[$value['idasecurite_site']]['night'] = 0;
                    $return[$value['idasecurite_site']]['sun_day'] = 0;
                    $return[$value['idasecurite_site']]['sun_night'] = 0;
                }

                $day = intval($value['heures_jour']);
                $night = intval($value['heures_nuit']);
                $sun_day = intval($value['heures_jour_dimanche']);
                $sun_night = intval($value['heures_nuit_dimanche']);

                $return[$value['idasecurite_site']]['day'] += $day;
                $return['total']['day'] += $day;
                $return[$value['idasecurite_site']]['sun_day'] += $sun_day;
                $return['total']['sun_day'] += $sun_day;

                $return[$value['idasecurite_site']]['night'] += $night;
                $return['total']['night'] += $night;
                $return[$value['idasecurite_site']]['sun_night'] += $sun_night;
                $return['total']['sun_night'] += $sun_night;
            }
        }
        return $return;
    }

    function draw_stat($all_planning) {

        $stat_array = $this->get_hours_stat_by_site($all_planning);

        $return = '<div>';

        $this->setup_table(APP_NAME, 'egw_asecurite_site');
        foreach ($stat_array as $key => $value) {
            $site_name = $key;

            if ($key == 'total') {
                $site_name = lang('Total global');
            } else {
                $f_site_name = $this->search(array('idasecurite_site' => $key), false);

                $return .= '<div id="site_stat">';

                if ($f_site_name) {
                    $site_name = $f_site_name[0]['nom'];
                }
            }
            $return .= $this->draw_chart($site_name, $value['day'] + $value['night'] + $value['sun_day'] + $value['sun_night'], $value['day'], $value['night'], $value['sun_day'], $value['sun_night']);
            $return .= '</div>';
        }

        $return .= '</div><div id="end_float"></div>';

        return $return;
    }

    /**
     * compute the number of hours worked including the number of hours worked in the day and in the night
     * @param int $arrival arrival datetime (timestamp)
     * @param int $departure departure datetime (timestamp)
     * @return array 
     */
    function compute_hour($arrival, $departure) {

        //split datetime (j/m/Y H:i)
        $explodeA = explode(' ', $this->datetime($arrival, true));
        $explodeD = explode(' ', $this->datetime($departure, true));

        //split hour (H:i)
        $explodeH_A = explode(':', $explodeA[1]);
        $explodeH_D = explode(':', $explodeD[1]);

        //get hour (H)
        $h_a = intval($explodeH_A[0]);
        $h_d = intval($explodeH_D[0]);

        //get date with j/m/Y format (without hour and minute
        $date_a = date('j/m/Y', $arrival - ($h_a * 3600 + (int) $explodeH_A[1] * 60));
        $date_d = date('j/m/Y', $departure - ($h_d * 3600 + (int) $explodeH_D[1] * 60));

        $nb_hour_day = 0;
        $nb_hour_night = 0;
        $sunday_nb_hour_day = 0;
        $sunday_nb_hour_night = 0;
        $nb_total_hour = $departure - $arrival;

        if ($date_a != $date_d) {// different days   
            $explode_date_a = explode('/', $date_a);
            $explode_date_d = explode('/', $date_d);
            $ts_a_00 = mktime(0, 0, 0, $explode_date_a[1], $explode_date_a[0], $explode_date_a[2]);
            $ts_a_6 = mktime(6, 0, 0, $explode_date_a[1], $explode_date_a[0], $explode_date_a[2]);
            $ts_a_21 = mktime(21, 0, 0, $explode_date_a[1], $explode_date_a[0], $explode_date_a[2]);
            $ts_a_23 = mktime(23, 0, 0, $explode_date_a[1], $explode_date_a[0], $explode_date_a[2]);
            $ts_d_00 = mktime(0, 0, 0, $explode_date_d[1], $explode_date_d[0], $explode_date_d[2]);
            $ts_d_6 = mktime(6, 0, 0, $explode_date_d[1], $explode_date_d[0], $explode_date_d[2]);
            $ts_d_21 = mktime(21, 0, 0, $explode_date_d[1], $explode_date_d[0], $explode_date_d[2]);

            if (0 <= $h_a && $h_a <= 6) { // case 1 [0, 6]
                $nb_hour_day = $ts_a_21 - $ts_a_6;
                $nb_hour_night = ($ts_a_6 - $arrival) + ($ts_d_00 - $ts_a_21);
            } elseif (6 < $h_a && $h_a <= 21) { // case 2 ]6, 21]
                $nb_hour_day = $ts_a_21 - $arrival;
                $nb_hour_night = $ts_d_00 - $ts_a_21;
            } elseif (21 < $h_a && $h_a <= 23) { // case 3 ]21, 23]                
                $nb_hour_night = $ts_d_00 - $arrival;
            }

            if ($this->is_sunday($arrival)) {
                $sunday_nb_hour_day = $nb_hour_day;
                $sunday_nb_hour_night = $nb_hour_night;
                $nb_hour_day = 0;
                $nb_hour_night = 0;
            }

            if (0 <= $h_d && $h_d <= 6) {
                if ($this->is_sunday($departure)) {
                    $sunday_nb_hour_night += $departure - $ts_d_00;
                } else {
                    $nb_hour_night += $departure - $ts_d_00;
                }
            } elseif (6 < $h_d && $h_d <= 21) {
                if ($this->is_sunday($departure)) {
                    $sunday_nb_hour_night += $ts_d_6 - $ts_d_00;
                    $sunday_nb_hour_day += $departure - $ts_d_6;
                } else {
                    $nb_hour_night += $ts_d_6 - $ts_d_00;
                    $nb_hour_day += $departure - $ts_d_6;
                }
            } elseif (21 < $h_d && $h_d <= 23) {
                if ($this->is_sunday($departure)) {
                    $sunday_nb_hour_night += ($ts_d_6 - $ts_d_00) + ($departure - $ts_d_21);
                    $sunday_nb_hour_day += $departure - $ts_d_6;
                } else {
                    $nb_hour_night += $ts_d_6 - $ts_d_00 + ($departure - $ts_d_21);
                    $nb_hour_day += $departure - $ts_d_6;
                }
            }

            //******************///

            /* if (6 <= $h_a && $h_a <= 21) {// day included in arrival
              $explode_date_a = explode('/', $date_a);
              $explode_date_d = explode('/', $date_d);

              $ts_21 = mktime(21, 0, 0, $explode_date_a[1], $explode_date_a[0], $explode_date_a[2]);
              $ts_00 = mktime(0, 0, 0, $explode_date_d[1], $explode_date_d[0], $explode_date_d[2]);

              $nb_hour_day = $ts_21 - $arrival;

              if ($this->is_sunday($arrival)) {
              $sunday_nb_hour_day = $nb_hour_day;
              $sunday_nb_hour_night = $ts_00 - $ts_21;
              }
              }

              if (6 <= $h_d && $h_d <= 21) { // day included in departure
              $explode_date_d = explode('/', $date_d);

              $ts_6 = mktime(6, 0, 0, $explode_date_d[1], $explode_date_d[0], $explode_date_d[2]);

              $nb_hour_day += $departure - $ts_6;

              if ($this->is_sunday($departure)) {
              $sunday_nb_hour_day = $departure - $ts_6;
              $ts_00 = mktime(0, 0, 0, $explode_date_d[1], $explode_date_d[0], $explode_date_d[2]);
              $sunday_nb_hour_night += $ts_6 - $ts_00;
              }
              }

              if (0 <= $h_d && $h_d <= 6) {
              if ($this->is_sunday($departure)) {
              $ts_00 = mktime(0, 0, 0, $explode_date_d[1], $explode_date_d[0], $explode_date_d[2]);
              $sunday_nb_hour_night += $departure - $ts_00;
              }
              } */
        } else { // same day 
            if (0 <= $h_a && $h_a <= 6) { // case 1 [0, 6]
                if (0 <= $h_d && $h_d <= 6) {
                    $nb_hour_night = $nb_total_hour;
                } else {
                    $explode_date_d = explode('/', $date_d);
                    $ts_6 = mktime(6, 0, 0, $explode_date_d[1], $explode_date_d[0], $explode_date_d[2]);

                    if (6 < $h_d && $h_d <= 21) {
                        $nb_hour_night = $ts_6 - $arrival;
                        $nb_hour_day = $departure - $ts_6;
                    } elseif (21 < $h_d && $h_d <= 23) {
                        $ts_21 = mktime(21, 0, 0, $explode_date_d[1], $explode_date_d[0], $explode_date_d[2]);

                        $nb_hour_night = ($ts_6 - $arrival) + ($departure - $ts_21);
                        $nb_hour_day = $ts_21 - $ts_6;
                    }
                }
            } elseif (6 < $h_a && $h_a <= 21) { // case 2 ]6, 21]
                if (6 < $h_d && $h_d <= 21) {
                    $nb_hour_day = $nb_total_hour;
                } elseif (21 < $h_d && $h_d <= 23) {
                    $explode_date_d = explode('/', $date_d);
                    $ts_21 = mktime(21, 0, 0, $explode_date_d[1], $explode_date_d[0], $explode_date_d[2]);
                    $nb_hour_night = $departure - $ts_21;
                    $nb_hour_day = $ts_21 - $arrival;
                }
            } elseif (21 < $h_a && $h_a <= 23) { // case 3 ]21, 23]
                if (21 < $h_d && $h_d <= 23) {
                    $nb_hour_night = $nb_total_hour;
                }
            }

            if ($this->is_sunday($departure)) {
                $sunday_nb_hour_night = $nb_hour_night;
                $sunday_nb_hour_day = $nb_hour_day;
                $nb_hour_night = 0;
                $nb_hour_day = 0;
            }

            /// ****************** ///

            /* if (6 <= $h_a && $h_a <= 21) {
              if (6 <= $h_d && $h_d <= 21) {// day included in departure
              $nb_hour_day = $nb_total_hour;

              if ($this->is_sunday($departure)) { // or arrival
              $sunday_nb_hour_day = $nb_hour_day;
              }
              } else {
              $explode_date_a = explode('/', $date_a);
              $ts_21 = mktime(21, 0, 0, $explode_date_a[1], $explode_date_a[0], $explode_date_a[2]);
              $nb_hour_day = $ts_21 - $arrival;

              if ($this->is_sunday($departure)) { // or arrival
              $sunday_nb_hour_day = $nb_hour_day;
              $sunday_nb_hour_night = $departure - $ts_21;
              }
              }
              } else {
              if (6 <= $h_d && $h_d <= 21) {
              $explode_date_d = explode('/', $date_d);

              $ts_6 = mktime(6, 0, 0, $explode_date_d[1], $explode_date_d[0], $explode_date_d[2]);

              $nb_hour_day = $departure - $ts_6;

              if ($this->is_sunday($departure)) {
              $sunday_nb_hour_day = $nb_hour_day;

              //$ts_00 = mktime(0, 0, 0, $explode_date_d[1], $explode_date_d[0], $explode_date_d[2]);
              $sunday_nb_hour_night = $ts_6 - $arrival;
              }
              } else {
              if (0 <= $h_d && $h_d <= 6) {
              if (0 <= $h_a && $h_a <= 6) {
              if ($this->is_sunday($departure)) {
              $sunday_nb_hour_day = 0;
              $sunday_nb_hour_night = $nb_total_hour;
              $nb_hour_day = 0;
              }
              } else {
              if ($this->is_sunday($departure)) {
              $ts_00 = mktime(0, 0, 0, $explode_date_d[1], $explode_date_d[0], $explode_date_d[2]);
              $sunday_nb_hour_night = $departure - $ts_00;
              }
              }
              } else {
              if (22 <= $h_d && $h_d <= 23) {

              }
              }
              $sunday_nb_hour_day = 0;
              $nb_hour_day = 0;
              }
              } */
        }


        // $nb_hour_night = $nb_total_hour - $nb_hour_day;

        return array('total' => $nb_total_hour, 'day' => $nb_hour_day, 'night' => $nb_hour_night, 'sunday' => $sunday_nb_hour_day, 'sunnight' => $sunday_nb_hour_night);
    }

    /**
     * verifie if given datetime is sunday
     * @param int $datetime datetime to verifie
     * @return boolean true if datetime is sunday else false
     */
    function is_sunday($datetime) {

        if (strtolower(date('D', $datetime)) == 'sun') {
            return true;
        }
        return false;
    }

    /**
     * Generate a random color
     * 
     */
    function random_color() {
        mt_srand((double) microtime() * 1000000);
        $c = '';
        while (strlen($c) < 6) {
            $c .= sprintf("%02X", mt_rand(0, 255));
        }
        return $c;
    }

    /**
     * convert a timestamp (without date) in hour and minute
     * @param int $timestamp to convert
     * @return string h:m 
     */
    function get_time($timestamp) {
        $hour = (int) ($timestamp / 3600);

        $remind = $timestamp % 3600;

        $min = $remind / 60;

        return $min != 0 ? $hour . 'h:' . $min . 'm' : $hour . 'h';
    }

    function is_ferie($date_time) {
        $explode_date_time = explode(' ', $this->datetime($date_time, true));

        //split hour (H:i)
        $explode_hi = explode(':', $explode_date_time[1]);

        //get hour (H)
        $h = intval($explode_hi[0]);

        $date = $date_time - ($explode_hi[0] * 3600 + (int) $explode_hi[1] * 60);
        $this->setup_table(APP_NAME, 'egw_asecurite_ferie');
        if (count($this->search(array('jour' => "$date"), false)) == 1) {
            return true;
        }
        return false;
    }

    function manage_display(&$row) {

        $total_hour = ($row['heure_depart'] - $row['heure_arrivee']) - $row['pause'];
        $row['nombre_heures'] = '<span id="hour">' . $this->get_time($total_hour) . '</span>';
        $row['pause'] = $this->get_time($row['pause']);

        if ($total_hour >= (3600 * 6)) {
            $row['panier'] = 1;
            $this->nb_baskets++;
        }
        $ferieA = '';
        if ($this->is_ferie($row['heure_arrivee'])) {
            $ferieA = lang('férié');
        }
        $ferieD = '';
        if ($this->is_ferie($row['heure_depart'])) {
            $ferieD = lang('férié');
        }
        $row['heures_jour'] = '<span id="hour">' . $this->get_time($row['heures_jour']) . '</span>';
        $row['heures_nuit'] = '<span id="hour">' . $this->get_time($row['heures_nuit']) . '</span>';
        $row['heures_jour_dimanche'] = '<span id="sunday">' . $this->get_time($row['heures_jour_dimanche']) . '</span>';
        $row['heures_nuit_dimanche'] = '<span id="sunday">' . $this->get_time($row['heures_nuit_dimanche']) . '</span>';
        $row['heure_arrivee'] = $this->datetime($row['heure_arrivee'], true) . ' ' . $ferieA;
        $row['heure_depart'] = $this->datetime($row['heure_depart'], true) . ' ' . $ferieD;
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
     * delete a site
     */
    public function delete_planning() {
        $id = get_var('id');
        if ($id !== '') {
            $explode = explode('-', $id);
            $count = count($explode);
            if ($count == 1) {
                $this->delete(array('idasecurite_horaires_agent' => $id));
            } else {
                for ($i = 0; $i < $count; $i++) {
                    $this->delete(array('idasecurite_horaires_agent' => $explode[$i]));
                }
            }
        }
    }
    
}
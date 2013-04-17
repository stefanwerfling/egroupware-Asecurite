<?php

/**
 * <b>File class.ui_imprime.inc.php</b>
 * asecurite's user interface for print
 * @author N'faly KABA
 * @since   29/08/2011
 * @version 2.0
 * @copyright KABANFALY
 * @package egroupware
 * @subpackage asecurite/inc/
 * @filesource  class.ui_imprime.inc.php
 */
include_once(EGW_INCLUDE_ROOT . '/asecurite/inc/class.bo_asecurite.inc.php');

class ui_imprime extends bo_asecurite {

    /**
     * Public functions callable via menuaction
     *
     * @var array
     */
    public $public_functions = array(
        'print_planning_global' => True
    );
    public $pdf;

    function __construct() {

        parent::__construct('egw_asecurite_horaires_agent');
        $this->init_template();
        $this->pdf = new ui_pdf('P', 'mm', 'A4');
    }

    function print_planning_global_($content = NULL) {
        $content['logo'] = $this->html->image(APP_NAME, 'asecurite', lang("Logo"));
        //set month
        $content['mois'] = $this->monthes[$GLOBALS['egw']->session->appsession('current_month', APP_NAME)];
        // set year
        $content['annee'] = $this->years[$GLOBALS['egw']->session->appsession('current_year', APP_NAME)];
        // set site city
        $content['ville'] = $this->cities[$GLOBALS['egw']->session->appsession('current_ville', APP_NAME)];
        // set site name
        $content['site'] = $this->sites[$GLOBALS['egw']->session->appsession('current_site', APP_NAME)];

        $all_site = false;
        if ($GLOBALS['egw']->session->appsession('current_site', APP_NAME) == '') {
            $all_site = true;
        }
        $all_agent = false;
        // set agent
        if ($GLOBALS['egw']->session->appsession('current_agent', APP_NAME) == '') {
            $all_agent = true;
            $content['titre'] = '<span>PLANNING DES AGENTS</span>';
        } else {
            $content['titre'] = "<span>FEUILLE DE PLANNING DE: {$this->agents[$GLOBALS['egw']->session->appsession('current_agent', APP_NAME)]} </span>";
        }

        /* ------------------  DEBUT HEADER  TABLEAU ------------------ */
        $content['planning'] = '<div id="planning"><table width="100%">
            <tr>
            <th>' . lang('Date') . '</th>';

        if ($all_agent) {
            $content['planning'] .= '<th>' . lang('Agent') . '</th>';
        }
        $content['planning'] .= '<th>' . lang("Heure d'arrivée") . '</th>' .
                '<th>' . lang('Pause') . '</th>' .
                '<th>' . lang('Heure de départ') . '</th>';

        if ($all_site) {
            $content['planning'] .= '<th>' . lang('Site') . '</th>';
        }
        $content['planning'] .= '<th>' . lang("Nbre d'heures jours") . '</th>' .
                '<th>' . lang("Nbre d'heures nuits") . '</th>' .
                '<th>' . lang("Nbre d'heures jour dimanche") . '</th>' .
                '<th>' . lang("Nbre d'heures nuit dimanche") . '</th>';

        if (self::$preferences['isPanier']) {
            $content['planning'] .= '<th>' . lang("Nbre\nde paniers") . '</th>';
        }
        $content['planning'] .=
                '<th>' . lang('Heures totales') . '</th>' .
                '</tr>';
        $total_day = $total_night = $total_sun_day = $total_sun_night = $total = 0;

        $nb_global_hour_by_agent = array();

        $nb_paniers = 0;
        foreach ($GLOBALS['egw']->session->appsession('planning_to_print', APP_NAME) as $key => $value) {
            if (!is_array($nb_global_hour_by_agent[$value['idasecurite_agent']])) {
                $nb_global_hour_by_agent[$value['idasecurite_agent']][$value['idasecurite_site']]['day'] = 0;
                $nb_global_hour_by_agent[$value['idasecurite_agent']][$value['idasecurite_site']]['sunday'] = 0;
                $nb_global_hour_by_agent[$value['idasecurite_agent']][$value['idasecurite_site']]['night'] = 0;
                $nb_global_hour_by_agent[$value['idasecurite_agent']][$value['idasecurite_site']]['sunnight'] = 0;
            }

            $day = intval($value['heures_jour']);
            $night = intval($value['heures_nuit']);
            $sun_day = intval($value['heures_jour_dimanche']);
            $sun_night = intval($value['heures_nuit_dimanche']);

            $nb_global_hour_by_agent[$value['idasecurite_agent']][$value['idasecurite_site']]['day'] += $day;
            $nb_global_hour_by_agent[$value['idasecurite_agent']][$value['idasecurite_site']]['sunday'] += $sun_day;
            $nb_global_hour_by_agent[$value['idasecurite_agent']][$value['idasecurite_site']]['night'] += $night;
            $nb_global_hour_by_agent[$value['idasecurite_agent']][$value['idasecurite_site']]['sunnight'] += $sun_night;

            $content['planning'] .= '<tr>' .
                    '<td>' . date('j', $value['heure_arrivee']) . '</td>';
            if ($all_agent) {
                $content['planning'] .= '<td>' . $this->agents[$value['idasecurite_agent']] . '</td>';
            }
            $content['planning'] .= '<td>' . date('H:i', $value['heure_arrivee']) . '</td>' .
                    '<td>' . $this->get_time($value['pause']) . '</td>' .
                    '<td>' . date('H:i', $value['heure_depart']) . '</td>';

            if ($all_site) {
                $content['planning'] .= '<td>' . $this->sites[$value['idasecurite_site']] . '</td>';
            }

            $total = $day + $night + $sun_day + $sun_night;
            $panier = floor($total / (3600 * 6));
            $nb_paniers += $panier;
            $content['planning'] .= '<td>' . $this->get_time($day) . '</td>' .
                    '<td>' . $this->get_time($night) . '</td>' .
                    '<td>' . $this->get_time($sun_day) . '</td>' .
                    '<td>' . $this->get_time($sun_night) . '</td>';

            if (self::$preferences['isPanier']) {
                $content['planning'] .= '<td>' . $panier . '</td>';
            }
            $content['planning'] .= '<td>' . $this->get_time($total) . '</td>' .
                    '</tr>';

            $total_day += $day;
            $total_sun_day += $sun_day;
            $total_night += $night;
            $total_sun_night += $sun_night;
        }
        $total = $total_day + $total_night + $total_sun_day + $total_sun_night;

        $content['planning'] .= '</table></div>';

        $content['total'] = '<div id="total"><table><caption>Global</caption>';
        if (self::$preferences['isPanier']) {
            $content['total'] .= '<tr><td>' . lang('Paniers') . '</td><td>' . $nb_paniers . '</td></tr>';
        }
        $content['total'] .= '<tr><td id="total_hour">' . lang('Total Heures') . '</td><td>' . $this->get_time($total) . '</td></tr>' .
                '<tr><td id="hour">' . lang('Total Heures de jour') . '</td><td>' . $this->get_time($total_day) . '</td></tr>' .
                '<tr><td id="hour">' . lang('Total Heures de nuit') . '</td><td>' . $this->get_time($total_night) . '</td></tr>' .
                '<tr><td id="sunday">' . lang('Heures jour dimanche') . '</td><td>' . $this->get_time($total_sun_day) . '</td></tr>' .
                '<tr><td id="sunday">' . lang('Heures nuit dimanche') . '</td><td>' . $this->get_time($total_sun_night) . '</td></tr>';
        '</table></div>';

        if (is_array($nb_global_hour_by_agent)) {
            $content['total_by_agent'] = '<div id= "site_planning_by_agent">';
            foreach ($nb_global_hour_by_agent as $agent => $site) {
                $this->setup_table(APP_NAME, 'egw_asecurite_agent');
                $f_agent = $this->search(array('idasecurite_agent' => $agent), false);
                if (count($f_agent) == 1) {

                    $agent_name = $f_agent[0]['nom'] . ' ' . $f_agent[0]['prenom'];

                    foreach ($site as $key => $value) {
                        $site = $this->sites[$key];
                        $content['total_by_agent'] .= '<div id="site_stat"><table><caption>' . $agent_name . ' ( ' . $site . ' )</caption>' .
                                '<tr><td id="total_hour">' . lang('Total Heures') . '</td><td><div>' . $this->get_time($value['day'] + $value['night'] + $value['sunday'] + $value['sunnight']) . '</div></td></tr>' .
                                '<tr><td id="hour">' . lang('Total Heures de jour') . '</td><td><div>' . $this->get_time($value['day']) . '</div></td></tr>' .
                                '<tr><td id="hour">' . lang('Total Heures de nuit') . '</td><td><div>' . $this->get_time($value['night']) . '</div></td></tr>' .
                                '<tr><td id="sunday">' . lang('Heures jour dimanche') . '</td><td><div>' . $this->get_time($value['sunday']) . '</div></td></tr>' .
                                '<tr><td id="sunday">' . lang('Heures nuit dimanche') . '</td><td><div>' . $this->get_time($value['sunnight']) . '</div></td></tr></table></div>';
                    }
                }
            }
            $content['total_by_agent'] .= '</div><div id="end_float"></div>';
        }
        $content['date'] = date('j/m/Y');
        $content['paniers'] = $nb_paniers;
        $content['adresse'] = '<center><span id="adresse"> <small>' . self::$preferences['address'] . '</small><span></center>';
        // $content['adresse'] = '<span id="adresse"> <small>20, bis rue de la Frelonnerie - 37270 Montlouis-sur-Loire</small><span>';
        $this->tmpl->read(APP_NAME . '.imprime');
        $this->tmpl->exec(APP_NAME . '.ui_imprime.print_planning_global', $content, '', '', '', 2);
    }

    /**
     * Print planning as PDF by usiing fpdf pluging
     */
    function print_planning_global() {
        //Initialisation
        $this->pdf->SetMargins(5, 10, 5);
        $this->pdf->AliasNbPages();
        $this->pdf->AddPage();
        $this->pdf->startPageNums();
        $this->pdf->SetFont('Times', '', 11);
        //Adding logo
        $this->pdf->Image(EGW_INCLUDE_ROOT . '/' . APP_NAME . '/templates/default/images/asecurite.png', 5, 6);

        $this->pdf->Cell(48); //set marging right

        $this->pdf->Cell(60, 0, 'Date d\'impression: ' . date('j/m/Y'), 0, 1, 'L');
        //$this->pdf->Ln(1);
        //set marging right
        $this->pdf->Cell(48); //set marging right
        $this->pdf->Cell(114, 10, utf8_decode('Ville: ' . $this->cities[$GLOBALS['egw']->session->appsession('current_ville', APP_NAME)]), 0, 1, 'L');
        //$this->pdf->Ln(1);
        $this->pdf->Cell(48);
        $this->pdf->Cell(114, 0, utf8_decode('Site: ' . $this->sites[$GLOBALS['egw']->session->appsession('current_site', APP_NAME)]), 0, 1, 'L');
        //$this->pdf->Ln(1);
        $this->pdf->Cell(48);
        $this->pdf->Cell(114, 10, utf8_decode('Mois: ' . $this->monthes[$GLOBALS['egw']->session->appsession('current_month', APP_NAME)]), 0, 1, 'L');
        // $this->pdf->Ln(1);
        $this->pdf->Cell(48);
        $this->pdf->Cell(114, 0, utf8_decode('Année: ' . $this->years[$GLOBALS['egw']->session->appsession('current_year', APP_NAME)]), 0, 1, 'L');
        $this->pdf->Ln(10);

        $all_site = false;
        if ($GLOBALS['egw']->session->appsession('current_site', APP_NAME) == '') {
            $all_site = true;
        }
        $all_agent = false;
        $this->pdf->SetFont('Times', 'B', 14);
        // set agent
        if ($GLOBALS['egw']->session->appsession('current_agent', APP_NAME) == '') {
            $all_agent = true;
            $this->pdf->Cell(0, 0, utf8_decode('PLANNING DES AGENTS'), 0, 1, 'C');
        } else {
            $this->pdf->Cell(0, 0, utf8_decode("Feuille de planning de: {$this->agents[$GLOBALS['egw']->session->appsession('current_agent', APP_NAME)]} "), 0, 1, 'C');
        }

        /* ---- BEGIN PLANNING TABLES ------ */
        $table_property = array(
            'TB_ALIGN' => 'L',
            'L_MARGIN' => 0,
            'BRD_COLOR' => array(0, 0, 0),
            'BRD_SIZE' => '0.3',
        );
        //Table header property
        $header_property = array(
            'T_COLOR' => array(0, 0, 0),
            'T_SIZE' => 9,
            'T_FONT' => 'Times',
            'T_ALIGN' => 'C',
            'V_ALIGN' => 'M',
            'T_TYPE' => 'B',
            'LN_SIZE' => 5,
            'BG_COLOR_COL0' => array(240, 240, 240),
            'BG_COLOR' => array(240, 240, 240),
            'BRD_COLOR' => array(0, 0, 0),
            'BRD_SIZE' => 0.2,
            'BRD_TYPE' => '1',
            'BRD_TYPE_NEW_PAGE' => '',
        );


        $header_sizes = array();
        $header_contents = array();
        $table_content = array();
        //Table property
        $content_property = array(
            'T_COLOR' => array(0, 0, 0),
            'T_SIZE' => 8,
            'T_FONT' => 'Times',
            'T_ALIGN_COL0' => 'L',
            'T_ALIGN' => 'C',
            'V_ALIGN' => 'M',
            'T_TYPE' => '',
            'LN_SIZE' => 6,
            'BG_COLOR_COL0' => array(255, 255, 255),
            'BG_COLOR' => array(255, 255, 255),
            'BRD_COLOR' => array(0, 0, 0),
            'BRD_SIZE' => 0.1,
            'BRD_TYPE' => '1',
            'BRD_TYPE_NEW_PAGE' => '',
        );
        /*         * ** Table Header *** */
        $header_sizes[] = 9;
        $header_contents[] = lang('Date');
        //If agents are set 
        if ($all_agent) {
            $header_sizes[] = 30;
            $header_contents[] = lang('Agent');
        }
        $header_sizes[] = 15;
        $header_contents[] = utf8_decode(lang("Heure\nd'arrivée"));
        $header_sizes[] = 12;
        $header_contents[] = utf8_decode(lang("Pause"));
        $header_sizes[] = 17;
        $header_contents[] = utf8_decode(lang("Heure\nde départ"));

        if ($all_site) {
            $header_sizes[] = 25;
            $header_contents[] = utf8_decode(lang("Site"));
        }
        $header_sizes[] = ($all_agent && $all_site) ? 15 : 20;
        $header_contents[] = utf8_decode(lang("Heures\njours"));
        $header_sizes[] = ($all_agent && $all_site) ? 15 : 20;
        $header_contents[] = utf8_decode(lang("Heures\nnuits"));
        $header_sizes[] = ($all_agent && $all_site) ? 18 : 20;
        $header_contents[] = utf8_decode(lang("Heures\njours\ndimanche"));
        $header_sizes[] = ($all_agent && $all_site) ? 18 : 20;
        $header_contents[] = utf8_decode(lang("Heures\nnuits\ndimanche"));

        if (self::$preferences['isPanier']) {
            $header_sizes[] = 13;
            $header_contents[] = utf8_decode(lang("Paniers"));
        }
        $header_sizes[] = 13;
        $header_contents[] = utf8_decode(lang("Heures\ntotales"));
        $this->pdf->Ln(5);
        $total_day = $total_night = $total_sun_day = $total_sun_night = $total = 0;
        $nb_global_hour_by_agent = array();
        $nb_paniers = 0;
        $nb_ferie = 0;
        //Fill table
        foreach ($GLOBALS['egw']->session->appsession('planning_to_print', APP_NAME) as $key => $value) {
            if (!is_array($nb_global_hour_by_agent[$value['idasecurite_agent']])) {
                $nb_global_hour_by_agent[$value['idasecurite_agent']][$value['idasecurite_site']]['day'] = 0;
                $nb_global_hour_by_agent[$value['idasecurite_agent']][$value['idasecurite_site']]['sunday'] = 0;
                $nb_global_hour_by_agent[$value['idasecurite_agent']][$value['idasecurite_site']]['night'] = 0;
                $nb_global_hour_by_agent[$value['idasecurite_agent']][$value['idasecurite_site']]['sunnight'] = 0;
                $nb_global_hour_by_agent[$value['idasecurite_agent']][$value['idasecurite_site']]['nb_ferie'] = 0;
            }
            //Nb hours * 2 is ferie
            if ($this->is_ferie($value['heure_arrivee']) && $this->is_ferie($value['heure_depart'])) {
                $day = intval($value['heures_jour']) * 2;
                $night = intval($value['heures_nuit']) * 2;
                $sun_day = intval($value['heures_jour_dimanche']) * 2;
                $sun_night = intval($value['heures_nuit_dimanche']) * 2;
                $nb_global_hour_by_agent[$value['idasecurite_agent']][$value['idasecurite_site']]['nb_ferie']++;
                $nb_ferie++;
            } else {
                $day = intval($value['heures_jour']);
                $night = intval($value['heures_nuit']);
                $sun_day = intval($value['heures_jour_dimanche']);
                $sun_night = intval($value['heures_nuit_dimanche']);
            }

            $nb_global_hour_by_agent[$value['idasecurite_agent']][$value['idasecurite_site']]['day'] += $day;
            $nb_global_hour_by_agent[$value['idasecurite_agent']][$value['idasecurite_site']]['sunday'] += $sun_day;
            $nb_global_hour_by_agent[$value['idasecurite_agent']][$value['idasecurite_site']]['night'] += $night;
            $nb_global_hour_by_agent[$value['idasecurite_agent']][$value['idasecurite_site']]['sunnight'] += $sun_night;

            //If ferie add (F) to date
            if ($this->is_ferie($value['heure_arrivee']) && $this->is_ferie($value['heure_depart'])) {
                $table_content[] = date('j', $value['heure_arrivee']) . ' (F)';
            } else {
                $table_content[] = date('j', $value['heure_arrivee']);
            }
            //Add agent name if agent
            if ($all_agent) {
                $table_content[] = $this->agents[$value['idasecurite_agent']];
            }
            $table_content[] = date('H:i', $value['heure_arrivee']);
            $table_content[] = $this->get_time($value['pause']);
            $table_content[] = date('H:i', $value['heure_depart']);

            if ($all_site) {
                $table_content[] = $this->sites[$value['idasecurite_site']];
            }
            $total = $day + $night + $sun_day + $sun_night;
            $panier = floor($total / (3600 * 6));
            $nb_paniers += $panier;
            $table_content[] = $this->get_time($day);
            $table_content[] = $this->get_time($night);
            $table_content[] = $this->get_time($sun_day);
            $table_content[] = $this->get_time($sun_night);

            if (self::$preferences['isPanier']) {
                $table_content[] = $panier;
            }
            $table_content[] = $this->get_time($total);
            $total_day += $day;
            $total_sun_day += $sun_day;
            $total_night += $night;
            $total_sun_night += $sun_night;
        }
        //compute total hours
        $total = $total_day + $total_night + $total_sun_day + $total_sun_night;

        //Table header
        $header = array_merge($header_sizes, $header_contents);
        //Draw table
        $this->pdf->drawTableau($this->pdf, $table_property, $header_property, $header, $content_property, $table_content);

        /* ----- END PLANNING TABLES ------ */
        /* ----- BEGIN GLOBAL HOUR TABLE --- */
        $header_property['BG_COLOR_COL0'] = array(10, 240, 240);
        $Global_header = array(50, 25, 'Global', 'COLSPAN2');
        $Global_table_content = array();

        if (self::$preferences['isPanier']) {
            $Global_table_content[] = lang('Paniers');
            $Global_table_content[] = $nb_paniers;
        }
        $Global_table_content[] = lang('Total heures');
        $Global_table_content[] = $this->get_time($total);
        $Global_table_content[] = lang(utf8_decode('Jours férié'));
        $Global_table_content[] = $nb_ferie;
        $Global_table_content[] = lang('Heures jours');
        $Global_table_content[] = $this->get_time($total_day);
        $Global_table_content[] = lang('Heures nuits');
        $Global_table_content[] = $this->get_time($total_night);
        $Global_table_content[] = lang('Heures jours dimanche');
        $Global_table_content[] = $this->get_time($total_sun_day);
        $Global_table_content[] = lang('Heures jours dimanche');
        $Global_table_content[] = $this->get_time($total_sun_night);

        $this->pdf->Ln(3);
        $header_property['T_SIZE'] = 11;
        $content_property['T_SIZE'] = 10;
        $this->pdf->drawTableau($this->pdf, $table_property, $header_property, $Global_header, $content_property, $Global_table_content);
        $this->pdf->Ln(3);

        /* ---- END GLOBAL HOUR TABLE ------ */
        /* ---- BEGIN GLOBAL SITES TABLE ------ */

        if (is_array($nb_global_hour_by_agent)) {
            $this->pdf->AddPage();
            $this->pdf->Cell(0, 10, lang('Statistiques globales par site et par agent'), 1, 1, 'C');
            $this->pdf->Ln(3);
            $header_property['BG_COLOR_COL0'] = array(100, 200, 240);
            $table_property['L_MARGIN'] = 0;

            foreach ($nb_global_hour_by_agent as $agent => $site) {
                $this->setup_table(APP_NAME, 'egw_asecurite_agent');
                $f_agent = $this->search(array('idasecurite_agent' => $agent), false);
                if (count($f_agent) == 1) {
                    $agent_name = $f_agent[0]['nom'] . ' ' . $f_agent[0]['prenom'];
                    foreach ($site as $key => $value) {
                        $site_stat_table_content = array();
                        $site = $this->sites[$key];
                        $site_stat_header = array(50, 25, $agent_name . '(' . $site . ')', 'COLSPAN2');

                        $site_stat_table_content[] = lang('Total Heures');
                        $site_stat_table_content[] = $this->get_time($value['day'] + $value['night'] + $value['sunday'] + $value['sunnight']);
                        $site_stat_table_content[] = lang(utf8_decode('Jours férié'));
                        $site_stat_table_content[] = $value['nb_ferie'];
                        $site_stat_table_content[] = lang('Heures jours');
                        $site_stat_table_content[] = $this->get_time($value['day']);
                        $site_stat_table_content[] = lang('Heures nuits');
                        $site_stat_table_content[] = $this->get_time($value['night']);
                        $site_stat_table_content[] = lang('Heures jours dimanche');
                        $site_stat_table_content[] = $this->get_time($value['sunday']);
                        $site_stat_table_content[] = lang('Heures jours dimanche');
                        $site_stat_table_content[] = $this->get_time($value['sunnight']);

                        $this->pdf->drawTableau($this->pdf, $table_property, $header_property, $site_stat_header, $content_property, $site_stat_table_content);
                        $this->pdf->Ln(2);
                    }
                }
            }
        }
        /* ---- BEGIN GLOBAL SITES TABLE ------ */
        $this->pdf->Output();
    }

}

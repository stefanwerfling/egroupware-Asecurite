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
include_once(EGW_INCLUDE_ROOT . '/asecurite/inc/class.ui_pdf.inc.php');
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

    function print_planning_global() {
        $this->pdf->SetMargins(5, 10, 5);
        $this->pdf->AliasNbPages();
        $this->pdf->AddPage();
        $this->pdf->startPageNums();
        $this->pdf->SetFont('Times', '', 11);
        $this->pdf->Image(EGW_INCLUDE_ROOT . '/' . APP_NAME . '/templates/default/images/asecurite.png', 5, 6);
        //décalage à droite
        $this->pdf->Cell(48);
        $this->pdf->Cell(60, 0, 'Date d\'impression: ' . date('j/m/Y'), 0, 1, 'L');
        $this->pdf->Ln(1);
        $this->pdf->Cell(48);
        $this->pdf->Cell(114, 10, utf8_decode('Ville: ' . $this->cities[$GLOBALS['egw']->session->appsession('current_ville', APP_NAME)]), 0, 1, 'L');
        $this->pdf->Ln(1);
        $this->pdf->Cell(48);
        $this->pdf->Cell(114, 0, utf8_decode('Site: ' . $this->sites[$GLOBALS['egw']->session->appsession('current_site', APP_NAME)]), 0, 1, 'L');
        $this->pdf->Ln(1);
        $this->pdf->Cell(48);
        $this->pdf->Cell(114, 10, utf8_decode('Mois: ' . $this->monthes[$GLOBALS['egw']->session->appsession('current_month', APP_NAME)]), 0, 1, 'L');
        $this->pdf->Ln(1);
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
        $tableProperty = array(
            'TB_ALIGN' => 'L',
            'L_MARGIN' => 0,
            'BRD_COLOR' => array(0, 0, 0),
            'BRD_SIZE' => '0.3',
        );
        $header_property = array(
            'T_COLOR' => array(0, 0, 0),
            'T_SIZE' => 10,
            'T_FONT' => 'Arial',
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


        $headerSizes = array();
        $headerContents = array();
        $tableContent = array();

        $content_property = array(
            'T_COLOR' => array(0, 0, 0),
            'T_SIZE' => 10,
            'T_FONT' => 'Arial',
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

        $headerSizes[] = 10;
        $headerContents[] = lang('Date');

        if ($all_agent) {
            $headerSizes[] = 30;
            $headerContents[] = lang('Agent');
        }
        $headerSizes[] = 16;
        $headerContents[] = utf8_decode(lang("Heure\nd'arrivée"));
        $headerSizes[] = 15;
        $headerContents[] = utf8_decode(lang("Pause"));
        $headerSizes[] = 17;
        $headerContents[] = utf8_decode(lang("Heure\nde départ"));


        if ($all_site) {
            $headerSizes[] = 40;
            $headerContents[] = utf8_decode(lang("Site"));
        }
        $headerSizes[] = 15;
        $headerContents[] = utf8_decode(lang("Heures\njours"));
        $headerSizes[] = 15;
        $headerContents[] = utf8_decode(lang("Heures\nnuits"));
        $headerSizes[] = 20;
        $headerContents[] = utf8_decode(lang("Heures jours\ndimanche"));
        $headerSizes[] = 20;
        $headerContents[] = utf8_decode(lang("Heures nuits\ndimanche"));

        if (self::$preferences['isPanier']) {
            $headerSizes[] = 15;
            $headerContents[] = utf8_decode(lang("Paniers"));
        }
        $headerSizes[] = 15;
        $headerContents[] = utf8_decode(lang("Heures\ntotales"));
        $this->pdf->Ln(5);

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

            $tableContent[] = date('j', $value['heure_arrivee']);
            if ($all_agent) {
                $tableContent[] = $this->agents[$value['idasecurite_agent']];
            }
            $tableContent[] = date('H:i', $value['heure_arrivee']);
            $tableContent[] = $this->get_time($value['pause']);
            $tableContent[] = date('H:i', $value['heure_depart']);

            if ($all_site) {
                $tableContent[] = $this->sites[$value['idasecurite_site']];
            }

            $total = $day + $night + $sun_day + $sun_night;
            $panier = floor($total / (3600 * 6));
            $nb_paniers += $panier;
            $tableContent[] = $this->get_time($day);
            $tableContent[] = $this->get_time($night);
            $tableContent[] = $this->get_time($sun_day);
            $tableContent[] = $this->get_time($sun_night);

            if (self::$preferences['isPanier']) {
                $tableContent[] = $panier;
            }
            $tableContent[] = $this->get_time($total);

            $total_day += $day;
            $total_sun_day += $sun_day;
            $total_night += $night;
            $total_sun_night += $sun_night;
        }
        $total = $total_day + $total_night + $total_sun_day + $total_sun_night;

        $header = array_merge($headerSizes, $headerContents);
        $this->pdf->drawTableau($this->pdf, $tableProperty, $header_property, $header, $content_property, $tableContent);


        /* ---- END PLANNING TABLES ------ */

        /* ---- BEGIN GLOBAL HOUR TABLE ------ */
        $tableProperty['L_MARGIN'] = 130;
        $header_property['BG_COLOR_COL0'] = array(10, 240, 240);
        $Global_header = array(50, 10, 'Global', 'COLSPAN2');
        $Global_tableContent = array();

        $content['total'] = '<div id="total"><table><caption>Global</caption>';
        if (self::$preferences['isPanier']) {
            $Global_tableContent[] = lang('Paniers');
            $Global_tableContent[] = $nb_paniers;
        }
        $Global_tableContent[] = lang('Heures jours');
        $Global_tableContent[] = $this->get_time($total_day);
        $Global_tableContent[] = lang('Heures nuits');
        $Global_tableContent[] = $this->get_time($total_night);
        $Global_tableContent[] = lang('Heures jours dimanche');
        $Global_tableContent[] = $this->get_time($total_sun_day);
        $Global_tableContent[] = lang('Heures jours dimanche');
        $Global_tableContent[] = $this->get_time($total_sun_night);

        $this->pdf->Ln(3);
        $this->pdf->drawTableau($this->pdf, $tableProperty, $header_property, $Global_header, $content_property, $Global_tableContent);

        /* ---- END GLOBAL HOUR TABLE ------ */

        /* ---- BEGIN GLOBAL SITES TABLE ------ */

        if (is_array($nb_global_hour_by_agent)) {

            $header_property['BG_COLOR_COL0'] = array(100, 200, 240);
            $tableProperty['L_MARGIN'] = 0;

            foreach ($nb_global_hour_by_agent as $agent => $site) {
                $this->setup_table(APP_NAME, 'egw_asecurite_agent');
                $f_agent = $this->search(array('idasecurite_agent' => $agent), false);
                if (count($f_agent) == 1) {

                    $agent_name = $f_agent[0]['nom'] . ' ' . $f_agent[0]['prenom'];

                    foreach ($site as $key => $value) {
                        $site_stat_tableContent = array();
                        $site = $this->sites[$key];
                        $site_stat_header = array(50, 10, $agent_name . ' ( ' . $site . ' )', 'COLSPAN2');

                        $site_stat_tableContent[] = lang('Total Heures');
                        $site_stat_tableContent[] = $this->get_time($value['day'] + $value['night'] + $value['sunday'] + $value['sunnight']);
                        $site_stat_tableContent[] = lang('Heures jours');
                        $site_stat_tableContent[] = $this->get_time($value['day']);
                        $site_stat_tableContent[] = lang('Heures nuits');
                        $site_stat_tableContent[] = $this->get_time($value['night']);
                        $site_stat_tableContent[] = lang('Heures jours dimanche');
                        $site_stat_tableContent[] = $this->get_time($value['sunday']);
                        $site_stat_tableContent[] = lang('Heures jours dimanche');
                        $site_stat_tableContent[] = $this->get_time($value['sunnight']);
                        
                        $this->pdf->drawTableau($this->pdf, $tableProperty, $header_property, $site_stat_header, $content_property, $site_stat_tableContent);
                        $this->pdf->Ln(2);
                    }
                }
            }
        }
        /* ---- BEGIN GLOBAL SITES TABLE ------ */
        $this->pdf->Output();
    }

}

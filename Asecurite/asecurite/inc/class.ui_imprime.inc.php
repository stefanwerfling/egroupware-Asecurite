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
include_once(EGW_INCLUDE_ROOT . '/asecurite/inc/class.ui_imprime_pdf.inc.php');

class ui_imprime extends bo_asecurite {

    /**
     * Public functions callable via menuaction
     *
     * @var array
     */
    public $public_functions = array(
        'print_planning_global' => True,
        'print_planning_site' => True,
        'print_planning_agent' => True,
        'print_planning_ville' => True,
    );
    public $pdf;

    function __construct() {

        parent::__construct('egw_asecurite_horaires_agent');
        $this->init_template();
        $this->pdf = new ui_imprime_pdf();
    }

    function print_planning_global($content = NULL) {

        $content['logo'] = $this->html->image(APP_NAME, 'asecurite', lang("Logo"));
        //set month
        $content['mois'] = $this->monthes[$GLOBALS['egw']->session->appsession('current_month', APP_NAME)];
        // set year
        $content['annee'] = $this->years[$GLOBALS['egw']->session->appsession('current_year', APP_NAME)];
        // set site city
        $content['ville'] = $this->cities[$GLOBALS['egw']->session->appsession('current_ville', APP_NAME)];
        // set site name
        $content['site'] = $this->sites[$GLOBALS['egw']->session->appsession('current_site', APP_NAME)];

        $this->pdf->AliasNbPages();
        $this->pdf->AddPage();

        $this->pdf->SetFont('Times', '', 12);
        $this->pdf->Image(EGW_INCLUDE_ROOT . '/' . APP_NAME . '/templates/default/images/asecurite.png', 5, 6);
        //décalage à droite
        $this->pdf->Cell(45);
        $this->pdf->Cell(60, 0, 'Date d\'impression: ' . date('j/m/Y'), 0, 1, 'L');
        $this->pdf->Ln(1);
        $this->pdf->Cell(45);
        $this->pdf->Cell(114, 10, utf8_decode('Ville: ' . $content['ville']), 0, 1, 'L');
        $this->pdf->Ln(1);
        $this->pdf->Cell(45);
        $this->pdf->Cell(114, 0, utf8_decode('Site: ' . $content['site']), 0, 1, 'L');
        $this->pdf->Ln(1);
        $this->pdf->Cell(45);
        $this->pdf->Cell(114, 10, utf8_decode('Mois: ' . $content['mois']), 0, 1, 'L');
        $this->pdf->Ln(1);
        $this->pdf->Cell(45);
        $this->pdf->Cell(114, 0, utf8_decode('Année: ' . $content['annee']), 0, 1, 'L');
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
            $content['titre'] = '<span>PLANNING DES AGENTS</span>';
            $this->pdf->Cell(0, 0, utf8_decode('PLANNING DES AGENTS'), 0, 1, 'C');
        } else {
            $content['titre'] = "<span>FEUILLE DE PLANNING DE: {$this->agents[$GLOBALS['egw']->session->appsession('current_agent', APP_NAME)]} </span>";
            $this->pdf->Cell(0, 0, utf8_decode("Feuille de planning de: {$this->agents[$GLOBALS['egw']->session->appsession('current_agent', APP_NAME)]} "), 0, 1, 'C');
        }


        

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
        $content['planning'] .= '<th>' . lang("Nbre d'heures de jour") . '</th>' .
                '<th>' . lang("Nbre d'heures de nuit") . '</th>' .
                '<th>' . lang("Nbre d'heures de jour dimanche") . '</th>' .
                '<th>' . lang("Nbre d'heures de nuit dimanche") . '</th>';
        if (self::$preferences['isPanier']) {
            $content['planning'] .= '<th>' . lang("Nbre de paniers") . '</th>';
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
//        $this->tmpl->read(APP_NAME . '.imprime');
//        $this->tmpl->exec(APP_NAME . '.ui_imprime.print_planning_global', $content, '', '', '', 2);


        $this->pdf->Output();
    }

}

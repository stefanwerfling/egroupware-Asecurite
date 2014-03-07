<?php

/**
 * <b>File hook_setting.inc.php</b>
 * Asecurite setting hook
 * @author KABA N'faly
 * @since   28/08/2012
 * @version 2.0
 * @copyright KABANFALY
 * @package egroupware
 * @subpackage asecurite/inc/
 * @filesource  hook_setting.inc.php
 */

$choice = array(
    0 => 'Non',
    1 => 'Oui'
);

$GLOBALS['settings'] = array(
    'address' => array(
        'type' => 'text',
        'label' => 'Adresse',
        'name' => 'address',
        'help' => 'Adresse du siège social de la société'
    ),
    'isPanier' => array(
        'type' => 'select',
        'label' => 'Activer le panier',
        'values'  => $choice,
        'name' => 'isPanier',
        'help' => 'Utiliser la notion de panier (un panier = 6 heures de travail sans arrêt'
    )
);
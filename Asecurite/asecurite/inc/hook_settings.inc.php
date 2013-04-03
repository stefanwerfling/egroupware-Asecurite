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

/**
 * @var array indexed by numbers (start at -1).contains  Available log levels of OPF_Logger (Orange product)
 */
$log_levels = array(
    -1 => 'No log',
    0 => 'Emergency',
    1 => 'Alert',
    2 => 'Critical',
    3 => 'Error',
    4 => 'Warning',
    5 => 'Notice',
    6 => 'Information',
    7 => 'Debug'
);

/**
 * @var array indexed by numbers. contains  Available log handlers of OPF_Logger (Orange product)
 */
$log_handlers = array(
    'Syslog' => 'Syslog',
    'Locallog' => 'Locallog',
    'FirePHP' => 'FirePHP',
    'Notifier' => 'Notifier'
);


$GLOBALS['settings'] = array(
    'address' => array(
        'type' => 'input',
        'label' => 'Adresse',
        'name' => 'address',
        'help' => 'Adresse du siège social de la société'
    ),
    'isPanier' => array(
        'type' => 'select',
        'label' => 'Activer le panier',
        'values' => $choice,
        'name' => 'isPanier',
        'help' => 'Utiliser la notion de panier (un panier = 6 heures de travail sans arrêt'
    ),  /** ----------------------------------------
     *           LOG Settings 
     *  --------------------------------------- */
    'log_level' => array(
        'type' => 'select',
        'label' => 'LOGs : Choisir le niveau de LOG',
        'name' => 'log_level',
        'values' => $log_levels,
        'forced' => 4,
        'help' => 'Pour disposer d\'informations supplémentaires concernant les niveaux de LOG, se réferrer à l\'API disponible sur le wiki wiki (OPF_Logger)',
    ),
    'log_handler' => array(
        'type' => 'select',
        'label' => 'LOGs : Choisir le log handler',
        'name' => 'log_handler',
        'values' => $log_handlers,
        'forced' => 'Syslog',
        'help' => 'Permet de définir quel est le LOG handler devant traiter les logs',
    ),
    'log_file' => array(
        'type' => 'input',
        'label' => 'Nom du fichier de log',
        'name' => 'log_file',
        'forced' => 'log_asecurite.log'
        
    )
    
);
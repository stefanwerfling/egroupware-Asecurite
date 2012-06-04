<?php

$setup_info['asecurite']['name'] = 'asecurite';
$setup_info['asecurite']['title'] = 'asecurite';
$setup_info['asecurite']['version'] = '1.025';
$setup_info['asecurite']['app_order'] = 10; 
$setup_info['pushoffres']['license']  = 'GPL';
//$setup_info['asecurite']['tables'] = array('egw_asecurite_agent','egw_asecurite_site','egw_asecurite_horaires_agent','egw_asecurite_ville','egw_asecurite_ferie');
$setup_info['asecurite']['enable'] = 1;


//menu definition
$setup_info['asecurite']['hooks'][] = 'sidebox_menu';

/* Dependencies for this app to work */
$setup_info['asecurite']['depends'][] = array(
'appname' => 'phpgwapi',
'versions' => Array('1.8','1.9')
);

$setup_info['asecurite']['depends'][] = array(
'appname' => 'etemplate',
'versions' => Array('1.8','1.9')
);

	$setup_info['asecurite']['tables'] = array('egw_asecurite_agent','egw_asecurite_site','egw_asecurite_horaires_agent','egw_asecurite_ville','egw_asecurite_ferie');





















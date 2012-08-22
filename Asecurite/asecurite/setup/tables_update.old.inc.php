<?php
/**
 * eGroupWare - Setup
 * http://www.egroupware.org
 * Created by eTemplates DB-Tools written by ralfbecker@outdoor-training.de
 *
 * @license http://opensource.org/licenses/gpl-license.php GPL - GNU General Public License
 * @package asecurite
 * @subpackage setup
 * @version $Id$
 */

function asecurite_upgrade1_001()
{
	$GLOBALS['egw_setup']->oProc->CreateTable('egw_asecurite_site',array(
		'fd' => array(
			'idasecurite_site' => array('type' => 'auto','nullable' => False),
			'nom' => array('type' => 'varchar','precision' => '255'),
			'adresse' => array('type' => 'text','precision' => '255'),
			'telephone' => array('type' => 'varchar','precision' => '255')
		),
		'pk' => array('idasecurite_site'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	));

	return $GLOBALS['setup_info']['asecurite']['currentver'] = '1.002';
}


function asecurite_upgrade1_002()
{
	$GLOBALS['egw_setup']->oProc->AlterColumn('egw_asecurite_agent','adresse',array(
		'type' => 'text'
	));

	return $GLOBALS['setup_info']['asecurite']['currentver'] = '1.003';
}


function asecurite_upgrade1_003()
{
	$GLOBALS['egw_setup']->oProc->CreateTable('egw_asecurite_horaires_agent',array(
		'fd' => array(
			'idasecurite_horaires_agent' => array('type' => 'auto','nullable' => False),
			'idasecurite_agent' => array('type' => 'int','precision' => '4'),
			'heure_arrivee' => array('type' => 'varchar','precision' => '255'),
			'heure_depart' => array('type' => 'varchar','precision' => '255'),
			'pause' => array('type' => 'int','precision' => '4'),
			'nombre_heures_jour' => array('type' => 'int','precision' => '4'),
			'nombre_heures_nuit' => array('type' => 'int','precision' => '4'),
			'idasecurite_site' => array('type' => 'int','precision' => '4')
		),
		'pk' => array('idasecurite_horaires_agent'),
		'fk' => array(),
		'ix' => array('idasecurite_agent','idasecurite_site'),
		'uc' => array()
	));

	return $GLOBALS['setup_info']['asecurite']['currentver'] = '1.004';
}


function asecurite_upgrade1_004()
{
	$GLOBALS['egw_setup']->oProc->AddColumn('egw_asecurite_agent','type_contrat',array(
		'type' => 'varchar',
		'precision' => '45'
	));

	return $GLOBALS['setup_info']['asecurite']['currentver'] = '1.005';
}


function asecurite_upgrade1_005()
{
	$GLOBALS['egw_setup']->oProc->RenameColumn('egw_asecurite_horaires_agent','nombre_heures_jour','heures_jour');
	$GLOBALS['egw_setup']->oProc->RenameColumn('egw_asecurite_horaires_agent','nombre_heures_nuit','heures_nuit');

	return $GLOBALS['setup_info']['asecurite']['currentver'] = '1.006';
}


function asecurite_upgrade1_006()
{
	$GLOBALS['egw_setup']->oProc->AddColumn('egw_asecurite_agent','code_postal',array(
		'type' => 'varchar',
		'precision' => '255'
	));
	$GLOBALS['egw_setup']->oProc->AddColumn('egw_asecurite_agent','ville',array(
		'type' => 'varchar',
		'precision' => '45'
	));

	return $GLOBALS['setup_info']['asecurite']['currentver'] = '1.007';
}


function asecurite_upgrade1_007()
{
	$GLOBALS['egw_setup']->oProc->AlterColumn('egw_asecurite_site','nom',array(
		'type' => 'varchar',
		'precision' => '45'
	));
	$GLOBALS['egw_setup']->oProc->AlterColumn('egw_asecurite_site','telephone',array(
		'type' => 'varchar',
		'precision' => '45'
	));
	$GLOBALS['egw_setup']->oProc->AddColumn('egw_asecurite_site','code_postal',array(
		'type' => 'varchar',
		'precision' => '45'
	));
	$GLOBALS['egw_setup']->oProc->AddColumn('egw_asecurite_site','ville',array(
		'type' => 'varchar',
		'precision' => '45'
	));

	return $GLOBALS['setup_info']['asecurite']['currentver'] = '1.008';
}


function asecurite_upgrade1_008()
{
	$GLOBALS['egw_setup']->oProc->AlterColumn('egw_asecurite_agent','nom',array(
		'type' => 'varchar',
		'precision' => '45'
	));
	$GLOBALS['egw_setup']->oProc->AlterColumn('egw_asecurite_agent','prenom',array(
		'type' => 'varchar',
		'precision' => '45'
	));
	$GLOBALS['egw_setup']->oProc->AlterColumn('egw_asecurite_agent','telephone',array(
		'type' => 'varchar',
		'precision' => '45'
	));
	$GLOBALS['egw_setup']->oProc->AlterColumn('egw_asecurite_agent','code_postal',array(
		'type' => 'varchar',
		'precision' => '45'
	));

	return $GLOBALS['setup_info']['asecurite']['currentver'] = '1.009';
}


function asecurite_upgrade1_009()
{
	$GLOBALS['egw_setup']->oProc->AlterColumn('egw_asecurite_site','adresse',array(
		'type' => 'text'
	));

	return $GLOBALS['setup_info']['asecurite']['currentver'] = '1.010';
}


function asecurite_upgrade1_010()
{
	$GLOBALS['egw_setup']->oProc->AddColumn('egw_asecurite_horaires_agent','date',array(
		'type' => 'date'
	));

	return $GLOBALS['setup_info']['asecurite']['currentver'] = '1.011';
}


function asecurite_upgrade1_011()
{
	$GLOBALS['egw_setup']->oProc->AddColumn('egw_asecurite_agent','idasecurite_ville',array(
		'type' => 'int',
		'precision' => '4'
	));

	return $GLOBALS['setup_info']['asecurite']['currentver'] = '1.012';
}


function asecurite_upgrade1_012()
{
	$GLOBALS['egw_setup']->oProc->AddColumn('egw_asecurite_site','idasecurite_ville',array(
		'type' => 'varchar',
		'precision' => '255'
	));

	return $GLOBALS['setup_info']['asecurite']['currentver'] = '1.013';
}


function asecurite_upgrade1_013()
{
	$GLOBALS['egw_setup']->oProc->AddColumn('egw_asecurite_horaires_agent','idasecurite_ville',array(
		'type' => 'int',
		'precision' => '4'
	));

	return $GLOBALS['setup_info']['asecurite']['currentver'] = '1.014';
}


function asecurite_upgrade1_014()
{
	$GLOBALS['egw_setup']->oProc->CreateTable('egw_asecurite_ville',array(
		'fd' => array(
			'idasecurite_ville' => array('type' => 'auto','nullable' => False),
			'nom' => array('type' => 'varchar','precision' => '45')
		),
		'pk' => array('idasecurite_ville'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	));

	return $GLOBALS['setup_info']['asecurite']['currentver'] = '1.015';
}


function asecurite_upgrade1_015()
{
	$GLOBALS['egw_setup']->oProc->DropColumn('egw_asecurite_agent',array(
		'fd' => array(
			'idasecurite_agent' => array('type' => 'auto','nullable' => False),
			'nom' => array('type' => 'varchar','precision' => '45'),
			'prenom' => array('type' => 'varchar','precision' => '45'),
			'adresse' => array('type' => 'text'),
			'telephone' => array('type' => 'varchar','precision' => '45'),
			'date_naissance' => array('type' => 'date'),
			'date_debut_contrat' => array('type' => 'date'),
			'date_fin_contrat' => array('type' => 'date'),
			'type_contrat' => array('type' => 'varchar','precision' => '45'),
			'code_postal' => array('type' => 'varchar','precision' => '45'),
			'idasecurite_ville' => array('type' => 'int','precision' => '4')
		),
		'pk' => array('idasecurite_agent'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	),'ville');

	return $GLOBALS['setup_info']['asecurite']['currentver'] = '1.016';
}


function asecurite_upgrade1_016()
{
	$GLOBALS['egw_setup']->oProc->DropColumn('egw_asecurite_site',array(
		'fd' => array(
			'idasecurite_site' => array('type' => 'auto','nullable' => False),
			'nom' => array('type' => 'varchar','precision' => '45'),
			'adresse' => array('type' => 'text'),
			'telephone' => array('type' => 'varchar','precision' => '45'),
			'code_postal' => array('type' => 'varchar','precision' => '45'),
			'idasecurite_ville' => array('type' => 'varchar','precision' => '255')
		),
		'pk' => array('idasecurite_site'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	),'ville');
	$GLOBALS['egw_setup']->oProc->RefreshTable('egw_asecurite_site',array(
		'fd' => array(
			'idasecurite_site' => array('type' => 'auto','nullable' => False),
			'nom' => array('type' => 'varchar','precision' => '45'),
			'adresse' => array('type' => 'text'),
			'telephone' => array('type' => 'varchar','precision' => '45'),
			'code_postal' => array('type' => 'varchar','precision' => '45'),
			'idasecurite_ville' => array('type' => 'varchar','precision' => '255')
		),
		'pk' => array('idasecurite_site'),
		'fk' => array(),
		'ix' => array('idasecurite_ville'),
		'uc' => array()
	));

	return $GLOBALS['setup_info']['asecurite']['currentver'] = '1.017';
}


function asecurite_upgrade1_017()
{
	$GLOBALS['egw_setup']->oProc->RefreshTable('egw_asecurite_agent',array(
		'fd' => array(
			'idasecurite_agent' => array('type' => 'auto','nullable' => False),
			'nom' => array('type' => 'varchar','precision' => '45'),
			'prenom' => array('type' => 'varchar','precision' => '45'),
			'adresse' => array('type' => 'text'),
			'telephone' => array('type' => 'varchar','precision' => '45'),
			'date_naissance' => array('type' => 'date'),
			'date_debut_contrat' => array('type' => 'date'),
			'date_fin_contrat' => array('type' => 'date'),
			'type_contrat' => array('type' => 'varchar','precision' => '45'),
			'code_postal' => array('type' => 'varchar','precision' => '45'),
			'idasecurite_ville' => array('type' => 'int','precision' => '4')
		),
		'pk' => array('idasecurite_agent'),
		'fk' => array(),
		'ix' => array('idasecurite_ville'),
		'uc' => array()
	));

	return $GLOBALS['setup_info']['asecurite']['currentver'] = '1.018';
}


function asecurite_upgrade1_018()
{
	$GLOBALS['egw_setup']->oProc->CreateTable('egw_asecurite_ferie',array(
		'fd' => array(
			'idasecurite_ferie' => array('type' => 'auto','nullable' => False),
			'jour' => array('type' => 'varchar','precision' => '255')
		),
		'pk' => array('idasecurite_ferie'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	));

	return $GLOBALS['setup_info']['asecurite']['currentver'] = '1.019';
}


function asecurite_upgrade1_019()
{
	$GLOBALS['egw_setup']->oProc->AddColumn('egw_asecurite_ferie','description',array(
		'type' => 'varchar',
		'precision' => '255'
	));

	return $GLOBALS['setup_info']['asecurite']['currentver'] = '1.020';
}



function asecurite_upgrade1_020()
{
	$GLOBALS['egw_setup']->oProc->AddColumn('egw_asecurite_horaires_agent','heures_jour_dimanche',array(
		'type' => 'int',
		'precision' => '4'
	));
	$GLOBALS['egw_setup']->oProc->AddColumn('egw_asecurite_horaires_agent','heures_nuit_dimanche',array(
		'type' => 'int',
		'precision' => '4'
	));
	$GLOBALS['egw_setup']->oProc->AddColumn('egw_asecurite_horaires_agent','heures_jour_ferie',array(
		'type' => 'int',
		'precision' => '4'
	));
	$GLOBALS['egw_setup']->oProc->AddColumn('egw_asecurite_horaires_agent','heures_nuit_ferie',array(
		'type' => 'int',
		'precision' => '4'
	));

	return $GLOBALS['setup_info']['asecurite']['currentver'] = '1.021';
}


function asecurite_upgrade1_021()
{
	$GLOBALS['egw_setup']->oProc->AlterColumn('egw_asecurite_horaires_agent','heures_jour_dimanche',array(
		'type' => 'int',
		'precision' => '5'
	));
	$GLOBALS['egw_setup']->oProc->AlterColumn('egw_asecurite_horaires_agent','heures_nuit_dimanche',array(
		'type' => 'int',
		'precision' => '5'
	));
	$GLOBALS['egw_setup']->oProc->AlterColumn('egw_asecurite_horaires_agent','heures_jour_ferie',array(
		'type' => 'int',
		'precision' => '5'
	));
	$GLOBALS['egw_setup']->oProc->AlterColumn('egw_asecurite_horaires_agent','heures_nuit_ferie',array(
		'type' => 'int',
		'precision' => '5'
	));

	return $GLOBALS['setup_info']['asecurite']['currentver'] = '1.022';
}


function asecurite_upgrade1_022()
{
	$GLOBALS['egw_setup']->oProc->RefreshTable('egw_asecurite_horaires_agent',array(
		'fd' => array(
			'idasecurite_horaires_agent' => array('type' => 'auto','nullable' => False),
			'idasecurite_agent' => array('type' => 'int','precision' => '4'),
			'heure_arrivee' => array('type' => 'varchar','precision' => '255'),
			'heure_depart' => array('type' => 'varchar','precision' => '255'),
			'pause' => array('type' => 'int','precision' => '4'),
			'heures_jour' => array('type' => 'int','precision' => '4'),
			'heures_nuit' => array('type' => 'int','precision' => '4'),
			'idasecurite_site' => array('type' => 'int','precision' => '4'),
			'date' => array('type' => 'date'),
			'idasecurite_ville' => array('type' => 'int','precision' => '4'),
			'heures_jour_dimanche' => array('type' => 'int','precision' => '5'),
			'heures_nuit_dimanche' => array('type' => 'int','precision' => '5'),
			'heures_jour_ferie' => array('type' => 'int','precision' => '5'),
			'heures_nuit_ferie' => array('type' => 'int','precision' => '5')
		),
		'pk' => array('idasecurite_horaires_agent'),
		'fk' => array(),
		'ix' => array('idasecurite_agent','idasecurite_site','idasecurite_ville'),
		'uc' => array()
	));

	return $GLOBALS['setup_info']['asecurite']['currentver'] = '1.023';
}


function asecurite_upgrade1_023()
{
	$GLOBALS['egw_setup']->oProc->AddColumn('egw_asecurite_horaires_agent','nb_paniers',array(
		'type' => 'int',
		'precision' => '5'
	));

	return $GLOBALS['setup_info']['asecurite']['currentver'] = '1.024';
}


function asecurite_upgrade1_024()
{
	$GLOBALS['egw_setup']->oProc->DropColumn('egw_asecurite_horaires_agent',array(
		'fd' => array(
			'idasecurite_horaires_agent' => array('type' => 'auto','nullable' => False),
			'idasecurite_agent' => array('type' => 'int','precision' => '4'),
			'heure_arrivee' => array('type' => 'varchar','precision' => '255'),
			'heure_depart' => array('type' => 'varchar','precision' => '255'),
			'pause' => array('type' => 'int','precision' => '4'),
			'heures_jour' => array('type' => 'int','precision' => '4'),
			'heures_nuit' => array('type' => 'int','precision' => '4'),
			'idasecurite_site' => array('type' => 'int','precision' => '4'),
			'date' => array('type' => 'date'),
			'idasecurite_ville' => array('type' => 'int','precision' => '4'),
			'heures_jour_dimanche' => array('type' => 'int','precision' => '5'),
			'heures_nuit_dimanche' => array('type' => 'int','precision' => '5'),
			'heures_jour_ferie' => array('type' => 'int','precision' => '5'),
			'heures_nuit_ferie' => array('type' => 'int','precision' => '5')
		),
		'pk' => array('idasecurite_horaires_agent'),
		'fk' => array(),
		'ix' => array('idasecurite_agent','idasecurite_site','idasecurite_ville'),
		'uc' => array()
	),'nb_paniers');

	return $GLOBALS['setup_info']['asecurite']['currentver'] = '1.025';
}


function asecurite_upgrade1_025()
{
	$GLOBALS['egw_setup']->oProc->AddColumn('egw_asecurite_agent','type_piece_identite',array(
		'type' => 'varchar',
		'precision' => '45'
	));
	$GLOBALS['egw_setup']->oProc->AddColumn('egw_asecurite_agent','numero_piece_identite',array(
		'type' => 'varchar',
		'precision' => '45'
	));
	$GLOBALS['egw_setup']->oProc->AddColumn('egw_asecurite_agent','date_debut_piece_identite',array(
		'type' => 'date'
	));
	$GLOBALS['egw_setup']->oProc->AddColumn('egw_asecurite_agent','date_fin_piece_identite',array(
		'type' => 'date'
	));
	$GLOBALS['egw_setup']->oProc->AddColumn('egw_asecurite_agent','commune_piece_identite',array(
		'type' => 'varchar',
		'precision' => '45'
	));
	$GLOBALS['egw_setup']->oProc->AddColumn('egw_asecurite_agent','pays_piece_identite',array(
		'type' => 'varchar',
		'precision' => '45'
	));

	return $GLOBALS['setup_info']['asecurite']['currentver'] = '1.026';
}


function asecurite_upgrade1_026()
{
	$GLOBALS['egw_setup']->oProc->AlterColumn('egw_asecurite_ferie','jour',array(
		'type' => 'date'
	));

	return $GLOBALS['setup_info']['asecurite']['currentver'] = '1.027';
}


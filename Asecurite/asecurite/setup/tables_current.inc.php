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


$phpgw_baseline = array(
	'egw_asecurite_agent' => array(
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
			'idasecurite_ville' => array('type' => 'int','precision' => '4'),
			'type_piece_identite' => array('type' => 'varchar','precision' => '45'),
			'numero_piece_identite' => array('type' => 'varchar','precision' => '45'),
			'date_debut_piece_identite' => array('type' => 'date'),
			'date_fin_piece_identite' => array('type' => 'date'),
			'commune_piece_identite' => array('type' => 'varchar','precision' => '45'),
			'pays_piece_identite' => array('type' => 'varchar','precision' => '45'),
			'email' => array('type' => 'varchar','precision' => '45')
		),
		'pk' => array('idasecurite_agent'),
		'fk' => array(),
		'ix' => array('idasecurite_ville'),
		'uc' => array()
	),
	'egw_asecurite_site' => array(
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
	),
	'egw_asecurite_horaires_agent' => array(
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
	),
	'egw_asecurite_ville' => array(
		'fd' => array(
			'idasecurite_ville' => array('type' => 'auto','nullable' => False),
			'nom' => array('type' => 'varchar','precision' => '45')
		),
		'pk' => array('idasecurite_ville'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	),
	'egw_asecurite_ferie' => array(
		'fd' => array(
			'idasecurite_ferie' => array('type' => 'auto','nullable' => False),
			'jour' => array('type' => 'date'),
			'description' => array('type' => 'varchar','precision' => '255')
		),
		'pk' => array('idasecurite_ferie'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	)
);


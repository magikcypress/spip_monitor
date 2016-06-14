<?php

/**
 * Administrations pour Monitor
 *
 * @plugin     Monitor
 * @copyright  2014
 * @author     cyp
 * @licence    GNU/GPL
 * @package    SPIP\Monitor\administrations
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Installation/maj des tables monitor
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function monitor_upgrade($nom_meta_base_version, $version_cible) {
	
	$maj = array();

	$maj['create'] = array(
		array('maj_tables', array('spip_monitor', 'spip_monitor_log', 'spip_syndic', 'spip_monitor_stats', 'spip_monitor_stats_plugins'))
	);

	$maj['1.1'] = array(
		// Ajout de champs dans spip_syndic
		array('maj_tables', array('spip_syndic'))
	);

	$maj['1.2'] = array(
		// Ajout du champs alert
		array('sql_alter', 'TABLE spip_monitor ADD alert int(11) DEFAULT 0 NOT NULL AFTER id_syndic'));

	$maj['1.3'] = array(
		// Ajout de la table spip_monitor_stats, spip_monitor_stats_plugins
		array('maj_tables', array('spip_syndic', 'spip_monitor_stats', 'spip_monitor_stats_plugins'))
	);

	$maj['1.4'] = array(
		// Ajout de la table spip_monitor_stats, spip_monitor_stats_plugins
		array('maj_tables', array('spip_monitor_stats'))
	);

	$maj['1.5'] = array(
		// Ajouter un index à la table spip_shortcut_urls_logs
		array('sql_alter', 'TABLE spip_monitor ADD INDEX (id_syndic)'));

	$maj['1.7'] = array(
		// Ajouter un index à la table spip_monitor_log
		array('sql_alter', 'TABLE spip_monitor_log ADD INDEX (valeur)'));

	$maj['1.8'] = array(
		// Ajout de champs dans spip_monitor_evenements
		array('maj_tables', array('spip_monitor_evenements'))
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Desinstallation/suppression des tables monitor
 *
 * @param string $nom_meta_base_version
 */
function monitor_vider_tables($nom_meta_base_version) {

	sql_drop_table('spip_monitor');
	sql_drop_table('spip_monitor_log');
	sql_drop_table('spip_monitor_stats');
	sql_drop_table('spip_monitor_stats_plugins');
	sql_alter('TABLE spip_syndic DROP COLUMN date_ping');
	sql_alter('TABLE spip_syndic DROP COLUMN statut_log');
	sql_alter('TABLE spip_syndic DROP COLUMN statut_stats');
	effacer_meta('monitor');
	effacer_meta($nom_meta_base_version);
}

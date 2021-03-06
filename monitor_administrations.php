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
		array('maj_tables', array('spip_monitor', 'spip_monitor_log', 'spip_syndic', 'spip_monitor_stats', 'spip_monitor_stats_plugins', 'spip_monitor_evenements'))
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

	$maj['1.9'] = array(
		// Ajout du champs monitor_evenements dans spip_syndic
		array('maj_tables', array('spip_syndic'))
	);

	$maj['2.0'] = array(
		// Ajouter un index à la table spip_monitor_log
		array('sql_alter', 'TABLE spip_monitor_log MODIFY valeur DECIMAL(50,14)'));

	$maj['2.1'] = array(
		// Ajout du champs ping_courant et poids_courant dans spip_monitor
		array('maj_tables', array('spip_monitor'))
	);

	$maj['2.2'] = array(
		// Suppression de unsigned et auto incremente pour compat sqlite
		array('sql_alter', 'TABLE spip_monitor MODIFY id_monitor BIGINT(21) NOT NULL AUTO_INCREMENT'),
		array('sql_alter', 'TABLE spip_monitor_log MODIFY id_monitor_log BIGINT(21) NOT NULL AUTO_INCREMENT'),
		array('sql_alter', 'TABLE spip_monitor_stats MODIFY id_monitor_stats BIGINT(21) NOT NULL AUTO_INCREMENT'),
		array('sql_alter', 'TABLE spip_monitor_evenements MODIFY id_monitor_evenements BIGINT(21) NOT NULL AUTO_INCREMENT')
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
	sql_drop_table('spip_monitor_evenements');
	sql_alter('TABLE spip_syndic DROP COLUMN date_ping');
	sql_alter('TABLE spip_syndic DROP COLUMN statut_log');
	sql_alter('TABLE spip_syndic DROP COLUMN statut_stats');
	sql_alter('TABLE spip_syndic DROP COLUMN monitor_evenements');
	effacer_meta('monitor');
	effacer_meta($nom_meta_base_version);
}

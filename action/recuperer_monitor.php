<?php
/**
 * Monitor
 *
 * @plugin     Monitor
 * @copyright  2014
 * @author     cyp
 * @licence    GNU/GPL
 * @package    SPIP\action\recuperer_monitor
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function action_recuperer_monitor_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	include_once(find_in_path('lib/Monitor/MonitorSites.php'));
	include_once(find_in_path('genie/monitor.php'));

	$site = sql_fetsel('id_syndic, url_site', 'spip_syndic', 'id_syndic=' . intval($arg));
	$result = updateWebsite($site['url_site']);

	if ($result['result']==false) {

		// Gestion des alertes
		// Prendre la dernière valeur d'un site
		$alert_site = sql_getfetsel('alert', 'spip_monitor', 'id_syndic=' . $site['id_syndic'] . ' order by maj DESC limit 0,1');
		$alert = $alert_site+1;
		// Insert les data dans monitor_log
		genie_monitor_insert($site['id_syndic'], 'ping', ($result['result'] ? 'oui' : 'non'), $result['latency'], $alert);
		if ($alert >= 5 && $alert <= 10) {
			// Notification du site malade
			notification_monitor($site['url_site'], 'down');
		}

	} else {

		// Gestion des alertes
		$alert_site = sql_getfetsel('alert', 'spip_monitor', 'id_syndic=' . $site['id_syndic'] . ' order by maj DESC limit 0,1');

		if ($result['latency'] >= 10 && $alert_site >= 0) {
			$alert = $alert_site+1;
			// Insert les data dans monitor_log
			genie_monitor_insert($site['id_syndic'], 'ping', ($result['result'] ? 'oui' : 'non'), $result['latency'], $alert);
		} elseif ($result['latency'] >= 10 && $alert_site >= 5) {
			// Notification du site malade
			notification_monitor($site['url_site'], 'latence');
			// Insert les data dans monitor_log
			$alert = $alert_site+1;
			genie_monitor_insert($site['id_syndic'], 'ping', ($result['result'] ? 'oui' : 'non'), $result['latency'], $alert);
		} else {
			if ($alert_site>=5) {
				notification_monitor($site['url_site'], 'restart');
			}

			$alert = 0;
			// Insert les data dans monitor_log
			genie_monitor_insert($site['id_syndic'], 'ping', ($result['result'] ? 'oui' : 'non'), $result['latency'], $alert);
		}

		$result = sizePage($site['url_site']);
		// Insert les data dans monitor_log
		$insert_poids = sql_insertq('spip_monitor_log', array('id_syndic' => $site['id_syndic'], 'statut' => 'poids', 'log' => ($result['result'] ? 'oui' : 'non'), 'valeur' => $result['poids']));

	}

	return false;
}

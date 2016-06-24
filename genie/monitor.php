<?php
/**
 * Monitor
 *
 * @plugin	 Monitor
 * @copyright  2014
 * @author	 cyp
 * @licence	GNU/GPL
 * @package	SPIP\genie\monitor
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Notification envoi
 *
 * @param string $texte_sujet
 * @param string $texte_corps
 * @return string structure du message
 *
 */
function notification_envoyer($texte_sujet, $texte_corps) {
	$sujet = '[' . $GLOBALS['meta']['nom_site'] .'] ' . $texte_sujet;
	$body = $texte_corps;
	$envoyer_mail = charger_fonction('envoyer_mail', 'inc');
	$envoyer_mail($GLOBALS['meta']['email_webmaster'], $sujet, $body);
}

/**
 * Notification par email des sites à problème
 *
 * @param string $href
 * @param string $type
 * @return string envoi de la notification
 *
 */
function notification_monitor($href, $type) {
	if ($type=='latence') {
		$texte_sujet = _T('monitor:alert_latence_sujet');
		$texte_corps = _T('monitor:alert_latence_corps', array('url_site' => $href));
	} elseif ($type=='restart') {
		$texte_sujet = _T('monitor:alert_restart_sujet');
		$texte_corps = _T('monitor:alert_restart_corps', array('url_site' => $href));
	} else {
		$texte_sujet = _T('monitor:alert_couper_sujet');
		$texte_corps = _T('monitor:alert_couper_corps', array('url_site' => $href));
	}

	// On supprime les notifications la nuit
	$now = time();
	$date_soir = strtotime('10pm', time());
	$date_matin = strtotime('8am', time());

	if ($now >= $date_matin and $now <= $date_soir) {
		notification_envoyer($texte_sujet, $texte_corps);
	}

	return false;
}

/**
 * Notification par email des sites tombés pendant la nuit
 *
 * @return string envoi de la notification de la nuit
 *
 */
function monitor_notification_recap_matin() {

	$now = time();
	$date_matin_debut = strtotime('8am', time());
	$date_matin_fin = strtotime('8:10am', time());

	if ($now >= $date_matin_debut and $now <= $date_matin_fin) {

		$sites = sql_allfetsel('monitor.id_syndic, site.url_site', 'spip_monitor as monitor left join spip_syndic as site on monitor.id_syndic = site.id_syndic', 'monitor.type = "ping" and monitor.statut = "oui" and alert >= 5');

		$site = array();
		foreach ($sites as $site) {
			array_push($site, $site['url_site']);
		}
		
		if ($site['url_site']) {
			$texte_sujet = _T('monitor:alert_latence_recap_sujet');
			$texte_corps = _T('monitor:alert_latence_recap_corps', array('url_sites' => $site['url_site']));
			notification_envoyer($texte_sujet, $texte_corps);
		}
	}

	return false;
}

/**
 * Insertion des données de monitoring
 *
 * @param string $id_syndic
 * @param string $statut
 * @param string $log
 * @param string $valeur
 * @param string $alert
 * @return string resultat
 */
function genie_monitor_insert($id_syndic, $statut, $log, $valeur, $alert) {
	// Insert les data dans monitor_log
	$insert_ping = sql_insertq('spip_monitor_log', array('id_syndic' => $id_syndic, 'statut' => $statut, 'log' => ($log ? 'oui' : 'non'), 'valeur' => $valeur));
	if (is_numeric($insert_ping) && $insert_ping > 0) {
		// Updater champs date_ping dans spip_syndic
		sql_updateq('spip_syndic', array('date_ping' => date('Y-m-d H:i:s'), 'statut_log' => ($log ? 'oui' : 'non')), 'id_syndic=' . intval($id_syndic));
		// On insére la valeur du poids et du ping courant
		spip_log($valeur, 'test.' . _LOG_ERREUR);
		spip_log($statut, 'test.' . _LOG_ERREUR);
		sql_updateq('spip_monitor', array('alert' => $alert, 'valeur_courant' => $valeur), 'type=' . sql_quote($statut) . ' and id_syndic=' . intval($id_syndic));
		$alert = sql_getfetsel('alert', 'spip_monitor', 'id_syndic=' . intval($id_syndic));
	}
}

/**
 * Tâche de fond pour le monitoring
 *
 * @param string $t
 *
 */
function genie_monitor_dist($t) {

	if (lire_config('monitor/activer_monitor') == 'oui') {
		include_spip('lib/Monitor/MonitorSites');

		$nb_site = lire_config('monitor/nb_site');
		if (!$nb_site) {
			$nb_site = 5;
		}
		
		// On limite le genie à l'adresse ip du serveur pour ne pas embêter les utilisateurs
		if ($_SERVER['REMOTE_ADDR'] == $_SERVER['SERVER_ADDR']) {

			// Aller chercher les derniers ping dans spip_syndic
			$sites = sql_allfetsel('monitor.id_syndic, site.url_site', 'spip_monitor as monitor left join spip_syndic as site on monitor.id_syndic = site.id_syndic', 'monitor.type = "ping" and monitor.statut = "oui"', '', 'site.date_ping ASC', '0,'.$nb_site.'');

			foreach ($sites as $site) {
				$result = updateWebsite($site['url_site']);

				if ($result['result']==false) {
					// Gestion des alertes
					// Prendre la dernière valeur d'un site
					$alert_site = sql_getfetsel('alert', 'spip_monitor', 'id_syndic=' . $site['id_syndic'] . ' order by maj DESC limit 0,1');
					$alert = $alert_site+1;

					// Insert les data dans monitor_log
					genie_monitor_insert($site['id_syndic'], 'ping', ($result['result'] ? 'oui' : 'non'), $result['latency'], $alert);
					if ($alert >= 5 && $alert <= 10) {
						$drapeau = sql_getfetsel('monitor_evenements', 'spip_syndic', 'id_syndic=' . $site['id_syndic']);
						if ($drapeau == 0) {
							// On log l'événement pour le site malade
							sql_insertq('spip_monitor_evenements', array('id_syndic' => $site['id_syndic'], 'log' => 'down', 'maj'=>date('Y-m-d H:i:s')));
							// On met un drapeau sur l'évenement pour ne pas répéter que le site est malade
							sql_updateq('spip_syndic', array('monitor_evenements' => 1), 'id_syndic=' . $site['id_syndic']);
							// Notification du site malade
							notification_monitor($site['url_site'], 'down');
						}
					}
				} else {

					// Gestion des alertes
					// Prendre la dernière valeur d'un site
					$alert_site = sql_getfetsel('alert', 'spip_monitor', 'id_syndic=' . $site['id_syndic'] . ' order by maj DESC limit 0,1');

					if ($result['latency'] >= 10) {
						$alert = $alert_site+1;
						// Insert les data dans monitor_log
						genie_monitor_insert($site['id_syndic'], 'ping', ($result['result'] ? 'oui' : 'non'), $result['latency'], $alert);
					} elseif ($result['latency'] >= 10 && $alert_site >= 5) {
						// Insert les data dans monitor_log
						$alert = $alert_site+1;
						genie_monitor_insert($site['id_syndic'], 'ping', ($result['result'] ? 'oui' : 'non'), $result['latency'], $alert);
						$drapeau = sql_getfetsel('monitor_evenements', 'spip_syndic', 'id_syndic=' . $site['id_syndic']);
						if ($drapeau == 0) {
							// On log l'événement pour le site malade
							sql_insertq('spip_monitor_evenements', array('id_syndic' => $site['id_syndic'], 'log' => 'down', 'maj'=>date('Y-m-d H:i:s')));
							// Notification du site malade
							notification_monitor($site['url_site'], 'latence');
						}
					} else {
						if ($alert_site>=5) {
							$drapeau = sql_getfetsel('monitor_evenements', 'spip_syndic', 'id_syndic=' . $site['id_syndic']);
							if ($drapeau == 1) {
								// On log l'événement pour le site reparti
								sql_insertq('spip_monitor_evenements', array('id_syndic' => $site['id_syndic'], 'log' => 'up', 'maj'=>date('Y-m-d H:i:s')));
								// On léve le drapeau quand le site est reparti
								sql_updateq('spip_syndic', array('monitor_evenements' => 0), 'id_syndic=' . $site['id_syndic']);
								// Notification du site up
								notification_monitor($site['url_site'], 'restart');
							}
						}
						$alert = 0;
						// Insert les data dans monitor_log
						genie_monitor_insert($site['id_syndic'], 'ping', ($result['result'] ? 'oui' : 'non'), $result['latency'], $alert);
					}
				}
			}

			// Aller chercher le poids dans spip_syndic
			$sites = sql_allfetsel('monitor.id_syndic, site.url_site', 'spip_monitor as monitor left join spip_syndic as site on monitor.id_syndic = site.id_syndic', 'monitor.type = "poids" and monitor.statut = "oui"', '', 'site.date_ping ASC', '0,'.$nb_site.'');

			foreach ($sites as $site) {
				$result = sizePage($site['url_site']);
				if ($result['result']!=false) {
					// Insert les data dans monitor_log
					$insert_poids = sql_insertq('spip_monitor_log', array('id_syndic' => $site['id_syndic'], 'statut' => 'poids', 'log' => ($result['result'] ? 'oui' : 'non'), 'valeur' => $result['poids']));
					// Prendre la dernière valeur des alertes d'un site pour éviter d'écraser l'alerte de latence
					$alert_site = sql_getfetsel('alert', 'spip_monitor', 'id_syndic=' . $site['id_syndic'] . ' order by maj DESC limit 0,1');
					genie_monitor_insert($site['id_syndic'], 'poids', ($result['result'] ? 'oui' : 'non'), $result['poids'], $alert_site);
				}
			}

			// Archive logs
			// On supprime les logs de plus d'un an
			$date_delete = date('Y-m-d', strtotime('-12 month', time()));
			sql_delete('spip_monitor', ' statut = "oui" and maj < '.sql_quote($date_delete));
			sql_delete('spip_monitor_log', 'maj < '.sql_quote($date_delete));
			
			monitor_optimiser_sites_effaces();
			monitor_notification_recap_matin();
		}
	}
}

/**
 * Supprimer les donnees pour les sites refusés de plus de 3 mois
 *
 */
function monitor_optimiser_sites_effaces() {

	$date_delete = date('Y-m-d', strtotime('-3 month', time()));
	$id_syndic = sql_getfetsel('id_syndic', 'spip_syndic', 'statut=' . sql_quote('refuse') . ' and maj < ' . sql_quote($date_delete));

	if ($id_syndic) {
		sql_delete('spip_monitor', 'id_syndic=' . intval($id_syndic));
		sql_delete('spip_monitor_log', 'id_syndic=' . intval($id_syndic));
	}

	return false;
}

<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Notification par email des sites à problème
 *
 * @return string resultat
 */
function notification_monitor($href) {
        $sujet = "[" . $GLOBALS['meta']["nom_site"] ."] " . _T('monitor:alert_latence_sujet');
        $body = _T('monitor:alert_latence_corps', array('url_site' => $href));
        $envoyer_mail = charger_fonction('envoyer_mail','inc');
        $envoyer_mail($GLOBALS['meta']['email_webmaster'], $sujet, $body);

        return false;
}


// http://doc.spip.org/@genie_monitor_insert
function genie_monitor_insert($id_syndic, $statut, $log, $valeur, $alert) {
    // Insert les data dans monitor_log
    $insert_ping = sql_insertq('spip_monitor_log', array('id_syndic' => $id_syndic, 'statut' => $statut, 'log' => ($log ? "oui" : "non"), 'valeur' => $valeur));
    if(is_numeric($insert_ping) && $insert_ping > 0) {
        // Updater champs date_ping dans spip_syndic
        sql_updateq('spip_syndic', array('date_ping' => date('Y-m-d H:i:s'), 'statut_log' => ($log ? "oui" : "non")), 'id_syndic=' . intval($id_syndic));
        sql_updateq('spip_monitor', array('alert' => $alert), 'id_syndic=' . intval($id_syndic));
        $alert = sql_getfetsel('alert', 'spip_monitor', 'id_syndic=' . intval($id_syndic));
    }
}

// http://doc.spip.org/@genie_monitor_dist
function genie_monitor_dist($t) {

    if (lire_config('monitor/activer_monitor') == "oui") {
        
        include_once(find_in_path("lib/Monitor/MonitorSites.php"));

        // Aller chercher les 5 dernier ping dans spip_syndic
        $sites = sql_allfetsel('monitor.id_syndic, site.url_site', 'spip_monitor as monitor left join spip_syndic as site on monitor.id_syndic = site.id_syndic', 'monitor.type = "ping" and monitor.statut = "oui" and site.statut="publie"', '', 'site.date_ping ASC', '0,5');

        foreach ($sites as $site) {
            $result = updateWebsite($site['url_site']);
            spip_log($site['url_site'], 'test.' . _LOG_ERREUR);
            spip_log($result['result'], 'test.' . _LOG_ERREUR);

	        if($result['result']==false) {

		    	// Gestion des alertes
			    // Prendre la dernière valeur d'un site
		        $alert_site = sql_getfetsel('alert', 'spip_monitor', 'id_syndic=' . $site['id_syndic'] . ' order by maj DESC limit 0,1');
		        $alert = $alert_site+1;
		        spip_log($alert, 'test.' . _LOG_ERREUR);
		        // Insert les data dans monitor_log
		        genie_monitor_insert($site['id_syndic'], 'ping', ($result['result'] ? "oui" : "non"), $result['latency'], $alert);                
		        if($alert >= 5) {
			    	// Notification du site malade
			    	notification_monitor($site['url_site']);
		        }

	    	} else {

	            // Gestion des alertes
	            // Prendre la dernière valeur d'un site
	            $alert_site = sql_getfetsel('alert', 'spip_monitor', 'id_syndic=' . $site['id_syndic'] . ' order by maj DESC limit 0,1');

	            if($result['latency'] >= 10 && $alert_site >= 0) {
	                $alert = $alert_site+1;
	                // Insert les data dans monitor_log
	                genie_monitor_insert($site['id_syndic'], 'ping', ($result['result'] ? "oui" : "non"), $result['latency'], $alert);                
	            } elseif($result['latency'] >= 10 && $alert_site >= 5) {
	            	// Notification du site malade
					notification_monitor($site['url_site']);
	                // Insert les data dans monitor_log
	                $alert = $alert_site+1;
	                genie_monitor_insert($site['id_syndic'], 'ping', ($result['result'] ? "oui" : "non"), $result['latency'], $alert);
	            } else {
	                $alert = 0;
	                // Insert les data dans monitor_log
	                genie_monitor_insert($site['id_syndic'], 'ping', ($result['result'] ? "oui" : "non"), $result['latency'], $alert);
	            }

		    }
        }

        // Aller chercher les 5 dernier poids dans spip_syndic
        $sites = sql_allfetsel('monitor.id_syndic, site.url_site', 'spip_monitor as monitor left join spip_syndic as site on monitor.id_syndic = site.id_syndic', 'monitor.type = "poids" and monitor.statut = "oui" and site.statut="publie"', '', 'site.date_ping ASC', '0,5');

        foreach ($sites as $site) {
            $result = sizePage($site['url_site']);

            if($result['result']!=false) {
            	// Insert les data dans monitor_log
            	$insert_poids = sql_insertq('spip_monitor_log', array('id_syndic' => $site['id_syndic'], 'statut' => 'poids', 'log' => ($result['result'] ? "oui" : "non"), 'valeur' => $result['poids']));
            }
        }

        // Archive logs
        // On supprime les logs de plus de 6 mois
        $date_delete = date('Y-m-d', strtotime('-6 month', time()));
        $logs_sites = sql_allfetsel('id_monitor', 'spip_monitor', ' statut = "oui" and maj < "' . $date_delete . '"');
        foreach ($logs_sites as $id_site) {
            sql_delete('spip_monitor', 'id_monitor=' . $id_site);
        }

        if (time() >= _TIME_OUT)
            return 0;
    }

}

?>

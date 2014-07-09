<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


// http://doc.spip.org/@genie_monitor_insert
function genie_monitor_insert($id_syndic, $statut, $log, $valeur, $alert) {
    // Insert les data dans monitor_log
    $insert_ping = sql_insertq('spip_monitor_log', array('id_syndic' => $id_syndic, 'statut' => $statut, 'log' => ($log ? "oui" : "non"), 'valeur' => $valeur));
    if(is_numeric($insert_ping) && $insert_ping > 0) {
        // Updater champs date_ping dans spip_syndic
        sql_updateq('spip_syndic', array('date_ping' => date('Y-m-d H:i:s'), 'statut_log' => ($log ? "oui" : "non"), 'alert' => $alert), 'id_syndic=' . intval($id_syndic));
        spip_log($alert, 'test.' . _LOG_ERREUR);
    }
}

// http://doc.spip.org/@genie_monitor_dist
function genie_monitor_dist($t) {

    if (lire_config('monitor/activer_monitor') == "oui") {
        
        include_once(_DIR_PLUGIN_MONITOR."lib/Monitor/MonitorSites.php");

        // Aller chercher les 5 dernier ping dans spip_syndic
        $sites = sql_allfetsel('monitor.id_syndic, site.url_site', 'spip_monitor as monitor left join spip_syndic as site on monitor.id_syndic = site.id_syndic', 'monitor.type = "ping" and monitor.statut = "oui"', '', 'site.date_ping ASC', '0,5');

        foreach ($sites as $site) {
            $result = updateWebsite($site['url_site']);

            // Gestion des alertes
            // Prendre la dernière valeur d'un site
            $alert_site = sql_getfetsel('alerts', 'spip_monitor_log', 'id_syndic=' . $site['id_syndic'] . ' order by maj DESC limit 0,1');
            

            if($result['latency'] >= 10 && $alert_site == 0) {
                $alert = $alert_site+1;
                // Insert les data dans monitor_log
                genie_monitor_insert($site['id_syndic'], 'ping', ($result['result'] ? "oui" : "non"), $result['latency'], $alert);                
            } elseif($result['latency'] >= 10 && $alert_site == 5) {
                $sujet = "[#NOM_SITE_SPIP] Alert latence";
                $body = "Bonjour,\n Je suis le robot qui vérifie la latence des site internet.\r\n
                         Le site " . $site['url_site'] . " rencontre un latence de plus de 10.\r\n
                         Passe une bonne journée,\n
                         Nono";
                $envoyer_mail = charger_fonction('envoyer_mail','inc');
                $envoyer_mail(_TEST_EMAIL_DEST, $sujet, $body);
                // Insert les data dans monitor_log
                genie_monitor_insert($site['id_syndic'], 'ping', ($result['result'] ? "oui" : "non"), $result['latency'], $alert);
            } else {
                $alert = 0;
                // Insert les data dans monitor_log
                genie_monitor_insert($site['id_syndic'], 'ping', ($result['result'] ? "oui" : "non"), $result['latency'], $alert);
            }
            spip_log($alert, 'test.' . _LOG_ERREUR);

        }

        // Aller chercher les 5 dernier poids dans spip_syndic
        $sites = sql_allfetsel('monitor.id_syndic, site.url_site', 'spip_monitor as monitor left join spip_syndic as site on monitor.id_syndic = site.id_syndic', 'monitor.type = "poids" and monitor.statut = "oui"', '', 'site.date_ping ASC', '0,5');

        foreach ($sites as $site) {
            $result = sizePage($site['url_site']);

            // Insert les data dans monitor_log
            $insert_poids = sql_insertq('spip_monitor_log', array('id_syndic' => $site['id_syndic'], 'statut' => 'poids', 'log' => ($result['result'] ? "oui" : "non"), 'valeur' => $result['poids']));
        }

        // Aller chercher les 5 dernier poids dans spip_syndic
        $sites = sql_allfetsel('monitor.id_syndic, site.url_site', 'spip_monitor as monitor left join spip_syndic as site on monitor.id_syndic = site.id_syndic', 'monitor.type = "poids" and monitor.statut = "oui"', '', 'site.date_ping ASC', '0,5');

        foreach ($sites as $site) {
            $result = getPage($site['url_site']);
            spip_log($result, 'test.' . _LOG_ERREUR);

            // Insert les data dans monitor_log
            // $insert_poids = sql_insertq('spip_monitor_log', array('id_syndic' => $site['id_syndic'], 'statut' => 'poids', 'log' => ($result['result'] ? "oui" : "non"), 'valeur' => $result['poids']));
        }

        return 0;
    }

}

?>
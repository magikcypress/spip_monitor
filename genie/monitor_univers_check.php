<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// http://doc.spip.org/@genie_monitor_univers_check
// Source: http://zone.spip.org/trac/spip-zone/browser/_dev_/univers_spip/
function genie_monitor_univers_check_dist($t) {
    include_spip('inc/univers_analyser');

    $nb_site = lire_config('monitor/nb_site');
    if(!$nb_site) $nb_site = 5;

    // On insert 5 sites qui ne sont pas encore traités
    $sites = sql_allfetsel('monitor.id_syndic, site.statut_stats,  site.url_site', 'spip_monitor as monitor left join spip_syndic as site on monitor.id_syndic = site.id_syndic AND monitor.type = "ping" AND site.statut="publie"', 'site.statut_stats!="oui"', '', '', '0,'.$nb_site.'');
    foreach ($sites as $row) {
        univers_analyser_un($row);
    }

    $il_y_a_une_heure = date('Y-m-d H:i:s',time()-3600);
    // 2 sites en attente de validation
    $sites = sql_allfetsel('monitor_stats.id_syndic, site.statut_stats, site.url_site', 'spip_monitor_stats as monitor_stats left join spip_syndic as site on monitor_stats.id_syndic = site.id_syndic', 'site.statut="publie" AND site.statut_stats="oui" AND (monitor_stats.retry=0 OR monitor_stats.date<'.sql_quote($il_y_a_une_heure).')', '', 'monitor_stats.date,monitor_stats.retry', '0,2');
    foreach ($sites as $row) {
        univers_analyser_un($row);
    }

    $il_y_a_quatre_heure = date('Y-m-d H:i:s',time()-4*3600);
    // revisiter 5 sites deja vu, en commencant par les plus anciens
    $sites = sql_allfetsel('monitor_stats.id_syndic, site.statut_stats, site.url_site', 'spip_monitor_stats as monitor_stats left join spip_syndic as site on monitor_stats.id_syndic = site.id_syndic', 'site.statut="publie" AND site.statut_stats="oui" AND (monitor_stats.retry=0 OR monitor_stats.date<'.sql_quote($il_y_a_une_heure).')', '', 'monitor_stats.date,monitor_stats.retry', '0,'.$nb_site.'');
    foreach ($sites as $row) {
        univers_analyser_un($row);
    }

    // revisiter un site publie, en retry de plus de 4 heures
    $sites = sql_allfetsel('monitor_stats.id_syndic, site.statut_stats, site.url_site', 'spip_monitor_stats as monitor_stats left join spip_syndic as site on monitor_stats.id_syndic = site.id_syndic', 'site.statut="publie" AND site.statut_stats="oui" AND (monitor_stats.retry>0 OR monitor_stats.date<'.sql_quote($il_y_a_une_heure).')', '', 'monitor_stats.date,monitor_stats.retry', '0,1');
    foreach ($sites as $row) {
        univers_analyser_un($row);
    }

    // passer a la poubelle les sites proposes sans DNS et essayes au moins 5 fois
    // sql_updateq("spip_monitor_stats",array('statut'=>'non'),"statut='non' AND retry>=5");

    // passer a la poubelle les sites morts et essayes au moins 10 fois
    // soit un propose pas vu vivant dans les 10 dernieres heures
    // soit un publie (donc vu vivant un jour) pas vu vivant dans les 40 dernieres heures
    // sql_updateq("spip_monitor_stats",array('statut'=>'non'),"statut IN ('non','oui') AND retry>=10");

    return 0;
}

?>
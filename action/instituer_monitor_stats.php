<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2014                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_instituer_monitor_stats_dist() {

	include_spip('inc/univers_analyser');

    $securiser_action = charger_fonction('securiser_action', 'inc');
    $arg = $securiser_action();

    // Mettre à jour les données techniques 
    $sites = sql_fetsel('monitor_stats.id_syndic, site.statut_stats, site.url_site', 'spip_monitor_stats as monitor_stats left join spip_syndic as site on monitor_stats.id_syndic = site.id_syndic', 'site.statut_stats="oui" AND site.id_syndic=' . intval($arg), '', '');
    univers_analyser_un($sites);

}
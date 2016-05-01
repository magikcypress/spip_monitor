<?php
/**
 * Monitor
 *
 * @plugin     Monitor
 * @copyright  2014
 * @author     cyp
 * @licence    GNU/GPL
 * @package    SPIP\action\monitor
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('lib/Monitor/univers_analyser');

function action_instituer_monitor_stats_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	// Mettre à jour les données techniques 
	$sites = sql_fetsel('monitor_stats.id_syndic, site.statut_stats, site.url_site', 'spip_monitor_stats as monitor_stats left join spip_syndic as site on monitor_stats.id_syndic = site.id_syndic', 'site.statut_stats="oui" AND site.id_syndic=' . intval($arg), '', '');
	univers_analyser_un($sites);

}
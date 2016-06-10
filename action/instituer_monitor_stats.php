<?php
/**
 * Monitor
 *
 * @plugin     Monitor
 * @copyright  2014
 * @author     cyp
 * @licence    GNU/GPL
 * @package    SPIP\action\instituer_monitor_stats
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('lib/Monitor/univers_analyser');

function action_instituer_monitor_stats_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	// Mettre à jour les données techniques
	if ($arg) {
		$site = sql_fetsel('id_syndic, statut_stats, url_site', 'spip_syndic', 'id_syndic=' . intval($arg), '', '');
	} else {
		$site = sql_fetsel('monitor_stats.id_syndic, site.statut_stats, site.url_site', 'spip_monitor_stats as monitor_stats left join spip_syndic as site on monitor_stats.id_syndic = site.id_syndic', '(site.statut_stats="oui" OR site.statut_stats="non") AND site.id_syndic=' . intval($arg), '', '');
	}

	univers_analyser_un($site);

}

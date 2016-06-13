<?php
/**
 * Monitor
 *
 * @plugin     Monitor
 * @copyright  2014
 * @author     cyp
 * @licence    GNU/GPL
 * @package    SPIP\genie\monitor_univers_check
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('lib/Monitor/univers_analyser');

/**
 * Tâche de fond pour le monitoring
 *
 * @param string $t
 *
 */
function genie_monitor_univers_check_dist($t) {

	if (lire_config('monitor/activer_monitor') == 'oui') {

		$nb_site = lire_config('monitor/nb_site', 5);

		// On limite le genie à l'adresse ip du serveur pour ne pas embêter les utilisateurs
		if ($_SERVER['REMOTE_ADDR'] == $_SERVER['SERVER_ADDR']) {
			// On insert 5 sites qui ne sont pas encore traités
			$sites = sql_allfetsel('monitor.id_syndic, site.statut_stats,  site.url_site', 'spip_monitor as monitor left join spip_syndic as site on monitor.id_syndic = site.id_syndic AND monitor.type = "ping"', 'site.statut_stats!="oui"', '', '', '0,'.$nb_site.'');
			foreach ($sites as $row) {
				univers_analyser_un($row);
			}

			$il_y_a_une_heure = date('Y-m-d H:i:s', time()-3600);
			// 2 sites en attente de validation
			$sites = sql_allfetsel('monitor_stats.id_syndic, site.statut_stats, site.url_site', 'spip_monitor_stats as monitor_stats left join spip_syndic as site on monitor_stats.id_syndic = site.id_syndic', 'site.statut_stats="oui" AND (monitor_stats.retry=0 OR monitor_stats.date<'.sql_quote($il_y_a_une_heure).')', '', 'monitor_stats.date, monitor_stats.retry', '0,2');
			foreach ($sites as $row) {
				univers_analyser_un($row);
			}

			// revisiter 5 sites deja vu, en commencant par les plus anciens
			$sites = sql_allfetsel('monitor_stats.id_syndic, site.statut_stats, site.url_site', 'spip_monitor_stats as monitor_stats left join spip_syndic as site on monitor_stats.id_syndic = site.id_syndic', 'site.statut_stats="oui" AND (monitor_stats.retry=0 OR monitor_stats.date<'.sql_quote($il_y_a_une_heure).')', '', 'monitor_stats.date, monitor_stats.retry', '0,'.$nb_site.'');
			foreach ($sites as $row) {
				univers_analyser_un($row);
			}

			$il_y_a_quatre_heures = date('Y-m-d H:i:s', time()-4*3600);
			// revisiter un site, en retry de plus de 4 heures
			$sites = sql_allfetsel('monitor_stats.id_syndic, site.statut_stats, site.url_site', 'spip_monitor_stats as monitor_stats left join spip_syndic as site on monitor_stats.id_syndic = site.id_syndic', 'site.statut_stats="oui" AND (monitor_stats.retry>0 OR monitor_stats.date<'.sql_quote($il_y_a_quatre_heures).')', '', 'monitor_stats.date, monitor_stats.retry', '0,1');
			foreach ($sites as $row) {
				univers_analyser_un($row);
			}
			return false;
		}

	}
	return false;
}

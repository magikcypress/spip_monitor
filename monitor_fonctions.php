<?php

/**
 * Pipeline pour Monitor
 *
 * @plugin     Monitor
 * @copyright  2014
 * @author     Vincent
 * @licence    GNU/GPL
 * @package    SPIP\Monitor\fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function monitor_pageSpeed($href) {

    if (lire_config('monitor/activer_monitor') == "oui") {
        
        include_once(_DIR_PLUGIN_MONITOR."lib/Monitor/MonitorSites.php");

        $result[] = getPageSpeedGoogle($href);
    }

    return $result;
}

function tableau_pagestats($texte) {

	if(isset($texte)) {
		foreach ($texte as $cle => $valeur) {
			$pagestats[$cle] = $valeur;
		}
	}
	
	return $pagestats;
}
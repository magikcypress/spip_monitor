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

function monitor_yellowLab($href) {

	if (lire_config('monitor/activer_monitor') == "oui") {
		
		include_once(_DIR_PLUGIN_MONITOR."lib/Monitor/MonitorSites.php");

		$result[] = getYellowLab($href);
	}

	return $result;
}

function monitor_tableau_yellowLab($texte) {
	if(isset($texte)) {
		foreach ($texte as $cle => $valeur) {
			$donnees[$cle] = $valeur;
		}
	}
	return $donnees;
}

function score($nombre) {
	if ($nombre > 80) return 'A';
	if ($nombre > 60) return 'B';
	if ($nombre > 40) return 'C';
	if ($nombre > 20) return 'D';
	if ($nombre > 0) return 'E';
}
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
	if (is_numeric($nombre) && $nombre > 80) return 'A';
	if (is_numeric($nombre) && $nombre > 60) return 'B';
	if (is_numeric($nombre) && $nombre > 40) return 'C';
	if (is_numeric($nombre) && $nombre > 20) return 'D';
	if (is_numeric($nombre) && $nombre > 0) return 'E';
	if (is_numeric($nombre) && $nombre == 0) return 'F';
}
<?php

/**
 * Fonctions pour Monitor
 *
 * @plugin     Monitor
 * @copyright  2014
 * @author     cyp
 * @licence    GNU/GPL
 * @package    SPIP\Monitor\fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * fonction sous jacente pour les 3 criteres
 * fusion_par_semaine, fusion_par_mois, fusion_par_annee
 * 
 * @param string $format
 * @param strinf $as
 * @param string $idb
 * @param object $boucles
 * @param object $crit
 */
function monitor_critere_diff_xx($format, $idb, &$boucles, $crit, $date){
	$boucles[$idb]->where[] = array("'>='", "maj", '"' . sql_quote($date) . '"');
}

/**
 * {diff_par_semaine maj}
 * 
 * @param string $idb
 * @param object $boucles
 * @param object $crit
 */
function critere_diff_par_semaine_dist($idb, &$boucles, $crit) {
	$date = date('Y-m-d', strtotime('-7 day', time()));
	monitor_critere_diff_xx('%Y-%m-%d',$idb, $boucles, $crit, $date);
}

/**
 * {diff_par_mois maj}
 *
 * @param string $idb
 * @param object $boucles
 * @param object $crit
 */
function critere_diff_par_mois_dist($idb, &$boucles, $crit) {
	$date = date('Y-m-d', strtotime('-1 month', time()));
	monitor_critere_diff_xx('%Y-%m',$idb, $boucles, $crit, $date);
}

/**
 * {diff_par_annee maj}
 *
 * @param string $idb
 * @param object $boucles
 * @param object $crit
 */
function critere_diff_par_annee_dist($idb, &$boucles, $crit) {
	$date = date('Y-m-d', strtotime('-1 year', time()));
	monitor_critere_diff_xx('%Y',$idb, $boucles, $crit, $date);
}

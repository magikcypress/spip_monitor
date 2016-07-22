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

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

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
function monitor_critere_diff_xx($format, $idb, &$boucles, $crit, $date) {
	$boucles[$idb]->where[] = array("'>='", 'maj', '"' . sql_quote($date) . '"');
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
	monitor_critere_diff_xx('%Y-%m-%d', $idb, $boucles, $crit, $date);
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
	monitor_critere_diff_xx('%Y-%m', $idb, $boucles, $crit, $date);
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
	monitor_critere_diff_xx('%Y', $idb, $boucles, $crit, $date);
}

/**
 * Afficher tous les points des graphs
 *
 * @param int $id_syndic
 * @param string $type
 * @param string $periode
 */
function graph_tout($id_syndic, $type, $periode) {

	switch ($periode) {
		case 'semaine':
			$periode = 'DATE_SUB(NOW(), INTERVAL 1 WEEK)';
		break;
		case 'mois':
			$periode = 'DATE_SUB(NOW(), INTERVAL 1 MONTH)';
		break;
		case 'annee':
			$periode = 'DATE_SUB(NOW(), INTERVAL 1 YEAR)';
		break;
		default:
			$periode = 'DATE_SUB(NOW(), INTERVAL 1 WEEK)';
		break;
	}

	$graph_tout = sql_allfetsel('DATE_FORMAT(maj, "%Y-%m-%d %H:%i:%s") AS label, valeur AS value', 'spip_monitor_log', 'statut=' . sql_quote($type) . ' and maj >= ' . $periode . ' and id_syndic=' . intval($id_syndic));
	$graph_tout = json_encode($graph_tout, true);

	return $graph_tout;
}

/**
 * Afficher la moyenne des points des graphs par jour
 *
 * @param int $id_syndic
 * @param string $type
 * @param string $periode
 */
function graph_moyenne($id_syndic, $type, $periode) {

	switch ($periode) {
		case 'semaine':
			$periode = 'DATE_SUB(NOW(), INTERVAL 1 WEEK)';
		break;
		case 'mois':
			$periode = 'DATE_SUB(NOW(), INTERVAL 1 MONTH)';
		break;
		case 'annee':
			$periode = 'DATE_SUB(NOW(), INTERVAL 1 YEAR)';
		break;
		default:
			$periode = 'DATE_SUB(NOW(), INTERVAL 1 WEEK)';
		break;
	}

	$graph_moyenne = sql_allfetsel('DATE_FORMAT(maj, "%Y-%m-%d %H:%i:%s") AS label, CAST(AVG(valeur) AS DECIMAL(10,4)) AS value', 'spip_monitor_log', 'statut=' . sql_quote($type) . ' and maj >= ' . $periode . ' and id_syndic=' . intval($id_syndic), 'DATE_FORMAT(maj, "%Y-%m-%d")');
	$graph_moyenne = json_encode($graph_moyenne, true);

	return $graph_moyenne;
}

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

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * fonction sous jacente pour les 3 criteres
 * fusion_par_jour, fusion_par_mois, fusion_par_annee
 * 
 * @param string $format
 * @param strinf $as
 * @param string $idb
 * @param object $boucles
 * @param object $crit
 */
function monitor_critere_diff_xx($format, $as, $idb, &$boucles, $crit){
	$boucle = &$boucles[$idb];
	$type = $boucle->type_requete;
	$_date = isset($crit->param[0]) ? calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent)
	  : "'".(isset($GLOBALS['table_date'][$type])?$GLOBALS['table_date'][$type]:"date")."'";

	$date = $boucle->id_table. '.' .substr($_date,1,-1);

	// annuler une eventuelle fusion sur cle primaire !
	foreach($boucles[$idb]->group as $k=>$g)
		if ($g==$boucle->id_table.'.'.$boucle->primary)
			unset($boucles[$idb]->group[$k]);

	$type = 'ping';
	$id_syndic = 53;
	$date = date('Y-m-d', strtotime('-1 year', time()));
	$date_debut = sql_getfetsel("maj","spip_monitor_log","id_syndic=" . $id_syndic . " and statut='" . $type . "'","","maj DESC","0,1");

	// $boucles[$idb]->group[]  = 'DATE_FORMAT('.$boucle->id_table.'.".'.$_date.'.", ' . "'$format')";
	// $boucles[$idb]->select[] = 'DATE_FORMAT('.$boucle->id_table.'.".'.$_date.'.", ' . "'$format') AS $as";
	$boucles[$idb]->group[]  = "id_syndic=" . $id_syndic . " and statut='" . $type . "' and maj<='" . $date_debut . "' and maj>='" . $date . "'");
	$boucles[$idb]->select[]  = "id_syndic=" . $id_syndic . " and statut='" . $type . "' and maj<='" . $date_debut . "' and maj>='" . $date . "'");
}

/**
 * {fusion_par_jour date_debut}
 * {fusion_par_jour date_fin}
 * 
 * @param string $idb
 * @param object $boucles
 * @param object $crit
 */
function critere_diff_jour_dist($idb, &$boucles, $crit) {
	monitor_critere_diff_xx('%Y-%m-%d','jour',$idb, $boucles, $crit);
}

/**
 * {fusion_par_mois date_debut}
 * {fusion_par_mois date_fin}
 *
 * @param string $idb
 * @param object $boucles
 * @param object $crit
 */
function critere_diff_mois_dist($idb, &$boucles, $crit) {
	monitor_critere_diff_xx('%Y-%m','mois',$idb, $boucles, $crit);
}

/**
 * {fusion_par_annee date_debut}
 * {fusion_par_annee date_fin}
 *
 * @param string $idb
 * @param object $boucles
 * @param object $crit
 */
function critere_diff_annee_dist($idb, &$boucles, $crit) {
	monitor_critere_diff_xx('%Y','annee',$idb, $boucles, $crit);
}

?>
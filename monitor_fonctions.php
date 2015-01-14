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
 * fusion_par_semaine, fusion_par_mois, fusion_par_annee
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

	if($as=="semaine") {
		$date = date('Y-m-d', strtotime('-7 day', time()));
	}
	elseif($as=="mois"){
		$date = date('Y-m-d', strtotime('-1 month', time()));
	}
	else{
		$date = date('Y-m-d', strtotime('-1 year', time()));
	}

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
	monitor_critere_diff_xx('%Y-%m-%d','semaine',$idb, $boucles, $crit);
}

/**
 * {diff_par_mois maj}
 *
 * @param string $idb
 * @param object $boucles
 * @param object $crit
 */
function critere_diff_par_mois_dist($idb, &$boucles, $crit) {
	monitor_critere_diff_xx('%Y-%m','mois',$idb, $boucles, $crit);
}

/**
 * {diff_par_semaine annee}
 *
 * @param string $idb
 * @param object $boucles
 * @param object $crit
 */
function critere_diff_par_annee_dist($idb, &$boucles, $crit) {
	monitor_critere_diff_xx('%Y','annee',$idb, $boucles, $crit);
}

?>
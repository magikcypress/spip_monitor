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

// http://doc.spip.org/@duree_affiche_graph
function duree_affiche_graph($duree,$periode,$type,$id_syndic){
    if (intval($duree))
        return $duree;

    $date_debut = sql_getfetsel("maj","spip_monitor_log","id_syndic=" . $id_syndic . " and statut='" . $type . "'","","maj DESC","0,1");

    if ($periode=='mois'){
        $date = date('Y-m-d', strtotime('-1 month', time()));
        $duree = "30";
        $aff_graph = affiche_graph($id_syndic,$date_debut,$date,$type);
        return $duree;
    } elseif ($periode=='annee') {
        $date = date('Y-m-d', strtotime('-1 year', time()));
        $duree = "365";
        $aff_graph = affiche_graph($id_syndic,$date_debut,$date,$type);
        return $duree;
    } else {
        $date = date('Y-m-d', strtotime('-1 week', time()));
        $duree = "7";
        $aff_graph = affiche_graph($id_syndic,$date_debut,$date,$type);
        return $duree;
    }

    return false;
}

// http://doc.spip.org/@affiche_graph
function affiche_graph($id_syndic, $date_debut, $date, $type){
    $result = sql_allfetsel("maj,valeur", "spip_monitor_log", "id_syndic=" . $id_syndic . " and statut='" . $type . "' and maj<='" . $date_debut . "' and maj>='" . $date . "'");

    return $result;
}

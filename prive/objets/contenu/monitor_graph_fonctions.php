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

function duree_affiche_graph($duree,$periode,$type){
    if (intval($duree))
        return $duree;

    if ($periode=='mois'){
        $debut = sql_getfetsel("maj","spip_monitor_log","statut='" . $type . "'","","maj","0,1");
        $debut = strtotime($debut);
        $duree = ceil((time()-$debut)/24/3600);
        return $duree;
    } elseif ($periode=='annee') {
        $debut = sql_getfetsel("maj","spip_monitor_log","statut='" . $type . "'","","maj","0,1");
        $debut = strtotime($debut);
        $duree = ceil((time()-$debut)/3600);
        return $duree;
    } else {
        $debut = sql_getfetsel("maj","spip_monitor_log","statut='" . $type . "'","","maj","0,1");
        $debut = strtotime($debut);
        $duree = ceil((time()-$debut)/30/24/3600);
        return $duree;
    }

    return $duree;
}
<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2013                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined('_ECRIRE_INC_VERSION')) return;

// fonction pour le pipeline
function monitor_autoriser() {}

// bouton du bandeau
function autoriser_monitor_menu_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	include_spip('inc/config');
	$t = lire_config('monitor/activer_monitor');
	spip_log($t, 'test.' . _LOG_ERREUR);

	return 	(lire_config('monitor/activer_monitor') != "non");
}
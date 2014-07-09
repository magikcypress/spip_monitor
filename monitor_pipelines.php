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

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Afficher monitor milieu page site
 * @param array $flux
 * @return array
 */
function monitor_affiche_milieu($flux){

	// si on est sur un site ou il faut activer le monitor...
	if (lire_config('monitor/activer_monitor') == "oui" and trouver_objet_exec($flux['args']['exec'] == "site")){
		$id_syndic = _request('id_syndic');
		$texte = recuperer_fond(
				'prive/objets/editer/monitor',
				array(
					'id_syndic'=>$id_syndic
				)
		);
		$texte .= recuperer_fond(
				'prive/objets/contenu/monitor_details',
				array(
					'id_syndic'=>$id_syndic,
					'type'=>'ping',
					'titre'=>_T('monitor:titre_page_monitor_ping'),
				)
		);
		$texte .= recuperer_fond(
				'prive/objets/contenu/monitor_details',
				array(
					'id_syndic'=>$id_syndic,
					'type'=>'poids',
					'titre'=>_T('monitor:titre_page_monitor_poids'),
				)
		);
		$texte .= recuperer_fond(
				'prive/objets/contenu/monitor_graph',
				array(
					'id_syndic'=>$id_syndic,
					'type'=>'ping',
				)
		);
		$texte .= recuperer_fond(
				'prive/objets/contenu/monitor_graph',
				array(
					'id_syndic'=>$id_syndic,
					'type'=>'poids',
				)
		);
		if ($p=strpos($flux['data'],"<!--affiche_milieu-->"))
			$flux['data'] = substr_replace($flux['data'],$texte,$p,0);
		else
			$flux['data'] .= $texte;
	}

	// si on est sur la liste des site référencés
	if (lire_config('monitor/activer_monitor') == "oui" and trouver_objet_exec($flux['args']['exec'] == "sites")){

		$texte = recuperer_fond(
				'prive/objets/editer/monitors'
		);
		if ($p=strpos($flux['data'],"<!--affiche_milieu-->"))
			$flux['data'] = substr_replace($flux['data'],$texte,$p,0);
		else
			$flux['data'] .= $texte;
	}

	return $flux;
}

/**
 * Ajout des scripts de dc-js dans le head des pages privées
 *
 * @pipeline header_prive
 * @param  string $flux Contenu du head
 * @return string Contenu du head
 */
function monitor_insert_head_prive($flux){
	$js_crossfilter = find_in_path('lib/dc.js/web/js/crossfilter.js');
	$js_dcjs = find_in_path('lib/dc.js/web/js/dc.js');
	$flux = porte_plume_inserer_head($flux, $GLOBALS['spip_lang'], $prive=true)
		. "<script type='text/javascript' src='$js_crossfilter'></script>\n"
		. "<script type='text/javascript' src='$js_dcjs'></script>\n";

	return $flux;
}

/**
 * Ajout des CSS du monitor au head public
 *
 * Appelé aussi depuis le privé avec $prive à true.
 * 
 * @pipeline insert_head_css
 * @param string $flux  Contenu du head
 * @param  bool  $prive Est-ce pour l'espace privé ?
 * @return string Contenu du head complété
 */
function monitor_insert_head_css($flux='', $prive = false){
	include_spip('inc/autoriser');
	// toujours autoriser pour le prive.
	if ($prive or autoriser('afficher_public', 'monitor')) {
		if ($prive) {
			$cssprive = find_in_path('css/perso.css');
			$flux .= "<link rel='stylesheet' type='text/css' media='all' href='$cssprive' />\n";
		}
		$css = direction_css(find_in_path('css/perso.css'), lang_dir());
		$css_icones = generer_url_public('css/perso.css');
		if (defined('_VAR_MODE') AND _VAR_MODE=="recalcul")
			$css_icones = parametre_url($css_icones, 'var_mode', 'recalcul');
		$flux
			.= "<link rel='stylesheet' type='text/css' media='all' href='$css' />\n";
	}
	return $flux;
}

/**
 * Taches periodiques de monitor 
 *
 * @param array $taches_generales
 * @return array
 */
function monitor_taches_generales_cron($taches_generales){


	if (lire_config('monitor/activer_monitor') == "oui") {
		$taches_generales['monitor'] = 90; 
	}
		
	return $taches_generales;
}

?>
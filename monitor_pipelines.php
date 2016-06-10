<?php

/**
 * Pipeline pour Monitor
 *
 * @plugin     Monitor
 * @copyright  2014
 * @author     cyp
 * @licence    GNU/GPL
 * @package    SPIP\Monitor\pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function monitor_affiche_gauche($flux) {
	return monitor_boite_info($flux, 'affiche_gauche');
}
function monitor_affiche_droite($flux) {
	return monitor_boite_info($flux, 'affiche_droite');
}

/**
 * Afficher l'activation de monitor dans la colonne de gauche
 * @param array $flux
 * @return array
 */
function monitor_boite_info($flux, $pipeline) {
	include_spip('inc/presentation');

	$flux['args']['pipeline'] = $pipeline;

	if (lire_config('monitor/activer_monitor') == 'oui' and trouver_objet_exec($flux['args']['exec'] == 'site')) {
		$id_syndic = _request('id_syndic');
		$texte = recuperer_fond('prive/squelettes/extra/monitor', array('id_syndic'=>$id_syndic));

		$flux['data'] .= $texte;
	}

	return $flux;
}

/**
 * Afficher monitor milieu page site
 * @param array $flux
 * @return array
 */
function monitor_affiche_milieu($flux) {
	include_spip('inc/config');
	// si on est sur un site ou il faut activer le monitor...
	if (lire_config('monitor/activer_monitor') == 'oui' and trouver_objet_exec($flux['args']['exec'] == 'site')) {
		$id_syndic = _request('id_syndic');
		$id_monitor_stats = _request('id_monitor_stats');
		$periode = _request('periode');

		$texte = recuperer_fond('prive/objets/contenu/monitor_info', array('id_syndic'=>$id_syndic, 'id_monitor_stats'=>$id_monitor_stats));
		$texte .= recuperer_fond(
						'prive/objets/contenu/monitor_stats',
						array(
							'id_syndic'=>$id_syndic,
							'id_monitor_stats'=>$id_monitor_stats
						)
		);
		$texte .= recuperer_fond(
						'prive/objets/contenu/monitor_recupstats',
						array(
							'id_syndic'=>$id_syndic,
							'id_monitor_stats'=>$id_monitor_stats
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
						'prive/objets/contenu/monitor_recupinfo',
						array(
							'id_syndic'=>$id_syndic,
							'id_monitor_stats'=>$id_monitor_stats
						)
		);
		$texte .= recuperer_fond(
						'prive/objets/contenu/monitor_graph',
						array(
							'id_syndic'=>$id_syndic,
							'type'=>'ping',
							'periode'=>$periode
						),
						'ajax'
		);
		$texte .= recuperer_fond(
						'prive/objets/contenu/monitor_graph',
						array(
							'id_syndic'=>$id_syndic,
							'type'=>'poids',
							'periode'=>$periode
						),
						'ajax'
		);
		if ($p=strpos($flux['data'], '<!--affiche_milieu-->')) {
			$flux['data'] = substr_replace($flux['data'], $texte, $p, 0);
		} else {
			$flux['data'] .= $texte;
		}
	}

	// si on est sur la liste des sites référencés
	if (lire_config('monitor/activer_monitor') == 'oui' and trouver_objet_exec($flux['args']['exec'] == 'sites')) {

		$texte = recuperer_fond(
						'prive/objets/editer/monitors'
		);
		if ($p=strpos($flux['data'], '<!--affiche_milieu-->')) {
			$flux['data'] = substr_replace($flux['data'], $texte, $p, 0);
		} else {
			$flux['data'] .= $texte;
		}
	}

	return $flux;
}


/**
 * Définition des messages de compagnon par défaut en fonction
 *
 * Retourne une liste de messages d'aides en fonction du pipeline
 * demandé
 *
 * @pipeline compagnon_messages
 *
 * @param array $flux
 *     Données du pipeline
 * @return array $flux
 *     Données du pipeline
 **/
function monitor_compagnon_messages($flux) {

	$exec = $flux['args']['exec'];
	$pipeline = $flux['args']['pipeline'];
	$vus = $flux['args']['deja_vus'];
	$aides = &$flux['data'];

	switch ($pipeline) {

		case 'affiche_milieu':
			switch ($exec) {

				case 'liste_monitor':
					// eviter si possible une requete sql.
					if (!isset($vus['liste_monitor']) and !sql_countsel('spip_monitor')) {
						$aides[] = array(
							'id' => 'liste_monitor',
							'titre' => _T('monitor:c_sites_monitores'),
							'texte' => _T('monitor:c_sites_monitores_texte'),
							'statuts' => array('webmestre')
						);
					}
					break;
			}
			break;

	}

	return $flux;
}

/**
 * Ajoute les css pour monitor chargées dans le privé
 * 
 * @param string $flux Contenu du head HTML concernant les CSS
 * @return string       Contenu du head HTML concernant les CSS
 */
function monitor_header_prive_css($flux) {

	$css = find_in_path('css/monitor.css');
	$flux .= "<link rel='stylesheet' type='text/css' media='all' href='".direction_css($css)."' />\n";

	return $flux;
}

/**
 * Ajoute les css pour monitor chargées dans le public
 * 
 * @param string $flux Contenu du head HTML concernant les CSS
 * @return string       Contenu du head HTML concernant les CSS
**/
function monitor_insert_head_css($flux) {
	include_spip('inc/config');
	if (lire_config('monitor/activer_monitor') == 'oui') {
		$css = find_in_path('css/monitor.css');
		$flux .= '<link rel="stylesheet" href="'.direction_css($css).'" type="text/css" media="all" />';
	}
	return $flux;
}

/**
 * Taches periodiques de monitor 
 *
 * @param array $taches_generales
 * @return array
 */
function monitor_taches_generales_cron($taches_generales) {
	include_spip('inc/config');
	if (lire_config('monitor/activer_monitor') == 'oui') {
		$taches_generales['monitor'] = 90;
		$taches_generales['monitor_univers_check'] = 90;
	}

	return $taches_generales;
}

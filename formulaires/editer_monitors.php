<?php
/**
 * Monitor
 *
 * @plugin     Monitor
 * @copyright  2014
 * @author     cyp
 * @licence    GNU/GPL
 * @package    SPIP\formulaires\monitor
 */

/**
 * Gestion du formulaire de monitoring des sites 
 *
 * @package SPIP\Formulaires
**/
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Chargement du formulaire de configuration du monitoring des sites
 *
 * @return array
 *     Environnement du formulaire
**/
function formulaires_editer_monitors_charger_dist() {

	$valeurs = array();
	$types = array('ping', 'poids');
	foreach ($types as $key) {
		$valeurs['compteur_' . $key] = sql_countsel('spip_monitor', 'type=' . sql_quote($key) . ' and statut="oui"');
	}

	return $valeurs;
	
}

/**
 * Vérifications du formulaire de configuration du monitoring des sites
 *
 * @return array
 *     Tableau des erreurs
**/
function formulaires_editer_monitors_verifier_dist() {
	$erreurs = array();
			
	return $erreurs;
}

/**
 * Traitement du formulaire de configuration du monitoring des sites
 *
 * @return array
 *     Retours du traitement
**/
function formulaires_editer_monitors_traiter_dist() {

	$syndic = sql_allfetsel('id_syndic', 'spip_monitor', 'id_syndic=' . $id_syndic);

	foreach (array('ping', 'poids') as $key) {
		$type = sql_getfetsel('id_syndic', 'spip_monitor', 'id_syndic=' . intval($id_syndic) . ' and type=' . sql_quote($key));
		if (!$type) {
			sql_insertq('spip_monitor', array('id_syndic' => intval($id_syndic), 'statut' => _request('activer_monitor_'. $key) ,'type' => $key, 'date_modif' => date('Y-m-d H:i:s')));
			sql_updateq('spip_syndic', array('statut_log' => 'non', 'statut_stats' => 'non'), 'id_syndic = ' . intval($id_syndic));
		} else {
			sql_updateq('spip_monitor', array('statut' => _request('activer_monitor_' . $key)), 'id_syndic=' . intval($id_syndic) . ' and type = ' . sql_quote($key));
		}
	}
		
	return array('message_ok'=>_T('config_info_enregistree'));
}

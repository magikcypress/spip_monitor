<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_core_/plugins/monitor/lang/
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'aucun_resultat' => 'Aucun résultat',
	'aucun_evenement' => 'Aucun évenement pour le moment',
	'activer_monitor' => 'Activer',
	'activer_monitor_ping' => 'Activer le ping',
	'activer_monitor_poids' => 'Activer le poids des pages',
	'afficher_plugins' => 'Afficher la liste des plugins',
	'alert_affichage_milieu' => 'Le nombre d\'alerte est de @alert@ points pour ce site. Le site est peut-être coupé ou sa latence est supérieur à 10 ms.',
	'alert_couper_sujet' => 'Alerte: le site est coupé',
	'alert_couper_corps' => 'Bonjour,

Je suis le robot qui vérifie la disponibilité des sites internet via spip monitor.

Le site @url_site@ est coupé.

Passe une bonne journée,
Monitor SPIP',
	'alert_latence_sujet' => 'Alerte: Latence est < 10',
	'alert_latence_corps' => 'Bonjour,

Je suis le robot qui vérifie la latence des sites internet via spip monitor.

Le site @url_site@ rencontre une latence de plus de 10ms.

Passe une bonne journée,
Monitor SPIP',
	'alert_restart_sujet' => 'Alerte: le site est reparti',
	'alert_restart_corps' => 'Bonjour,

Je suis le robot qui vérifie la disponibilité des sites internet via spip monitor.

Le site @url_site@ est reparti.

Passe une bonne journée,
Monitor SPIP',
	'alert_latence_recap_sujet' => 'Alerte matinale: liste des sites est coupés',
	'alert_latence_recap_corps' => 'Bonjour,

Je suis le robot qui vérifie la latence des sites internet via spip monitor.

Cette nuit les sites suivants ont rencontrés un problème de latence supérieur à 10ms.

	@url_sites@

Passe une bonne journée,
Monitor SPIP',
	'alert_latence_evenement_start' => 'Le site est reparti',
	'alert_latence_evenement_stop' => 'Le site est arrêté',

	// B
	'bouton_activer_ping' => 'Activer le "ping" sur tous les sites',
	'bouton_activer_poids' => 'Activer le "poids" sur tous les sites',
	'bouton_desactiver_ping' => 'Désactiver le "ping" sur tous les sites',
	'bouton_desactiver_poids' => 'Désactiver le "poids" sur tous les sites',

	// C
	'c_sites_monitores' => 'Comment monitorer un site ?',
	'c_sites_monitores_texte' => 'Vous ne pourrez monitorer un site depuis cette page qu’à partir du moment où vous avez activé le plugins dans la configuration et que vous avez ajouté un nouveau site syndiqué. Vous pouvez syndiquer un site depuis le menu « Édition », puis « Site Référencés ».',

	// E
	'etats_sites' => 'États des sites Monitorés',
	'evenements_sites' => 'Événements des sites Monitorés',

	// L
	'legend_activer_monitor' => 'Choix d\'activer monitor',
	'legend_explication_activer_monitor' => ' ',
	'legend_explication_obligatoire_monitor' => ' ',
	'legend_explication_recommande_monitor' => ' ',
	'legend_obligatoire_monitor' => 'Variables fixes et obligatoires',
	'legend_recommande_monitor' => 'Variables optionnelles dépendant de chaque page auditée (utilisation fortement recommandée)',

	// G
	'graph_annee' => 'Par année',
	'graph_mois' => 'Par mois',
	'graph_semaine' => 'Par semaine',

	// F
	'form_date' => 'Date du relevé',
	'form_date_ping' => 'Date',
	'form_gzip' => 'Compression Gzip',
	'form_ip' => 'Adresse IP',
	'form_ping' => 'Latence (en ms)',
	'form_log_evenement' => 'Message',
	'form_nbplugins' => 'Nombre de plugins',
	'form_pays' => 'Pays d\'hébergement',
	'form_php' => 'Version de php',
	'form_poids' => 'Poids (en Kb)',
	'form_recuperer_site' => 'Récupérer la latence et le poids pour ce site',
	'form_recuperer_stats' => 'Récupérer les stats pour ce site',
	'form_resultat' => 'Résultats',
	'form_retry' => 'Retry',
	'form_server' => 'Type de serveur',
	'form_spip' => 'Version du CMS',
	'form_statstechnic' => 'Données techniques',
	'form_status' => 'Status',
	'form_url_site' => 'Nom du site',
	'form_version' => 'Version écran de sécurité',

	// I
	'icone_monitor_configuration' => 'Configurer Monitor',
	'icone_monitor_editer' => 'Lister sites Monitorés',
	'icone_monitor_etats' => 'États sites Monitorés',
	'icone_monitor_evenements' => 'Événements sites Monitorés',
	'icone_monitor_stats' => 'Stats des sites Monitorés',
	'info_site_noping' => 'Le site ne ping plus.',
	'info_site_ping' => 'Le site ping bien.',
	'item_activer_nb_site' => 'Nombre de sites a analyser par le cron (défaut: 5)',
	'item_non_utiliser_monitor' => 'Désactiver Monitor',
	'item_non_utiliser_monitor_ping' => 'Désactiver ping',
	'item_non_utiliser_monitor_poids' => 'Désactiver poids page',
	'item_utiliser_monitor' => 'Activer Monitor',
	'item_utiliser_monitor_ping' => 'Activer ping',
	'item_utiliser_monitor_poids' => 'Activer poids page',

	// M
	'monitor' => 'Sites Monitorés',
	'monitor_etats' => 'États sites Monitorés',
	'monitor_evenements' => 'Événements sites Monitorés',
	'monitor_logs' => 'Logs des sites Monitorés',
	'monitor_stats' => 'Stats des sites Monitorés',
	'monitor_stats_plugins' => 'Stats des plugins pour les sites Monitorés',

	// S
	'source_tout' => 'Voir tout',
	'source_moyenne' => 'Voir moyenne',
	'statistiques_sites' => 'Statistiques des sites monitoriés',
	'stats_affiche_bubble' => 'Afficher bubble',
	'stats_affiche_tout' => 'Afficher tout',
	'stats_affiche_version' => 'Afficher version',

	// T
	'texte_monitor' => '<p>Activer Monitor, puis renseigner le formulaire de configuration du plugin</p>',
	'texte_monitor_compteur_aucun_ping' => 'Il n\'y a aucun site qui a "ping" d\'activé sur @site_publie@ sites publiés',
	'texte_monitor_compteur_aucun_poids' => 'Il n\'y a aucun site qui a "poids" d\'activé sur @site_publie@ sites publiés',
	'texte_monitor_compteur_ping' => 'Il y a @nb@ site qui a "ping" d\'activé sur @site_publie@ sites publiés',
	'texte_monitor_compteur_poids' => 'Il y a @nb@ site qui a "poids" d\'activé sur @site_publie@ sites publiés',
	'texte_monitor_compteurs_ping' => 'Il y a @nb@ sites qui ont "ping" d\'activé sur @site_publie@ sites publiés',
	'texte_monitor_compteurs_poids' => 'Il y a @nb@ sites qui ont "poids" d\'activé sur @site_publie@ sites publiés',
	'texte_monitor_poids' => '<p>Activer le monitoring (poids page) pour ce site</p>',
	'texte_monitor_site' => '<p>Activer le monitoring en choisissant d\'activer les résultats du ping ou du poids pour ce site.</p>',
	'texte_monitor_sites' => '<p>Activer le monitoring pour tous les sites</p>',
	'titre_activer_nb_site' => 'Nombre de site',
	'titre_configurer' => 'Configurer Monitor',
	'titre_monitor' => 'Configuration du plugin Monitor',
	'titre_monitor_liste' => 'Liste des sites sous monitor',
	'titre_monitor_ping' => 'Activer le monitoring (ping) pour ce site',
	'titre_monitor_poids' => 'Activer le monitoring (poids page) pour ce site',
	'titre_monitor_site' => 'Activer Monitor pour ce site',
	'titre_monitor_sites' => 'Activer Monitor pour les sites',
	'titre_page_monitor_ping' => 'Liste des sites monitorés (ping)',
	'titre_page_monitor_poids' => 'Liste des sites monitorés (poids)',
);

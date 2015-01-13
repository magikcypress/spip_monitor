<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_core_/plugins/monitor/lang/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'activer_monitor' => 'Activer',
	'activer_monitor_ping' => 'Activer le ping',
	'activer_monitor_poids' => 'Activer le poids des pages',
	'afficher_plugins' => 'Afficher la liste des plugins',
	'alert_affichage_milieu' => 'Le nombre d\'alerte est de @alert@ points pour ce site. Le site est peut-être coupé ou sa latence est supérieur à 10 ms.',
	'alert_couper_sujet' => 'Alert: le site est coupé',
	'alert_couper_corps' => 'Bonjour,

Je suis le robot qui vérifie la disponibilité des sites internet via spip_monitor.

Le site @url_site@ est coupé.

Passe une bonne journée,
Nono',
	'alert_latence_sujet' => 'Alert: Latence est < 10',
	'alert_latence_corps' => 'Bonjour,

Je suis le robot qui vérifie la latence des sites internet via spip_monitor.

Le site @url_site@ rencontre une latence de plus de 10ms.

Passe une bonne journée,
Nono',	
	'alert_restart_sujet' => 'Alert: le site est reparti',
	'alert_restart_corps' => 'Bonjour,

Je suis le robot qui vérifie la disponibilité des sites internet via spip_monitor.

Le site @url_site@ est reparti.

Passe une bonne journée,
Nono',

	// B
	'bouton_activer_ping' => 'Activer le "ping" sur l\'ensemble des sites',
	'bouton_activer_poids' => 'Activer le "poids" sur l\'ensemble des sites',
	'bouton_desactiver_ping' => 'Désactiver le "ping" sur l\'ensemble des sites',
	'bouton_desactiver_poids' => 'Désactiver le "poids" sur l\'ensemble des sites',

	// L
	'legend_obligatoire_monitor' => 'Variables fixes et obligatoires',
	'legend_explication_obligatoire_monitor' => ' ',
	'legend_recommande_monitor' => 'Variables optionnelles dépendant de chaque page auditée (utilisation fortement recommandée)',
	'legend_explication_recommande_monitor' => ' ',
	'legend_activer_monitor' => 'Choix d\'activer monitor',
	'legend_explication_activer_monitor' => ' ',

	// G
	'graph_annee' => 'Par année',
	'graph_mois' => 'Par mois',
	'graph_semaine' => 'Par semaine',

	// F
	'form_date' => 'Date du relevé',
	'form_date_ping' => 'Date',
	'form_ip' => 'Adresse IP',
	'form_gzip' => 'Compression Gzip',
	'form_latence' => 'Latence (en ms)',
	'form_minifycss' => 'Minifycss',
	'form_minifyhtml' => 'MinifyHTML',
	'form_minifyjavascript' => 'Minifyjavascript',
	'form_nbplugins' => 'Nombre de plugins',
	'form_pagestats' => 'PageStats',
	'form_pays' => 'Pays d\'hébergement',
	'form_poids' => 'Poids (en Kb)',
	'form_php' => 'Version de php',
	'form_recuperer_stats' => 'Récupérer les stats pour ce site',
	'form_recuperer_site' => 'Récupérer la latence et le poids pour ce site',
	'form_resultat' => 'Résultats',
	'form_retry' => 'Retry',
	'form_score' => 'Score',
	'form_server' => 'Nom du serveur',
	'form_spip' => 'Version du CMS',
	'form_statstechnic' => 'Données techniques',
	'form_status' => 'Status',
	'form_url_site' => 'Nom du site',
	

	// I
	'icone_monitor_configuration' => 'Configurer Monitor',
	'icone_monitor_editer' => 'Lister sites Monitorés',
	'info_site_ping' => 'Le site ping bien.',
	'info_site_noping' => 'Le site ne ping plus.',
	'item_activer_pagespeed' => 'En cochant la case vous activer les résultats de page speed Google',
	'item_utiliser_monitor' => 'Activer Monitor',
	'item_utiliser_monitor_ping' => 'Activer ping',
	'item_utiliser_monitor_poids' => 'Activer poids page',
	'item_non_utiliser_monitor' => 'Désactiver Monitor',
	'item_non_utiliser_monitor_ping' => 'Désactiver ping',
	'item_non_utiliser_monitor_poids' => 'Désactiver poids page',

	// T
	'texte_monitor' => '<p>Activer Monitor, puis renseigner le formulaire de configuration du plugin</p>',
	'texte_monitor_site' => '<p>Activer le monitoring en choisissant d\'activer les résultats du ping ou du poids pour ce site.</p>',
	'texte_monitor_sites' => '<p>Activer le monitoring pour tout les sites "publié"</p>',
	'texte_monitor_poids' => '<p>Activer le monitoring (poids page) pour ce site</p>',
	'texte_monitor_compteur_aucun_ping' => 'Il n\'y a aucun site qui a "ping" d\'activé sur @site_publie@ sites',
	'texte_monitor_compteur_aucun_poids' => 'Il n\'y a aucun site qui a "poids" d\'activé sur @site_publie@ sites',
	'texte_monitor_compteur_ping' => 'Il y a @nb@ site qui a "ping" d\'activé sur @site_publie@ sites',
	'texte_monitor_compteur_poids' => 'Il y a @nb@ site qui a "poids" d\'activé sur @site_publie@ sites',
	'texte_monitor_compteurs_ping' => 'Il y a @nb@ sites qui ont "ping" d\'activé sur @site_publie@ sites',
	'texte_monitor_compteurs_poids' => 'Il y a @nb@ sites qui ont "poids" d\'activé sur @site_publie@ sites',
	'titre_activer_pagespeed' => 'Activer page speed',
	'titre_configurer' => 'Configurer Monitor',
	'titre_monitor' => 'Configuration du plugin Monitor',
	'titre_monitor_site' => 'Activer Monitor pour ce site',
	'titre_monitor_sites' => 'Activer Monitor pour les sites',
	'titre_monitor_ping' => 'Activer le monitoring (ping) pour ce site',
	'titre_monitor_poids' => 'Activer le monitoring (poids page) pour ce site',
	'titre_page_monitor_ping' => 'Liste des sites sous monitor (ping)',
	'titre_page_monitor_poids' => 'Liste des sites sous monitor (poids)',
	'titre_pagespeed' => 'Monitor site avec pageSpeed Google',
);

SPIP monitor
=======

Monitorer site web syndiqué.

Plugin SPIP réalisé pour monitorer des sites web syndiqués.

## Necessite

Les relevés sont réalisés avec la commande curl ou par le fonction SPIP recuperer_url().

## Plugins SPIP utilisé

* sites (http://zone.spip.org/trac/spip-zone/browser/_core_/plugins/sites) 
* d3js (http://zone.spip.org/trac/spip-zone/browser/_plugins_/d3js/trunk)
* geoip (http://zone.spip.org/trac/spip-zone/browser/_plugins_/geoip/branches/v1)
* spip_bonux (http://zone.spip.org/trac/spip-zone/browser/_plugins_/spip-bonux-3)

## Ce que fait le plugin

En publiant un site web, vous avez la possibilité de le monitorer. 

Dans l'édition d'un site web vous avez 3 outils :

- Activer le ping;
- Activer le poids des pages;
- Récupération des infos importantes du site;
	- Nom du serveur
	- Adresse IP de l'hébergement
	- Version de SPIP 
	- Nombre de plugins
	- Pays de l'hébergement

### Les alertes :

Ce plugins vous alerte par email si un site est tombé ou si sa latence est supérieur à 10ms. La nuit vous êtes alerté une seule fois a partir de 22h jusqu'à 8h que le site est planté. Un récapitulatif est envoyé le matin pour signaler les sites plantés. Le reste de la journée vous êtes notifié par email si le site est arrêté ou reparti.

### Dans l'admin, vous retrouvez 

- L'état de tous les sites monitorés avec leurs latences; (Edition > Etats sites monitorés)
- Les évenements des sites retracent la vie des sites quand ils tombent et qu'ils repartent; (Edition > Evénements sites monitorés)
- Les stats globals sur les sites; (Edition > Stats sites monitorés)

Pour pouvoir monitorer les sites nous vous recommandons de mettre un cron serveur si vous en avez la possibilité.

> */5 *         * * *   root    nice /usr/bin/curl http://domain.tld/spip.php?action=super_cron >/dev/null 2>&1

# Version 1.0.x

## Version 1.0.9

* Traiter les etats par maj 
* Faire une moyens par jour/mois/année sur les graphs pour éviter de les surcharger
* Corrections bugs sur l'affichage des graphs par semaine/mois/année
* Nettoyage des fichiers inutiles
* Ajout de tableaux caché en HTML sur les graphs pour la version sans javascript

## Version 1.0.8

 (mise à jour de base, schema 1.9)
* Ajout d'un drapeau pour éviter de spammer la liste des événements
et les notifications par email
* Ajout des événements

## Version 1.0.7

* Ajout de la liste des états
* Ajout de la liste des stats dans l'interface privé
* Necessite spip-bonux 

## Version 1.0.6

* Optimisation des graphs en utilisant JSON au lieu de CSV
* Petites pétouilles
* Suppression de la lib geoIP pour necessite le plugin geoIP

## Version 1.0.5

* Déplacement de la lib geoIP dans le bon répertoire

## Version 1.0.4

* La notification matinale spam trop quand on est sur le site la nuit
* Rappeler en journée les sites down dans un email avec la liste des sites plantés
* Suppression de yellowlab
* Suppression de pagespeed
* Ajout d'un index sur le champs valeur dans spip_monitor_log pour accélerer les graphs
* On limite le lancement du cron à l'IP du serveur pour ne pas pénaliser les utilisateurs

## Version 1.0.3

* Ajout d'un index pour id_syndic sur la table spip_monitor
* Ne pas alerter des sites down la nuit sauf lors du premier plantage (5 alertes)
* Supprimer les données d'un site quand il est mis à la poubelle de plus de 3 mois
* Déplacer univers_analyse.php dans lib/Monitor, suppression du répertoire inc

## Version 1.0.2

* Fix erreur log php

## Version 1.0.1

* Faire fonctionner la suppression des monitoring plus vieux d'un an

## Version 1.0.0

* Les données techniques regroupant l'IP, la version du CMS, le nom du serveur web, la liste des plugins pour les sites propulsé par SPIP, etc ...
* Le monitoring du site qui permet de connaître la latence du site et le poids du site. Les relevés courants sont afficher sous forme d'un tableau. Vous pouvez connaître les relevés sur une plus grandes periodes sous forme de graphs. 
* Les relevés de pageSpeed Google
* Audit de la page avec l'API de Yellowlab (http://yellowlab.tools)

# TODO

* Essayer de trouver pourquoi le site est down et mettre un message explicite dans les événements
* Détecter quand le poids d'une page chute énormément (cas d'une attaque)
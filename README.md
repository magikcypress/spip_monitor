SPIP monitor
=======

Monitorer site web syndiqué.

Plugin SPIP réalisé pour monitorer des sites web syndiqués.

En publiant un site web sur spip, vous avez la possibilité de le monitorer. 

Dans l'édition d'un site web vous avez 3 outils :

- Activer le ping;
- Activer le poids des pages;
- Récupération des infos importantes du site;
	- Nom du serveur
	- Adresse IP de l'hébergement
	- Version de SPIP 
	- Nombre de plugins
	- Pays de l'hébergement

Ce plugins vous alerte si un site est tombé ou si sa latence est supérieur à 10ms. La nuit vous êtes alerté une seule fois a partir de 22h jusqu'à 8h que le site est planté. Un récapitulatif est envoyé le matin pour signaler les sites plantés. Le reste de la journée vous êtes notifié par email.

# Version 1.0.x

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

# Necessite

Les relevés sont réalisés avec la commande curl qui doit être prise en charge par le serveur. 

## Plugins SPIP

* sites (http://zone.spip.org/trac/spip-zone/browser/_core_/plugins/sites) 
* d3js (http://zone.spip.org/trac/spip-zone/browser/_plugins_/d3js/trunk)

# TODO




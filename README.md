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

Les alertes :

Ce plugins vous alerte si un site est tombé ou si sa latence est supérieur à 10ms. La nuit vous êtes alerté une seule fois a partir de 22h jusqu'à 8h que le site est planté. Un récapitulatif est envoyé le matin pour signaler les sites plantés. Le reste de la journée vous êtes notifié par email.

Dans l'admin, vous retrouvez 

- L'état de tous les sites monitorés avec leurs latences
- Les évenements des sites (quand ils tombent et qu'ils repartent)
- Les stats globals sur les sites

# Version 1.0.x

## Version 1.0.8

- Ajout des événements (mise à jour de base, schema 1.8)

## Version 1.0.7

- Ajout de la liste des états
- Ajout de la liste des stats dans l'interface privé
- Necessite spip-bonux 

## Version 1.0.6

- Optimisation des graphs en utilisant JSON au lieu de CSV
- Petites pétouilles
- Suppression de la lib geoIP pour necessite le plugin geoIP

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
* geoip (http://zone.spip.org/trac/spip-zone/browser/_plugins_/geoip/branches/v1)
* spip_bonux (http://zone.spip.org/trac/spip-zone/browser/_plugins_/spip-bonux-3)

# TODO

- Traiter les etats par maj (actuellement, se base sur maj de spip_monitor, mais devrait se passer sur maj de spip_monitor_log)
- Essayer de trouver pourquoi le site est down et mettre un message explicite dans les événements
- Faire une moyens par jour/mois/année sur les graphs pour éviter de les surcharger
- Détecter quand le poids d'une page chute énormément (cas d'une attaque)
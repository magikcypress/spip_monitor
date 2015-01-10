SPIP monitor
=======
Monitorer site web syndiqué.

Plugin SPIP réalisé pour monitorer des sites web syndiqués

En publiant un site web sur spip, vous avez la possibilité de le monitorer. 

Dans l'edition d'un site web vous avez 3 outils :

* Les données techniques regroupant l'IP, la version du CMS, le nom du serveur web, la liste des plugins pour les sites propulsé par SPIP.
* Le monitoring du site qui permet de connaître la latence du site et le poids du site. Les relevés courants sont afficher sous forme d'un tableau. Vous pouvez connaître les relevés sur une plus grandes periodes sous forme de graphs. 
* Les relevés de pageSpeed Google

# Requirements

Les relevés sont réalisés avec la commande curl qui doit être prise en charge par le serveur.

# Version 1.0.0

Necessite : 
	* sites (http://zone.spip.org/trac/spip-zone/browser/_core_/plugins/sites) 
	* d3js (https://github.com/magikcypress/spip_d3js)
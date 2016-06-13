<?php
/**
 * Univers Analyse SPIP
 *
 * @version    1.0.0
 * @copyright  2015
 * @author     Cedric, cyp 
 * @licence    GNU/GPL3
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// User agent used to load the page
@define('_INC_DISTANT_USER_AGENT', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; fr; rv:1.9.1.9) Gecko/20100315 Firefox/3.5.9');
@define('_INC_DISTANT_VERSION_HTTP', 'HTTP/1.0');

include_spip('inc/filtres');
include_spip('inc/distant');
include_spip('inc/meta');
include_spip('lib/Monitor/MonitorSites');

/**
 * Récupérer la page compléte.
 * Peut-être sub.something.tld/spip.php
 *
 *
 * @param string $url
 * @param string $cookie
 *
 */
function univers_recuperer_lapage($url, $cookie = '') {
	$site = updateWebsite($url);
	return $site;
}

/**
 * Récupérer l'adresse host
 * Utilise nslookup à la place de la fonction php
 *
 * @param string $host
 * @param int $timeout
 * @return string
 */
function univers_getaddrbyhost($host, $timeout = 3) {
	$query = `nslookup -timeout=$timeout -retry=1 $host`;
	if (preg_match('/\nAddress: (.*)\n/', $query, $matches)) {
		return trim($matches[1]);
	}
	return $host;
}

/**
 * Analyser le site et vérifier si c'est du SPIP et trouver les versions et les plugins
 *
 * @param string $url
 * @param bool $debug
 * @return boolean
 */
function univers_analyser($url, $debug = false) {
	$res = array();

	$path = parse_url($url);
	$ip = univers_getaddrbyhost($path['host']);
	if (!$ip or $ip == $path['host']) {
		return false;  // pas d'ip, pas de site, rien a faire
	}

	$res['ip'] = $ip;

	// Récupérer la page compléte
	$site = univers_recuperer_lapage($url);
	if (!$site) {
		$res['response'] = false;
		return $res;
	}

	// Récupérer lécran de sécurité
	if (function_exists('curl_init')) {
		$ecran_secu = univers_recuperer_lapage($path['scheme'] . '://' . $path['host'] . '/?test_ecran_securite=1');
		if (!$ecran_secu) {
			$res['response'] = false;
			return $res;
		}

		if (preg_match(',test\s*([0-9.]+),', $ecran_secu['result'], $r)) {
			$res['version'] = $r[1];
		}

		$header = $site['result'];

	} else {
		list($header, $page) = $site['result'];
	}

	$res = parse_header($header, $res, $page, $url);
	return $res;

}

/**
 * Parser l'entête et vérifier la version et plus d'informations
 *
 * @param string $header
 * @return array
 */
function parse_header($header, $res, $page = '', $url) {

	// Récupérer les informations génériques (serveur, php, gzip)
	if (preg_match(',Server: (.*)$,m', $header, $r)) {
		$res['server'] = $r[1];
	}
	if (preg_match(',X-Powered-By: PHP/(.+)$,m', $header, $r)) {
		$res['php'] = $r[1];
	}
	if (preg_match(',Content-Encoding: gzip$,m', $header)) {
		$res['gzip'] = true;
	}

	// Vérifier si l'entête dit "Hey, c'est fabriqué avec SPIP"
	if (preg_match($regexp = ',Composed-By: (.*)( @ www.spip.net)( ?\+ ?(.*))?$,m', $header, $r)) {
		// essayer de choper local/config.txt si il est la car plus complet si le header semble coupe
		if (substr($header, -1) !== ')') {
			$url_config = suivre_lien($url, 'local/config.txt');
			$config = univers_recuperer_lapage($url_config);
			if ($config and preg_match($regexp, $config['result'], $rc)) {
				$r = $rc;
			}
		}
		$res['spip'] = $r[1]?trim(preg_replace(',^[^0-9]*,', '', $r[1])):trim(preg_replace('(.*)', '', $r[1]));

		if (!$res['spip']) {
			$res['spip'] = '?';
		}
		$res['plugins'] = array();
		if ($r[4]==0) {
			if ($p = array_filter(explode(',', $r[4]))) {
				foreach ($p as $plugin) {
					$plugin = trim($plugin);
					$res['plugins'][preg_replace(',[(].*,', '', $plugin)] = $plugin;
				}
			}
		} else {
			$res['plugins'] = false;
		}
	} else {
		// Vérifier si l'entête dit "Hey, c'est fabriqué avec un autre CMS"
		$res['otherscms'] = '?';
	}

	// Sinon, trouver un autre indice
	// Si "spip" est dans le code HTML, une chance que ce soit un site SPIP
	if (!isset($res['spip'])) {
		if (preg_match(',spip,i', $page)) {
			$res['spip'] = '';
		}
	}

	// Si peut-être, mais pas sûr, essayez d'obtenir la page de connexion
	// Il devrait avoir quelques informations qui parle de "spip"
	if (isset($res['spip']) and (!$res['spip'] or $res['spip']=='?')) {
		// recuperer la page de login
		$login = preg_replace(',spip[.]php.*$,', '', $url) . 'ecrire/';
		if ($login = univers_recuperer_lapage($login)) {
			list(, $login) = $login;
			if (preg_match(',<meta name=[\'"]generator["\'][^>]*>,Uims', $login, $r)
				and $v = extraire_attribut($r[0], 'content')
				and preg_match(',SPIP ([^\[]+),', $v, $r)) {
				$res['spip'] = trim($r[1]);
			}
		}
	}

	// Si nous ne trouvons pas de page de connexion, ou aucune information 
	// essayer d'obtenir le htaccess.txt livré avec SPIP, 
	// il a quelques informations supplémentaires
	if (isset($res['spip']) and (!$res['spip'] or $res['spip']=='?')) {
		// tenter de recup le htaccess.txt qui contient un numero de version
		$ht = preg_replace(',spip[.]php.*$,', '', $url) . 'htaccess.txt';
		if ($ht = univers_recuperer_lapage($ht)) {
			list(, $htpage) = $ht;
			if (preg_match(',SPIP v\s*([0-9.]+),', $htpage, $r)) {
				$res['spip'] = $r[1];
			}
		}

		// Si nous ne trouvons pas de confirmation et qu'il y a 'spip' dans le html 
		// peut-être une ancienne version de spip
		if (!$res['spip']) {
			$res['spip'] = '<1.8?';
		}
	}

	// Si c'est une version de wordpress mais pas sure
	if (isset($res['otherscms']) and (!$res['otherscms'] or $res['otherscms']=='?')) {
		// recuperer la page d'admin de wordpress
		$login = preg_replace(',wp-login[.]php.*$,', '', $url);
		if ($login = univers_recuperer_lapage($login)) {
			list(, $login) = $login;
			if (preg_match(',<meta name=[\'"]generator["\'][^>]*>,Uims', $login, $r)
				and $v = extraire_attribut($r[0], 'content')
				and preg_match(',([^\[]+),', $v, $r)) {
				$res['otherscms'] = trim($r[1]);
			}
		}
	}

	// Si c'est une 404
	if (count($res)==1 and preg_match(',404 ,', $header)) {
		$res['response'] = '404';
	} else {
		$res['response'] = true;
	}

	return $res;

}

/**
 * Prendre un enregistrement pour vérifier dans la base de données
 * et traiter les données recueillies
 *
 * @param array $row
 * @param bool $debug
 */
function univers_analyser_un($row, $debug = false) {
	$id_syndic = $row['id_syndic'];
	$url = $row['url_site'];
	$statut = $row['statut_stats'];

	if ($statut=='oui') {
		// incrementer le retry et la date avant tout pour ne pas tourner en rond
		// sur ce site si il timeout
		sql_updateq('spip_monitor_stats', array('retry'=>$row['retry']+1,'date'=>date('Y-m-d H:i:s'),'status'=>'timeout?'), 'id_syndic=' . intval($id_syndic));
		$id_monitor_stats = sql_getfetsel('id_monitor_stats', 'spip_monitor_stats', 'id_syndic=' . intval($id_syndic));
	} else {
		sql_updateq('spip_syndic', array('statut_stats' => 'oui'), 'id_syndic=' . intval($id_syndic));
		sql_insertq('spip_monitor_stats', array('id_syndic'=>intval($id_syndic),'retry'=>$row['retry']+1,'date'=>date('Y-m-d H:i:s'),'status'=>'timeout?'));
		$id_monitor_stats = sql_getfetsel('id_monitor_stats', 'spip_monitor_stats', 'id_syndic=' . intval($id_syndic));
	}

	$res = univers_analyser($url, $debug);

	$set = array();
	$set['id_syndic'] = $id_syndic;
	if ($res===false) {
		$set['retry'] = ++$row['retry'];
		$set['status'] = 'no-dns';
	} elseif ($res['response'] === false or $res['response'] === '404') {
		$set['ip'] = $res['ip'];
		$set['retry'] = ++$row['retry'];
		$set['status'] = ($res['response']?$res['response']:'dead');
	} elseif ($res['response'] === true) {
		$set['ip'] = $res['ip'];
		$set['server'] = $res['server']?$res['server']:'';
		$set['php'] = $res['php']?$res['php']:'';
		$set['gzip'] = $res['gzip']?'oui':'';
		$set['version'] = $res['version'];
		
		if(function_exists('geoip_informations')) {
			$set['pays'] = geoip_informations($set['ip']);
		}

		if (isset($res['spip'])) {
			
			// c'est un SPIP !
			$set['spip'] = $res['spip'];
			$set['plugins'] = count($res['plugins']);

			// mettre a jour les plugins
			sql_delete('spip_monitor_stats_plugins', 'id_monitor_stats=' . intval($id_monitor_stats));
			if ($res['plugins']) {
				foreach ($res['plugins'] as $p => $v) {
					sql_insertq('spip_monitor_stats_plugins', array('id_monitor_stats'=>$id_monitor_stats,'plugin'=>$p,'version'=>$v));
				}
			}
		}
		if (isset($res['otherscms'])) {

			// c'est un WordPress
			$set['spip'] = $res['otherscms']?$res['otherscms']:'';
			$set['plugins'] = 0; // Get plugins WordPress ???
		}
		$set['status'] = '';
		$set['retry'] = 0;
	} else {
		// ???
		$set['retry'] = ++$row['retry'];
		$set['status'] = $res['response'];
	}

	$set['date'] = date('Y-m-d H:i:s');
	sql_updateq('spip_monitor_stats', $set, 'id_monitor_stats=' . intval($id_monitor_stats));

	if (time() >= _TIME_OUT) {
		return;
	}

}

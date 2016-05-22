<?php
/**
 * MonitorSites
 *
 * @plugin     Monitor
 * @copyright  2014
 * @author     cyp
 * @licence    GNU/GPL
 * @package    SPIP\lib\Monitor\MonitorSites
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/distant');
include_spip('lib/Monitor/univers_analyser');

function recupererLapage($url, $cookie = '', $href = '', $post = false) {
	$ref = $GLOBALS['meta']['adresse_site'];
	// let's say we're coming from google, after all...
	$GLOBALS['meta']['adresse_site'] = 'http://www.google.fr';
	$datas = ''
#    ."Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n"
#    ."Accept-Language: fr,fr-fr;q=0.8,en-us;q=0.5,en;q=0.3\r\n"
#    ."Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7\r\n"
#    ."Keep-Alive: 300\r\n"
#    ."Connection: keep-alive\r\n"
	. 'Cookie: $cookie\r\n'
#    ."If-Modified-Since: Sat, 08 May 2010 20:49:37 GMT\r\n"
#    ."Cache-Control: max-age=0\r\n"
	. '\r\n'
	;

	if ($post==false) {
		$site = recuperer_lapage($url, false, 'GET', 1048576, $datas);
	} else {
		$site = recuperer_page($href, $trans = false, $get_headers = false, $taille_max = null, $datas = 'url='.$url, $boundary = '', $refuser_gz = false, $date_verif = '', $uri_referer = '');
	}

	$GLOBALS['meta']['adresse_site'] = $ref;
	
	if (!$site) {
		return $site;
	}
	if (is_string($site) and !$max_redir) {
		return false;
	}
	list($header, $page) = $site;
	
	return $site;
}

/**
 * Regroupement de curl_init(), curl_exec et curl_close()
 *
 * @param string $href
 * @param boolean $header Retourne l'entête
 * @param boolean $body Retourne le corps
 * @param int $timeout connection timeout en secondes
 * @param boolean $add_agent Ajout d'un user agent
 * @return string cURL resultat
 */
function curl_get($href, $header = false, $body = true, $timeout = 10, $add_agent = true, $status = false, $post = false, $params = '') {

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_HEADER, $header);
	curl_setopt($ch, CURLOPT_NOBODY, (!$body));

	if (ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')) {
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	} else {
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
	}

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_ENCODING, '');
	curl_setopt($ch, CURLOPT_URL, $href);
	if ($add_agent) {
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; spip/; +http://www.spip.net)');
	}

	if (!$result = curl_exec($ch)) {
		// logger dans les logs de SPIP les erreurs de connexion curl
		spip_log(curl_error($ch), 'monitor.' . _LOG_ERREUR);
	}

	// if size page
	if ($status) {
		$result = curl_getinfo($ch, $status);
	}

	curl_close($ch);

	return $result;
}

/**
 * Latence des sites, corresponds au ping dans l'interface SPIP
 *
 * @param string $href
 * @return array resultat
 */
function updateWebsite($href) {

		$starttime = microtime(true);

		if (function_exists('curl_init')) {
			$result = curl_get($href, true);
		} else {
			$result = recupererLapage($href);
		}

		$latency = (microtime(true) - $starttime);

		return array('result' => $result, 'latency' => $latency);
}

/**
 * Verifie le bonne état d'un service
 *
 * @param string $href
 * @return string resultat
 */
function updateService($href) {
		$errno = 0;
		$error = 1;
		// save response time
		$starttime = microtime(true);

		$fp = fsockopen($href, '80', $errno, $error, 10);

		$status = ($fp === false) ? false : true;
		$rtime = (microtime(true) - $starttime);

		fclose($fp);

		return $status;
}

/**
 * Récupére le poids de la page d'un site
 *
 * @param string $href
 * @return array resultat
 */
function sizePage($href) {

		if (function_exists('curl_init')) {
			$poids = curl_get($href, true, true, 10, false, CURLINFO_SIZE_DOWNLOAD);
		} else {
			$poids = recupererLapage($href);
			
			if (preg_match(',Content-Length: (.+)$,m', $poids[0], $r)) {
				$poids = $r[1];
			}
		}

		if (!$poids) {
				$result = false;
		} else {
				$result = true;
		}

		return array('result' => $result, 'poids' => $poids);
}

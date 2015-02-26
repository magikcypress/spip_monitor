<?php
/**
 * MonitorSites
 *
 * @version    1.7.0
 * @copyright  2013
 * @author     Vincent L
 * @licence    GNU/GPL3
 */

include_spip('inc/distant');
include_spip('inc/univers_analyser');

if (!defined("_ECRIRE_INC_VERSION")) return;

function recupererLapage($url,$cookie="") {
	$ref = $GLOBALS['meta']["adresse_site"];
	// let's say we're coming from google, after all...
	$GLOBALS['meta']["adresse_site"] = "http://www.google.fr";
	$datas = ""
#    ."Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n"
#    ."Accept-Language: fr,fr-fr;q=0.8,en-us;q=0.5,en;q=0.3\r\n"
#    ."Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7\r\n"
#    ."Keep-Alive: 300\r\n"
#    ."Connection: keep-alive\r\n"
	 ."Cookie: $cookie\r\n"
#    ."If-Modified-Since: Sat, 08 May 2010 20:49:37 GMT\r\n"
#    ."Cache-Control: max-age=0\r\n"
	 ."\r\n"
	;
	// $site = $url;
	// $max_redir = 10;
	// while ($site AND is_string($site) AND $max_redir--) {
	// 	$url = $site;
	// 	$site = recuperer_lapage($url,false,'GET',1048576,$datas);
	// }
	$site = recuperer_lapage($url,false,'GET',1048576,$datas);
	
	$GLOBALS['meta']["adresse_site"] = $ref;
	if (!$site)
		return $site;
	if (is_string($site) AND !$max_redir)
		return false;
	list($header, $page) = $site;

	// if a cookie set, accept it an retry with it
	// if (preg_match(",Set-Cookie: (.*)(;.*)?$,Uims",$header,$r)) {
	// 	//ne pas relancer si le cookie est déjà présent
	// 	if (strpos($cookie,$r[1])===FALSE) {
	// 		$cookie .= $r[1] . ";";
	// 		spip_log("Cookie : $cookie on repart pour un tour ", "univers_check");
	// 		return univers_recuperer_lapage($url, $cookie);
	// 	}
	// }
	
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
function curl_get($href, $header = false, $body = true, $timeout = 10, $add_agent = true, $status = false) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, $header);
		curl_setopt($ch, CURLOPT_NOBODY, (!$body));
		if (ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off'))
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		else
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_ENCODING, '');
		curl_setopt($ch, CURLOPT_URL, $href);
		if($add_agent) {
			curl_setopt ($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; spip/; +http://www.spip.net)');
		}

		$result = curl_exec($ch);

		// if size page
		if($status) {
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
 
		if (function_exists("curl_init"))
			$result = curl_get($href, true);
		else
			$result = recupererLapage($href);

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

		if (function_exists("curl_init"))
			$poids = curl_get($href, true, true, 10, false, CURLINFO_SIZE_DOWNLOAD);
		else {
			$poids = recupererLapage($href);
			
			if(preg_match(',Content-Length: (.+)$,m', $poids[0], $r))
				$poids = $r[1];
		}

		if(!$poids)
				$result = false;
		else
				$result = true;

		return array('result' => $result, 'poids' => $poids);
}

/**
 * Récupére les données depuis l'api google pageSpeed
 *
 * @param string $href
 * @return array resultat
 */
function getPageSpeedGoogle($href) {
		$url_pagespeed = 'https://www.googleapis.com/pagespeedonline/v1/runPagespeed?url=';
		if (function_exists("curl_init"))
			$result = curl_get($url_pagespeed.$href, false);
		else {
			$result = recupererLapage($url_pagespeed.$href);
			$result = $result[1];
		}

		spip_log($result, 'test.' . _LOG_ERREUR);
		$result = json_decode($result, false);

		if(isset($result) && isset($result->{'responseCode'})) {
			$score = $result->{'score'};
			$pagestats = $result->{'pageStats'};
			$minifycss = $result->{'formattedResults'}->{'ruleResults'}->{'MinifyCss'}->{'ruleImpact'};
			$minifycss_msg = $result->{'formattedResults'}->{'ruleResults'}->{'MinifyCss'}->{'urlBlocks'};
			foreach ($minifycss_msg[0]->{'header'} as $key => $value) {
				if($key == 'format') $minifycss_msg = $value;
			}
			$minifyhtml = $result->{'formattedResults'}->{'ruleResults'}->{'MinifyHTML'}->{'ruleImpact'};
			$minifyhtml_msg = $result->{'formattedResults'}->{'ruleResults'}->{'MinifyHTML'}->{'urlBlocks'};
			foreach ($minifyhtml_msg[0]->{'header'} as $key => $value) {
				if($key == 'format') $minifyhtml_msg = $value;
			}
			$minifyjavascript = $result->{'formattedResults'}->{'ruleResults'}->{'MinifyJavaScript'}->{'ruleImpact'};
			$minifyjavascript_msg = $result->{'formattedResults'}->{'ruleResults'}->{'MinifyJavaScript'}->{'urlBlocks'};
			foreach ($minifyjavascript_msg[0]->{'header'} as $key => $value) {
				if($key == 'format') $minifyjavascript_msg = $value;
			}   
		}
		
		return array('score' => $score, 
					'pagestats' => $pagestats, 
					'minifycss' => $minifycss,
					'minifycss_msg' => $minifycss_msg,
					'minifyhtml' => $minifyhtml,
					'minifyhtml_msg' => $minifyhtml_msg,
					'minifyjavascript' => $minifyjavascript,
					'minifyjavascript_msg' => $minifyjavascript_msg);
}
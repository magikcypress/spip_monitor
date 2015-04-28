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

include_spip('inc/distant');
include_spip('inc/univers_analyser');

if (!defined("_ECRIRE_INC_VERSION")) return;

function recupererLapage($url,$cookie="",$href="",$post=false) {
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
	if($post==false)
		$site = recuperer_lapage($url,false,'GET',1048576,$datas);	
	else
		$site = recuperer_page($href, $trans = false, $get_headers = false,
						$taille_max = null, $datas = 'url='.$url, $boundary = '', $refuser_gz = false,
						$date_verif = '', $uri_referer = '');

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
function curl_get($href, $header = false, $body = true, $timeout = 10, $add_agent = true, $status = false, $post = false, $params = "") {

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_HEADER, $header);
	curl_setopt($ch, CURLOPT_NOBODY, (!$body));

	if (ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off'))
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	else
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_ENCODING, '');
	curl_setopt($ch, CURLOPT_URL, $href);
	if($add_agent) {
		curl_setopt ($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; spip/; +http://www.spip.net)');
	}

	if($post == true) {
		// TODO
		// for yellowlab only, adjust with others services
		$var_post = '{"url":"'. $params . '"}';
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $var_post);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"Content-type: application/json",
			)
		);
	}

	if(!$result = curl_exec($ch)) 
	{ 
		trigger_error(curl_error($ch)); 
	}

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


/**
 * Récupére les données depuis l'api de yellowlab
 *
 * @param string $href
 * @return array resultat
 */
function getYellowLab($href) {
		// form URL
		// return API key
		// POST http://yellowlab.tools/api/runs
		// result
		// GET http://yellowlab.tools/api/runs/<runId>
		if (function_exists("curl_init")) {
			$href = preg_replace('#^http(s)?://#', '', $href);
			$get_api = curl_get("http://yellowlab.tools/api/runs", false, false, 80, false, false, true, $href);
			// Result
			// Moved Temporarily. Redirecting to /api/results/e2gn05k8pzc
			$get_api=explode('to',$get_api);
			$result = curl_get("http://yellowlab.tools" . trim($get_api[1]), false);
		}
		else {
			$get_api = recupererLapage("http://yellowlab.tools/api/runs","",true);
			$result = recupererLapage("http://yellowlab.tools/api/runs/" . $get_api,"",true);
			$result = $result[1];
		}

		if($result == "Too many requests")
			return false;
		if(!$result)
			return false;

		$donnees = array();
		$donnees_rules = array();
		$result = json_decode($result, false);
		$scoreProfiles = $result->{'scoreProfiles'}->{'generic'}->{'categories'};
		$globalScore = $result->{'scoreProfiles'}->{'generic'}->{'globalScore'};
		if($scoreProfiles) {
			foreach ($scoreProfiles as $valueScore) {
				foreach ($valueScore as $cle => $valeur) {
					array_push($donnees, array($cle => $valeur));
					if(is_array($valeur)) {
						foreach ($valeur as $key => $val) {
							$rules = $result->{'rules'}->{'' .$val . ''};
							array_push($donnees, $rules);
						}
					}
				}
			}
		}
		return array('donnees' => $donnees, 'globalscore' => $globalScore);
}
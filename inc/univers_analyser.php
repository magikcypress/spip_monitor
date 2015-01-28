<?php
/*
 * Plugin Univers SPIP
 * (c) 2010 Cedric
 * Distribue sous licence GPL
 *
 */

// User agent used to load the page
@define('_INC_DISTANT_USER_AGENT', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; fr; rv:1.9.1.9) Gecko/20100315 Firefox/3.5.9');
@define('_INC_DISTANT_VERSION_HTTP', "HTTP/1.0");

include_spip('inc/filtres');
include_spip('inc/distant');
include_spip('inc/meta');
include_spip('lib/Monitor/MonitorSites');

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Get the complete page.
 * Should be sub.something.tld/spip.php
 *
 * Some site set a cookie then redirect before everything else
 * so we have to accept it
 *
 * @param string $url
 * @param string $cookie
 *
 */
function univers_recuperer_lapage($url,$cookie="") {

	$site = updateWebsite($url);

	return $site;
}

/**
 * Get address by host.
 * Use nslookup instead of php function
 *
 * @param string $host
 * @param int $timeout
 * @return string
 */
function univers_getaddrbyhost($host, $timeout = 3) {
   $query = `nslookup -timeout=$timeout -retry=1 $host`;
   if(preg_match('/\nAddress: (.*)\n/', $query, $matches))
      return trim($matches[1]);
   return $host;
}

/**
 * Analyse a site to check it's made with SPIP and find versions and plugins
 *
 * @param string $url
 * @param bool $debug
 * @return boolean
 */
function univers_analyser($url, $debug=false) {
	$res = array();

	$path = parse_url($url);
	$ip = univers_getaddrbyhost($path['host']);
	if (!$ip OR $ip==$path['host'])
		return false;  // pas d'ip, pas de site, rien a faire

	$res['ip'] = $ip;

	// get the complete page
	$site = univers_recuperer_lapage($url);
	if (!$site) {
		$res['response'] = false;
		return $res;
	}

	list($header, $page) = $site;

	spip_log($header, 'test.' . _LOG_ERREUR);
	spip_log($page, 'test.' . _LOG_ERREUR);

	// get some generic informations (server, php, gzip)
	if (preg_match(',Server: (.*)$,m', $header, $r)) {
		$res['server'] = $r[1];
	}
	if (preg_match(',X-Powered-By: PHP/(.+)$,m', $header, $r)) {
		$res['php'] = $r[1];
	}
	if (preg_match(',Content-Encoding: gzip$,m', $header)) {
		$res['gzip'] = true;
	}

	// check if the header says "Hey, i'm made with SPIP"
	if (preg_match($regexp = ',Composed-By: (.*)( @ www.spip.net)( ?\+ ?(.*))?$,m', $header, $r)) {
		// essayer de choper local/config.txt si il est la car plus complet si le header semble coupe
		if (substr($header,-1)!==")"){
			$url_config = suivre_lien($url,"local/config.txt");
			$config = univers_recuperer_lapage($url_config);
			if ($config AND preg_match($regexp, $config, $rc))
				$r = $rc;
		}
		// $res['spip'] = trim(preg_replace(',^[^0-9]*,','',$r[1]));
		$res['spip'] = $r[1]?trim(preg_replace(',^[^0-9]*,','',$r[1])):trim(preg_replace('(.*)','',$r[1]));

		if (!$res['spip'])
			$res['spip'] = '?';
		$res['plugins'] = array();
		if ($p = array_filter(explode(',', $r[4]))) {
			foreach ($p as $plugin) {
				$plugin = trim($plugin);
				$res['plugins'][preg_replace(',[(].*,','', $plugin)] = $plugin;
			}
		}
	} else {
		// Check if the header says "Hey, i'm made with otherCMS"
		$res['otherscms'] = '?';
	}

	// else, find another clue
	// if 'spip' is in the html, there are some chance that it is a SPIP site
	if (!isset($res['spip']))
		if (preg_match(',spip,i', $page))
			$res['spip'] = '';

	// if maybe but not sure, try to get the login page
	// it should have some information that says "SPIP"
	if (isset($res['spip']) AND (!$res['spip'] OR $res['spip']=='?')){
		// recuperer la page de login
		$login = preg_replace(",spip[.]php.*$,","",$url)."ecrire/";
		if ($login = univers_recuperer_lapage($login)){
			list(, $login) = $login;
			if  (preg_match(',<meta name=[\'"]generator["\'][^>]*>,Uims',$login,$r)
				AND $v = extraire_attribut($r[0], 'content')
				AND preg_match(",SPIP ([^\[]+),",$v,$r))
				$res['spip'] = trim($r[1]);
		}
	}

	// if we did'nt found login page, or there whas no information
	// try to get the htaccess.txt delivered with SPIP,
	// it has some extra informations
	if (isset($res['spip']) AND (!$res['spip'] OR $res['spip']=='?')){
		// tenter de recup le htaccess.txt qui contient un numero de version
		$ht = preg_replace(",spip[.]php.*$,","",$url)."htaccess.txt";
		if ($ht = univers_recuperer_lapage($ht)) {
			list(, $htpage) = $ht;
			if (preg_match(",SPIP v\s*([0-9.]+),",$htpage,$r))
				$res['spip'] = $r[1];
		}

		// if we did'nt found a confirmation and there was only 'spip' in the html
		// maybe it's an old spip site, but whe mark it apart as it is suspect
		if (!$res['spip'])
			$res['spip'] = '<1.8?';
	}

	// if maybe but not sure, try to get the admin wordpress page
	if (isset($res['otherscms']) AND (!$res['otherscms'] OR $res['otherscms']=='?')){
		// recuperer la page d'admin de wordpress
		$login = preg_replace(",wp-login[.]php.*$,","",$url);
		if ($login = univers_recuperer_lapage($login)){
			list(, $login) = $login;
			if  (preg_match(',<meta name=[\'"]generator["\'][^>]*>,Uims',$login,$r)
				AND $v = extraire_attribut($r[0], 'content')
				AND preg_match(",([^\[]+),",$v,$r))
				$res['otherscms'] = trim($r[1]);
		}
	}

	// if it is a 404, that was a bad adress
	if (count($res)==1 AND preg_match(',404 ,', $header)){
		$res['response'] = '404';
	}
	else {
		$res['response'] = true;
	}
	// spip_log($res, 'test.' . _LOG_ERREUR);
	return $res;

}

/**
 * Take one record to check in database, and process it
 *
 * @param array $row
 * @param bool $debug
 */
function univers_analyser_un($row,$debug = false){
	$id = $row['id_syndic'];
	$url = $row['url_site'];
	$statut = $row['statut_stats'];

	if($statut=="oui") {
		// incrementer le retry et la date avant tout pour ne pas tourner en rond
		// sur ce site si il timeout
		sql_updateq('spip_monitor_stats', array('retry'=>$row['retry']+1,'date'=>date('Y-m-d H:i:s'),'status'=>'timeout?'), 'id_monitor_stats=' . intval($id));
		$id_monitor_stats = sql_getfetsel('id_monitor_stats', 'spip_monitor_stats', 'id_syndic=' . intval($id));
	} else {
		sql_updateq('spip_syndic', array('statut_stats' => 'oui'), 'id_syndic=' . intval($id));
		sql_insertq('spip_monitor_stats', array('id_syndic'=>intval($id),'retry'=>$row['retry']+1,'date'=>date('Y-m-d H:i:s'),'status'=>'timeout?'));
		$id_monitor_stats = sql_getfetsel('id_monitor_stats', 'spip_monitor_stats', 'id_syndic=' . intval($id));
	}

	$res = univers_analyser($url, $debug);

	$set = array();
	if ($res===false) {
		$set['retry'] = ++$row['retry'];
		$set['status'] = 'no-dns';
	}
	elseif ($res['response']===false OR $res['response']==='404'){
		$set['ip'] = $res['ip'];
		$set['retry'] = ++$row['retry'];
		$set['status'] = ($res['response']?$res['response']:'dead');
	}
	elseif ($res['response']===true) {
		$set['ip'] = $res['ip'];
		$set['server'] = $res['server']?$res['server']:'';
		$set['php'] = $res['php']?$res['php']:'';
		$set['gzip'] = $res['gzip']?'oui':'';

		if (isset($res['spip'])){
			$set['pays'] = univers_geoip($set['ip']);

			// c'est un SPIP !
			$set['spip'] = $res['spip'];
			$set['plugins'] = count($res['plugins']);

			// mettre a jour les plugins
			sql_delete('spip_monitor_stats_plugins','id_monitor_stats=' . intval($id_monitor_stats));
			if($res['plugins']) {
				foreach($res['plugins'] as $p=>$v) {
					sql_insertq('spip_monitor_stats_plugins', array('id_monitor_stats'=>$id_monitor_stats,'plugin'=>$p,'version'=>$v));
				}
			}
		}
		if (isset($res['otherscms'])){
			$set['pays'] = univers_geoip($set['ip']);

			// c'est un WordPress
			$set['spip'] = $res['otherscms']?$res['otherscms']:'';
			$set['plugins'] = 0; // Get plugins WordPress ???
		}
		$set['status'] = '';
		$set['retry'] = 0;
	}
	else {
		// ???
		$set['retry'] = ++$row['retry'];
		$set['status'] = $res['response'];
	}

	$set['date'] = date('Y-m-d H:i:s');
	// spip_log($set, 'test.' . _LOG_ERREUR);
	sql_updateq("spip_monitor_stats", $set, "id_monitor_stats=".intval($id_monitor_stats));

	if (time() >= _TIME_OUT)
        return;

}


/**
 * Find state information from IP adress with GeoIP tool
 *
 * @staticvar string $gi
 * @param string $ip
 * @return string
 */
function univers_geoip($ip=null){
	static $gi = null;
	if (is_null($ip)){
		include_spip("geoip/geoip");
		geoip_close($gi);
		return;
	}
	if (is_null($gi)){
		include_spip("geoip/geoip");
		$gi = geoip_open(find_in_path("geoip/GeoIP.dat"),GEOIP_STANDARD);
	}

	return geoip_country_code_by_addr($gi,$ip);
}

?>

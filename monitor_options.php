<?php
	
/**
 * Options pour Monitor
 *
 * @plugin     Monitor
 * @copyright  2014
 * @author     cyp
 * @licence    GNU/GPL
 * @package    SPIP\Monitor\options
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Cron de mise à jour des sites syndiqués
 *
 * @param int $t Date de dernier passage
 * @return int
 **/
function genie_syndic($t) {
	if ($_SERVER['REMOTE_ADDR'] == $_SERVER['SERVER_ADDR']) {
		include_spip('genie/syndic');
		return executer_une_syndication();
	} else {
		return 0;
	}
}
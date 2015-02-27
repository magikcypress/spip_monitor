<?php
/**
 * Monitor
 *
 * @plugin     Monitor
 * @copyright  2014
 * @author     cyp
 * @licence    GNU/GPL
 * @package    SPIP\action\monitor
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_instituer_monitor_dist() {

    $securiser_action = charger_fonction('securiser_action', 'inc');
    $arg = $securiser_action();

    list($type, $statut) = preg_split('/\W/', $arg);

    if(in_array($type, array('ping','poids')))  {
        $monitor = sql_allfetsel('id_syndic', 'spip_monitor', 'type=' . sql_quote($type));
        $monitores = array();
        foreach ($monitor as $key => $value) {
            $monitores[] = $value['id_syndic'];

        }
        $syndics = sql_allfetsel('id_syndic', 'spip_syndic', 'statut="publie"');
        foreach ($syndics as $key => $value) {
            $id_syndic = $value['id_syndic'];
            if(in_array($id_syndic, $monitores)) {
                sql_updateq('spip_monitor', array('statut'=>$statut), 'id_syndic=' . intval($id_syndic) . ' and type=' . sql_quote($type));
            } else {
                sql_insertq('spip_monitor', array('id_syndic'=>$id_syndic, 'statut'=>$statut, 'type'=>$type, 'date_modif' => date('Y-m-d H:i:s')));
            }

        }        
    }
}

?>

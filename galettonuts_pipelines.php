<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

$p = explode(basename(_DIR_PLUGINS) . '/', str_replace('\\', '/', realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_GALETTONUTS', _DIR_PLUGINS . end($p));
unset($p);

function galettonuts_affiche_milieu($flux)
{
    // On se branche sur la page Auteurs
    if ('auteurs' == $flux['args']['exec'])
    {
        include_spip('inc/galettonuts');
        // Le plugin n'est accessible qu'au(x) super-admin(s)
        if ('0minirezo' === $GLOBALS['auteur_session']['statut'] && $GLOBALS['connect_toutes_rubriques'])
            $flux['data'] .= galettonuts_formulaire_synchro();
    }
    
    return $flux;
}

function galettonuts_taches_generales_cron($taches_generales)
{
    // Récupérer la périodicité de synchronisation dans 
    // la meta galettonuts_config, autrement la définir à 24h.
    $periodicite = 86400;
    
    if (isset($GLOBALS['meta']['galettonuts_synchro']))
    {
        $meta = unserialize($GLOBALS['meta']['galettonuts_synchro']);
        if (array_key_exists('periodicite', $meta))
            $periodicite = (int) $meta['periodicite'];
    }
    
    $taches_generales['galettonuts_cron'] = $periodicite;
    return $taches_generales;
}

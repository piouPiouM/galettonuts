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
    // Par défaut le plugin réalise la synchro toutes les 10 minutes.
    if (!defined('_GALETTONUTS_DELAIS_CRON'))
    {
        if (file_exists(_DIR_TMP . 'galettonuts_cron.php'))
            include _DIR_TMP . 'galettonuts_cron.php';
        else
            define('_GALETTONUTS_DELAIS_CRON', 600);
    }
    
    $taches_generales['galettonuts_cron'] = _GALETTONUTS_DELAIS_CRON;
    return $taches_generales;
}

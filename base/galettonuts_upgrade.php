<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS['galettonuts_version'] = 0.1;

function galettonuts_upgrade()
{
    $version_base      = $GLOBALS['galettonuts_version'];
    $version_installee = (isset($GLOBALS['meta']['galettonuts_version']) ? $GLOBALS['meta']['galettonuts_version'] : '0.0');
    
    // Si la version installée est la dernière en date
    if ($version_base == $version_installee)
        return;
    
    // Sinon s'il s'agit d'une nouvelle installation
    else if (version_compare($version_installee, '0.0', 'eq'))
    {
        include_spip('base/galettonuts_tables');
        include_spip('base/create');
        include_spip('base/abstract_sql');
        creer_base();
        
        ecrire_meta('galettonuts_version', $version_base);
        ecrire_meta('galettonuts_config', serialize(array(
            'adresse_db'    => 'localhost',
            'prefix_db'     => 'galette_',
            'db_ok'         => false,
            'activer_cron'  => true,
            'heures'        => 0,
            'minutes'       => 30
        )));
        ecrire_metas();
        
        echo '<br />',
             debut_boite_info(true),
             '<strong>', _T('galettonuts:installation_succes'),'</strong>',
             '<p>', _T('galettonuts:texte_installation_succes'), '</p>',
             fin_boite_info(true);
        
        return;
    }
    
    $version_comparaison = version_compare($version_base, $version_installee);
    
    // S'il s'agit d'une mise à jour
    if (-1 == $version_comparaison)
    {
        // TODO: Gérer un Upgrade
        return;
    }
    // Ou si on est en situation d'une régression
    else if (1 == $version_comparaison)
    {
        // TODO: Gérer un Downgrade
        return;
    }
}

function galettonuts_vider_tables()
{
    spip_query("DROP TABLE IF EXISTS spip_galettonuts");
    effacer_meta('galettonuts_version');
    effacer_meta('galettonuts_config');
    effacer_meta('galettonuts_synchro');
    ecrire_metas();
}

function galettonuts_install($action)
{
    switch ($action)
    {
        case 'test':
            return (isset($GLOBALS['meta']['galettonuts_version']) AND ($GLOBALS['galettonuts_version'] <= $GLOBALS['meta']['galettonuts_version']));
        break;
        case 'install':
            galettonuts_upgrade();
        break;
        case 'uninstall':
            galettonuts_vider_tables();
        break;
    }
}

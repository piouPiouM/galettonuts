<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

function cron_galettonuts_cron($t)
{
    if (!class_exists('L2_Spip_Plugin_Metas'))
        include_spip('lib/L2/Spip/Plugin/Metas.class');
    
    $config = new L2_Spip_Plugin_Metas('galettonuts_config');
    
    // La synchronisation automatique est désactivée
    if (!$config->lire('activer_cron'))
    {
        spip_log('cron: galettonuts_cron, desactivee.');
        return null;
    }
    
    include_spip('inc/galettonuts_fonctions');
    
    $code    = (int) galettonuts_synchroniser();
    $synchro = new L2_Spip_Plugin_Metas('galettonuts_synchro');
    
    if (-2 == $code || -1 == $code || 0 == $code)
    {
        spip_log('cron: galettonuts_cron, echec.');
        return (0 - $t);
    }
    else if (-10 == $code)
    {
        spip_log('cron: galettonuts_cron, auteurs a jour.');
        return null;
    }
    else
    {
        spip_log('cron: galettonuts_cron, synchronisation de ' . $code . ' auteurs galette dans spip.');
        return 1;
    }
}

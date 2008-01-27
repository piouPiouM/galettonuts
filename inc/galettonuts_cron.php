<?php

function cron_galettonuts_cron($time)
{
    spip_log('### Galettonuts ###');
    $maintenant = date('Y-m-d G:i:s', time());
    // if ($maintenant < $maj)
    // {
    //     return (0 - $time);
    // }
    // 
    // // La synchronisation est complète
    // $synchro->ajouter(array('maj' => $maintenant), true);
    return 1;
}

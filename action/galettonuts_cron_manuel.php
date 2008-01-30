<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

function action_galettonuts_cron_manuel_dist()
{
    $securiser_action = charger_fonction('securiser_action', 'inc');
    $arg = $securiser_action('galettonuts-0.1');
    
    $voir_statut = _request('statut');
    if ($voir_statut) $voir_statut = '&statut=' . $voir_statut;
    
    include_spip('inc/galettonuts_fonctions');
    $code = (int) galettonuts_synchroniser();
    redirige_par_entete(generer_url_ecrire(
        _request('redirect'),
        'galettonuts_synchro_ok=oui&code_retour=' . $code . $voir_statut, true)
    );
}

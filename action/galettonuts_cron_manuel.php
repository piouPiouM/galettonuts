<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

function action_galettonuts_cron_manuel_dist()
{
    $securiser_action = charger_fonction('securiser_action', 'inc');
    $arg = $securiser_action('galettonuts-0.1');
    
    include_spip('inc/galettonuts_fonctions');
    $erreur = '';
    $statut = (int) galettonuts_synchroniser();
    
    switch ($statut)
    {
        // Une erreur inconnue est survenue.
        case 0:
            $erreur = "Une erreur inconnue est survenue.";
            spip_log('[Galettonuts: cron manuel] Une erreur inconnue est survenue.');
            break;
        
        // Des erreurs sont survenues lors de la connexion à la BDD.
        case -2:
            $erreur = "Des erreurs sont survenues lors de la connexion à la BDD.";
            spip_log('[Galettonuts: cron manuel] Des erreurs sont survenues lors de la connexion a la BDD.');
            break;
        
        // La synchronisation a échouée.
        case -1:
            $erreur = "La synchronisation a échouée.";
            spip_log('[Galettonuts: cron manuel] La synchronisation a echouee.');
            break;
        
        // La synchronisation est inutile.
        case -10:
            $erreur = "La synchronisation est inutile.";
            spip_log('[Galettonuts: cron manuel] La synchronisation est inutile.');
            break;
        
        // La synchronisation s'est déroulée correctement
        default:
            spip_log('[Galettonuts: cron manuel] La synchronisation s\'est deroulee correctement.');
            break;
    }
    
    if ('' != $erreur)
        $erreur = '&erreur=' . urlencode($erreur);
    
    redirige_par_entete(generer_url_ecrire('auteurs', 'galettonuts_synchro_ok=oui' . $erreur, true));
}

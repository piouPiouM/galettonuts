<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

function galettonuts_formulaire_synchro()
{
    global $spip_lang_left, $spip_lang_right;
    include_spip('inc/config');
    
    // Détermine si on dépli le cadre
    if ('oui' == _request('galettonuts_synchro_ok'))
        $visibilite = 'visible';
    else
        $visibilite = 'invisible';
    
    // Traiter la demande de synchronisation manuelle
    if (_request('_galettonuts_ok') && 'visible' == $visibilite)
    {
        include_spip('inc/galettonuts_fonctions');
        $statut = (int) galettonuts_synchroniser();
        
        switch ($statut)
        {
            // Une erreur inconnue est survenue.
            case 0:
                $erreurs = "Une erreur inconnue est survenue.";
                break;
            
            // Des erreurs sont survenues lors de la connexion à la BDD.
            case -2:
                $erreurs = "Des erreurs sont survenues lors de la connexion à la BDD.";
                break;
            
            // La synchronisation a échouée.
            case -1:
                $erreurs = "La synchronisation a échouée.";
                break;
            
            // La synchronisation est inutile.
            case -10:
                $erreurs = "La synchronisation est inutile.";
                break;
            
            // La synchronisation s'est déroulée correctement,
            // 
            default:
                redirige_par_entete(generer_url_ecrire('auteurs', 'galettonuts_synchro_ok=oui'));
        }
        
    }
    
    $return  = debut_cadre_couleur('synchro-24.gif', true, '', call_user_func('bouton_block_' . $visibilite, 'galettonuts_synchro') . _T('galettonuts:titre_formulaire_synchro'));
    $return .= call_user_func('debut_block_' . $visibilite, 'galettonuts_synchro');
    
    // Le plugin n'a pas encore été configuré
    if (!isset($GLOBALS['meta']['galettonuts_config']) || 'a:0:{}' === (string) $GLOBALS['meta']['galettonuts_config'])
    {
        $return .= '<p class="verdana2"><strong>';
        $return .= _T('galettonuts:configuration_manquante');
        $return .= '</strong><br />';
        $return .= _T('galettonuts:configuration_lien', array('url' => generer_url_ecrire('admin_galettonuts')));
        $return .= '</p>';
        return $return;
    }
    
    // Dernière mise à jour
    if (!class_exists('L2_Spip_Plugin_Metas'))
        include_spip('lib/L2/Spip/Plugin/Metas.class');
    $synchro = new L2_Spip_Plugin_Metas('galettonuts_synchro');
    
    if ('' != $maj = $synchro->lire('maj'))
    {
        $return .= '<div style="font-size:x-small;text-align:' . $spip_lang_right . '" class="verdana2">';
        $return .= _T('galettonuts:derniere_maj');
        $return .= $maj;
        $return .= '</div>';
    }
    
    // Affichage des erreurs
    if ($erreurs)
    {
        $return .= '<p class="verdana2">';
        $return .= print_r($erreurs, 1);
        $return .= '</p>';
    }
    
    $return .= '<p class="verdana2">' . _T('galettonuts:texte_synchro_manuelle') . '</p>';
    $return .= generer_url_post_ecrire('auteurs', 'galettonuts_synchro_ok=oui');
    
    // Bouton de validation
    $return .= '<div style="text-align:right;padding:0 2px;margin-top:.5em" id="buttons">';
    $return .= '<input type="submit" name="_galettonuts_ok" value="' . _T('bouton_valider') . '" class="fondo" style="cursor:pointer"/></div>';
    $return .= '</form>';
    $return .= fin_block();
    $return .= fin_cadre_couleur(true);
    
    return $return;
}

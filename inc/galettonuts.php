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
    if ($erreur = _request('erreur'))
    {
        $return .= '<p class="verdana2">';
        $return .= urldecode($erreur);
        $return .= '</p>';
    }
    
    $action = generer_action_auteur('galettonuts_cron_manuel', 'galettonuts-0.1&galettonuts_synchro_ok=oui', 'auteurs');
    
    $return .= '<p class="verdana2">' . _T('galettonuts:texte_synchro_manuelle') . '</p>';
    $return .= '<form action="' . $action . '" method="post>"';
    $return .= form_hidden($action);
    
    // Bouton de validation
    $return .= '<div style="text-align:right;padding:0 2px;margin-top:.5em" id="buttons">';
    $return .= '<input type="submit" name="_galettonuts_ok" value="' . _T('bouton_valider') . '" class="fondo" style="cursor:pointer"/></div>';
    $return .= '</form>';
    $return .= fin_block();
    $return .= fin_cadre_couleur(true);
    
    return $return;
}

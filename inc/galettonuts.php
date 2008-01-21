<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

function galettonuts_formulaire_synchro()
{
    global $spip_lang_left, $spip_lang_right;
    include_spip('inc/config');
    
    // Lecture des configurations
    if (!class_exists('L2_Spip_Plugin_Metas'))
        include_spip('lib/L2/Spip/Plugin/Metas.class');
    $config  = new L2_Spip_Plugin_Metas('galettonuts_config');
    $synchro = new L2_Spip_Plugin_Metas('galettonuts_synchro');
    
    // Détermine si on dépli le cadre
    if ('oui' == _request('galettonuts_synchro_ok'))
        $visibilite = 'visible';
    else
        $visibilite = 'invisible';
    
    // Bouton radio coché ou pas
    $lancer_synchro = _request('lancer_synchro');
    if (!$lancer_synchro)
        $lancer_synchro = 'non';
    
    // Traiter la demande de synchronisation manuelle
    if (_request('_galettonuts_ok') && 'visible' == $visibilite)
    {
        $erreurs = array();
        
        $link = @mysql_connect($config->lire('adresse_db'), $config->lire('login_db'), $config->lire('pass_db'));
        
        if (!$link || 0 !== @mysql_errno($link))
            $erreurs[] = _T('galettonuts:avis_connexion_echec_1');
        else if (!@mysql_select_db($config->lire('choix_db'), $link))
            $erreurs[] = _T('galettonuts:avis_connexion_echec_2');
        else
        {
            // Auteur SPIP :
            // id_auteur, nom, bio, email, nom_site, url_site, login, pass, low_sec, statut, maj, pgp, htpass, en_ligne,
            // imessage, messagerie, alea_actuel, alea_futur, prefs, cookie_oubli, source, lang, idx, url_propre, extra
            // -----
            // Membre Galette :
            // id_adh, id_statut, nom_adh, prenom_adh, pseudo_adh, titre_adh, ddn_adh,
            // adresse_adh, adresse2_adh, cp_adh, ville_adh, pays_adh,
            // tel_adh, gsm_adh, email_adh, url_adh, icq_adh, msn_adh, jabber_adh,
            // info_adh, info_public_adh, prof_adh,
            // login_adh, mdp_adh, date_crea_adh, activite_adh, bool_admin_adh, bool_exempt_adh, bool_display_info, date_echeance
            // -----
            // SHOW TABLE STATUS LIKE 'galette_adherents';
            
            $req = "SELECT `nom_adh` AS `nom`, `prenom_adh` AS `prenom`,  `date_echeance` FROM `"
                 . $config->lire('prefix_db')
                 . "adherents` WHERE 1;";
            $resultat = mysql_query($req ,$link);
            
            while ($adh = mysql_fetch_assoc($resultat))
            {
                
            }
            mysql_free_result($resultat);
        }
        
        if (false !== $link)
            mysql_close($link);
    }
    
    $return  = debut_cadre_couleur('synchro-24.gif', true, '', call_user_func('bouton_block_' . $visibilite, 'galettonuts_synchro') . _T('galettonuts:titre_formulaire_synchro'));
    $return .= call_user_func('debut_block_' . $visibilite, 'galettonuts_synchro');
    
    // Le plugin n'a pas encore été configuré
    if (is_null($config->lire()))
    {
        $return .= '<p class="verdana2"><strong>';
        $return .= _T('galettonuts:configuration_manquante');
        $return .= '</strong><br />';
        $return .= _T('galettonuts:configuration_lien', array('url' => generer_url_ecrire('admin_galettonuts')));
        $return .= '</p>';
        return $return;
    }
    
    // Dernière mise à jour
    if ('' != $maj = $synchro->lire('maj'))
    {
        $return .= '<div style="font-size:x-small;text-align:' . $spip_lang_right . '" class="verdana2">';
        $return .= _T('galettonuts:derniere_maj');
        $return .= $maj;
        $return .= '</div>';
    }
    
    // Choix de l'action
    $return .= '<p class="verdana2">' . _T('galettonuts:texte_synchro_manuelle') . '</p>';
    $return .= generer_url_post_ecrire('auteurs', 'galettonuts_synchro_ok=oui');
    $return .= '<p>';
    $return .= afficher_choix('lancer_synchro', $lancer_synchro,
                              array('oui' => _T('galettonuts:entree_lancer_synchro'), 'non' => _T('galettonuts:ne_rien_faire')));
    $return .= '</p>';
    
    // Bouton de validation
    $return .= '<div style="text-align:right;padding:0 2px;margin-top:.5em" id="buttons">';
    $return .= '<input type="submit" name="_galettonuts_ok" value="' . _T('bouton_valider') . '" class="fondo" style="cursor:pointer"/></div>';
    $return .= '</form>';
    $return .= fin_block();
    $return .= fin_cadre_couleur(true);
    
    return $return;
}


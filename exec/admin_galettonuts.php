<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

function exec_admin_galettonuts()
{
    // Seuls les super-admins sont authorisés réaliser des synchros,
    // et par conséquent de configurer le plugin
    if (!('0minirezo' === $GLOBALS['auteur_session']['statut'] && $GLOBALS['connect_toutes_rubriques']))
    {
        echo minipres(_T('avis_non_acces_page'));
        exit;
    }

    $erreurs     = array();
    $icone_base  = _DIR_PLUGIN_GALETTONUTS . 'img_pack/galettonuts-sql_status-';
    $icone_src   = 'config-168.png';
    $icone_title = _T('galettonuts:icone_db_config');

    // Lecture de la configuration
    if (!class_exists('L2_Spip_Plugin_Metas'))
        include_spip('lib/L2/Spip/Plugin/Metas.class');
    $config   = new L2_Spip_Plugin_Metas('galettonuts_config');
    $contexte = $config->lire();
    
    $activer_cron = (array_key_exists('activer_cron', $contexte)) ? $contexte['activer_cron'] : true;

// {{{ Traitement des données reçues

    if (_request('_galettonuts_ok'))
    {
        $champs = array(
            'adresse_db'=> _request('adresse_db'),
            'login_db'  => _request('login_db'),
            'pass_db'   => _request('pass_db'),
            'prefix_db' => _request('prefix_db'),
            'choix_db'  => _request('choix_db')
        );
        
        // Des champs sont-ils vides ?
        $champs = array_map('trim', $champs);
        if (false === (!in_array(null, $champs) || !in_array('', $champs)))
            $erreurs[] = _T('galettonuts:texte_erreur_1');
        
        // Activer la synchronisation automatique ?
        if ('oui' == _request('activer_cron'))
            $activer_cron = true;
        else
            $activer_cron = false;
        
        if ($activer_cron)
        {
            $champs['heures']  = intval(_request('heures'));
            $champs['minutes'] = intval(_request('minutes'));
            
            $synchro = new L2_Spip_Plugin_Metas('galettonuts_synchro');
            $frequence = 3600 * $champs['heures'] + 60 * $champs['minutes'];
            
            spip_log('#### frequence :' . var_export($frequence, true));
            
            // On s'assure de bien supprimer le fichier de vérouillage
            // pour forcer la resynchronisation tenant compte de la nouvelle
            // configuration.
            if ($frequence !== $synchro->lire('frequence'))
            {
                spip_log('#### frequence a mettre a jour');
                @unlink(_DIR_TMP . 'galettonuts_cron.lock');
                $synchro->ajouter(array('frequence' => $frequence), true);
            }
        }
        $contexte['activer_cron'] = $activer_cron;
        
        // Prise en compte dans le contexte
        $contexte = array_merge($contexte, $champs);
        unset($champs);

        // Test de connexion à la BDD Galette
        if (!count($erreurs))
        {
            $link = galettonuts_galette_db($contexte['adresse_db'], $contexte['login_db'], $contexte['pass_db']);
            
            if (-1 === $link)
            {
                $erreurs[]   = _T('galettonuts:avis_connexion_echec_1');
                $icone_src   = 'error-168.png';
                $icone_title = _T('galettonuts:icone_db_erreur');
            }
            else if (-2 === galettonuts_galette_db($contexte['choix_db'], $link))
            {
                $erreurs[] = _T('galettonuts:avis_connexion_echec_2');
                $icone_src   = 'error-168.png';
                $icone_title = _T('galettonuts:icone_db_erreur');
            }
            else
            {
                $icone_src   = 'ok-168.png';
                $icone_title = _T('galettonuts:icone_db_ok');
            }
            
            if (0 < $link)
                mysql_close($link);
            
            unset($link);
        }
        
        // Mémorisation de la configuration à la base de données Galette
        if (!count($erreurs))
            $config->ajouter($contexte, true);
    }

// }}}
// {{{ Aucune, action : test de la connexion si une configuration est présente

    else if (!empty($contexte['adresse_db']) && !empty($contexte['login_db']) && !empty($contexte['pass_db']))
    {
        $link = galettonuts_galette_db($contexte['adresse_db'], $contexte['login_db'], $contexte['pass_db']);
        if (0 > $link)
        {
            $icone_src   = 'error-168.png';
            $icone_title = _T('galettonuts:icone_db_erreur');
        }
        else
        {
            $icone_src   = 'ok-168.png';
            $icone_title = _T('galettonuts:icone_db_ok');
            mysql_close($link);
            unset($link);
        }
    }

// }}}
// {{{ Affichage

    // Haut de page
    $commencer_page  = charger_fonction('commencer_page', 'inc');
    echo $commencer_page(_T('galettonuts:titre_page_admin'), '', 'galettonuts'), '<br/><br/><br/>';
    gros_titre(_T('galettonuts:titre_admin'));

    // Boîte d'informations
    debut_gauche();
    debut_boite_info();
    echo _T('galettonuts:texte_info_admin');
    fin_boite_info();

    // Message(s) d'erreur(s)
    debut_droite();
    if ($c = count($erreurs))
    {
        if (1 == $c)
        {
            $erreur_titre = _T('galettonuts:texte_erreur');
            $erreur_texte = (string) $erreurs[0];
        }
        else
        {
            $erreur_titre = _T('galettonuts:texte_erreurs');
            $erreur_texte = '<ul>';
            for ($i=0; $c < $i; ++$i)
                $erreur_texte .= '<li>' . $erreurs[$i] . '</li>';
            $erreur_texte .= '</ul>';
        }
        
        echo '<div style="background-color:#fee;color:red;border:1px solid red;padding:.5em;margin-bottom:25px" class="verdana2"><strong>',
             $erreur_titre,
             '</strong>&nbsp;:<br />',
             $erreur_texte,
             '</div>';
    }
    echo generer_url_post_ecrire('admin_galettonuts');

    // Accès à la BDD
    debut_cadre_trait_couleur('base-24.gif', false, '', _T('galettonuts:info_bdd'));
    echo '<div style="float:right;width:175px" class="verdana2">',
            _T('galettonuts:texte_info_bdd'),
            '<div>',
                '<div style="position:absolute;bottom:35px;width:168px;height:168px">',
                    '<img src="', $icone_base, $icone_src, '" width="168" height="168" alt="" title="', $icone_title, '" />',
                '</div>',
            '</div>',
         '</div>';
    echo '<div style="width:298px">';
    debut_cadre_couleur();
    echo '<p><label for="adresse_db" style="font-weight:bold;cursor:pointer">',
         _T('galettonuts:entree_db_adresse'),
         '</label><br/>',
         '<input type="text" name="adresse_db" value="',
         $contexte['adresse_db'],
         '" id="adresse_db" class="fondl" style="width:278px" tabindex="504"/>',
         '</p>';
    echo '<p><label for="login_db" style="font-weight:bold;cursor:pointer">',
         _T('galettonuts:entree_db_login'),
         '</label><br/>',
         '<input type="text" name="login_db" value="', $contexte['login_db'], '" id="login_db" class="fondl" style="width:278px" tabindex="508"/>',
         '</p>';
    echo '<p><label for="pass_db" style="font-weight:bold;cursor:pointer">',
         _T('galettonuts:entree_db_mdp'),
         '</label><br/>',
         '<input type="password" name="pass_db" value="', $contexte['pass_db'], '" id="pass_db" class="fondl" style="width:278px" tabindex="512"/>',
         '</p>';
    echo '<p><label for="prefix_db" style="font-weight:bold;cursor:pointer">',
         _T('galettonuts:entree_db_prefix'),
         '</label><br/>',
         '<input type="text" name="prefix_db" value="',
         $contexte['prefix_db'],
         '" id="prefix_db" class="fondl" style="width:278px" tabindex="516"/>',
         '</p>';
    echo '<p><label for="choix_db" style="font-weight:bold;cursor:pointer">',
         _T('galettonuts:entree_db_choix'),
         '</label><br/>',
         '<input type="text" name="choix_db" value="', $contexte['choix_db'], '" id="choix_db" class="fondl" style="width:278px" tabindex="520"/>',
         '</p>';
    fin_cadre_couleur();
    echo '</div>';
    echo '<div style="text-align:right;padding:0 2px;margin-top:.5em" id="buttons">',
         '<input type="submit" name="_galettonuts_ok" value="', _T('bouton_valider'), '" class="fondo" style="cursor:pointer" tabindex="560"/></div>';
    fin_cadre_trait_couleur();

    // Synchronisation automatique
    echo '<br />';
    debut_cadre_relief('synchro-24.gif', false, '', _T('galettonuts:info_cron'));
    echo '<p class="verdana2">', _T('galettonuts:texte_info_cron'), '</p>';
    echo '<p class="verdana2">',
            '<label', ($activer_cron) ? ' style="font-weight:bold"' : '', '>',
                '<input type="radio" name="activer_cron" value="oui" id="activer_cron_oui" tabindex="602" ',
                ($activer_cron) ? ' checked="checked" ' : '',
                'onclick="changeVisible(this.checked, \'config-cron\', \'block\', \'none\');"',
                '/>',
                _T('galettonuts:entree_cron_utiliser'),
            '</label><br />',
            '<label', (!$activer_cron) ? ' style="font-weight:bold"' : '', '>',
                '<input type="radio" name="activer_cron" value="non" id="activer_cron_non" tabindex="604" ',
                (!$activer_cron) ? ' checked="checked" ' : '',
                'onclick="changeVisible(this.checked, \'config-cron\', \'none\', \'block\');"',
                '/>',
                _T('galettonuts:entree_cron_utiliser_non'),
             '</label>',
         '</p>';
    echo '<div id="config-cron"', (!$activer_cron) ? ' style="display:none"' : '', '><hr />';
    echo '<p class="verdana2">', _T('galettonuts:frequence'), '</p>';
    echo '<p class="verdana2" style="text-align:center">',
            '<input type="text" name="heures" value="', $contexte['heures'], '" id="cron_heures" size="2" maxlength="2" tabindex="606" class="fondl" style="text-align:right"/>',
            '<label for="cron_heures" style="font-weight:bold;cursor:pointer">', _T('galettonuts:heures'), '</label>',
            '<input type="text" name="minutes" value="', $contexte['minutes'], '" id="cron_minutes" size="2" maxlength="2" tabindex="606" class="fondl" style="text-align:right"/>',
            '<label for="cron_minutes" style="font-weight:bold;cursor:pointer">', _T('galettonuts:minutes'), '</label>',
         '</p>';
    echo '</div>';
    echo '<div style="text-align:right;padding:0 2px;margin-top:.5em" id="buttons">',
         '<input type="submit" name="_galettonuts_ok" value="', _T('bouton_valider'), '" class="fondo" style="cursor:pointer" tabindex="660"/></div>';
    fin_cadre_relief();

    // // Liaison inter-plugins
    // if (defined('_DIR_PLUGIN_ACCESRESTREINT'))
    // {
    //     echo '<br />';
    //     debut_cadre_relief(_DIR_PLUGIN_ACCESRESTREINT . 'img_pack/zones-acces-24.gif', false, '', _T('galettonuts:info_liaison_plugins'));
    //     echo '<p class="verdana2">', _T('galettonuts:texte_info_liaison_plugins'), '</p>';
    //     
    //     echo '<div style="text-align:right;padding:0 2px;margin-top:.5em" id="buttons">',
    //          '<input type="submit" name="_galettonuts_ok" value="', _T('bouton_valider'), '" class="fondo" style="cursor:pointer" tabindex="760"/></div>';
    //     fin_cadre_relief();
    // }
    
    echo '</form>';

    // Fin de page
    echo fin_gauche() . fin_page();

// }}}

}

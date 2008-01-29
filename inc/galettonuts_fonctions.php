<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Synchronise les utilisateurs Galette vers Spip.
 * 
 * @return int Le nombre de fiches mis à jour ;
 *             0 lorsqu'une erreur inconnue est survenue ;
 *            -1 si la synchronisation a échouée ;
 *            -2 lorsque des erreurs sont survenues lors de la connexion à la BDD ;
 *           -10 si la synchronisation est inutile.
 **/
function galettonuts_synchroniser()
{

    // Lecture de la configuration
    if (!class_exists('L2_Spip_Plugin_Metas'))
        include_spip('lib/L2/Spip/Plugin/Metas.class');
    $config  = new L2_Spip_Plugin_Metas('galettonuts_config');
    
    // Connexion à la base Galette
    $link = galettonuts_galette_db(
        $config->lire('adresse_db'),
        $config->lire('login_db'),
        $config->lire('pass_db'),
        $config->lire('choix_db')
    );
    
    if (!$link)
    {
        spip_log('cron: Galettonuts, echec de connexion a la bdd de galette');
        return -2;
    }
    
    $synchro = new L2_Spip_Plugin_Metas('galettonuts_synchro');
    
    // La synchronisation est inutile.
    if (!is_null($synchro->lire('maj')) && galettonuts_a_jour($synchro->lire('maj'), $config->lire('prefix_db'), $link))
    {
        return -10;
    }
    // Première synchronisation ou il y a eu une modification de la table des utilisateurs
    // galette depuis la dernière synchronisation.
    else
    {
        // Compteur d'utilisateurs traités
        $compteur = 0;
        $maintenant = time();
        
        // Récupération des adhérents Galette
        $req = "SELECT `id_adh` AS `id`, `nom_adh` AS `nom`, `prenom_adh` AS `prenom`, `activite_adh` AS `actif`, `login_adh` AS `login`, `mdp_adh` AS `pass`, `email_adh` AS `email` FROM `" . $config->lire('prefix_db') . "adherents` WHERE 1;";
        $res = mysql_query($req ,$link);
        
        // Pour chaque adhérent de galette
        while ($adh = mysql_fetch_assoc($res))
        {
            include_spip('inc/acces');
            include_spip('inc/charsets');

            // Formatage des informations de l'auteur à destination de Spip
            $statut = (1 == $adh['actif']) ? '6forum' : '5poubelle';
            $login  = charset2unicode($adh['login'], 'iso-8859-15');
            $email  = charset2unicode($adh['email'], 'iso-8859-15');
            $nom    = charset2unicode(ucfirst($adh['prenom']) . ' ' . ucfirst($adh['nom']), 'iso-8859-15');
            $pass   = charset2unicode($adh['pass'], 'iso-8859-15');
            $mdpass = md5($pass);
            $htpass = generer_htpass($pass);

            // Récupération de l'identifiant de l'auteur Spip, s'il existe
            $res2 = spip_query("SELECT `id_auteur` FROM `spip_galettonuts` WHERE `id_adh` = '{$adh['id']}';");
            if (spip_mysql_count($res2))
                $id_auteur = (int) mysql_result($res2, 0);
            else
                $id_auteur = null;

            // Mise à jour de l'auteur Spip
            if ($id_auteur)
            {
                $req  = "UPDATE `spip_auteurs` SET "
                      . "`nom` = "   . _q($nom)
                      . ", `email` = " . _q($email)
                      . ", `login` = " . _q($login)
                      . ", `pass` = "  . _q($mdpass)
                      . ", `htpass` = ". _q($htpass)
                      . ", `statut` = ". _q($statut)
                      . ", `maj` = NOW()"
                      . " WHERE `id_auteur` = " . _q($id_auteur);
                spip_query($req);
            }

            // Création de l'auteur Spip
            else
            {
                $req = "INSERT INTO `spip_auteurs` (`nom`, `email`, `login`, `pass`, `htpass`, `alea_futur`, `statut`) "
                     . "VALUES ("
                     . _q($nom)     . ', '
                     . _q($email)   . ', '
                     . _q($login)   . ', '
                     . _q($mdpass)  . ', '
                     . _q($htpass)  . ', '
                     . "FLOOR(32000*RAND()), "
                     . _q($statut)
                     . ");";
                spip_query($req);
                unset($req);
                
                // Puisque la colonne id_auteur de la table spip_auteurs est
                // de type BIGINT, on ne peut utiliser mysql_insert_id() de PHP.
                $id_auteur = mysql_result(spip_query("SELECT LAST_INSERT_ID();"), 0);

                $req = "INSERT INTO `spip_galettonuts` (`id_auteur`, `id_adh`) VALUES (" . _q($id_auteur) . ', ' . _q($adh['id']) . ');';
                spip_query($req);
            }
            spip_free_result($res2);

            // Hop, un utilisateur de synchronisé en plus
            ++$compteur;
            
        } // while
        
        // La synchronisation est complète, on le saugarde
        $synchro->ajouter(array('maj' => $maintenant), true);
        
        return $compteur;
    }
    
}

/**
 * Tester si la synchronisation est à jour.
 * 
 * @param  string    $derniere_maj Date de la dernière mise à jour connue, au format Y-m-d G:i:s.
 * @param  string    $prefix_db    Préfixe des tables Galette.
 * @param  ressource $link         Identifiant de connexion SQL.
 * @return bool
 **/
function galettonuts_a_jour($derniere_maj, $prefix_db, $link)
{
    $res = mysql_query("SHOW TABLE STATUS LIKE '" . $prefix_db . "adherents';", $link);
    $maj = mysql_result($res, 0, 'Update_time');
    return (bool) ($derniere_maj >= MySQLtoTimestamp($maj));
}

/**
 * Gestionnaire d'accès à MySQL.
 * 
 * @return mixed Retourne l'identifiant de connexion MySQL en cas de succès, 
 *               -1 en cas d'échec de connexion, 
 *               -2 en cas d'échec de sélection de la base.
 **/
function galettonuts_galette_db()
{
    $link       = null;
    $adresse_db = null;
    $login_db   = null;
    $pass_db    = null;
    $choix_db   = null;
    
    switch (func_num_args())
    {
        // base de données, identifiant de la connexion SQL
        case 2:
            $choix_db = func_get_arg(0);
            $link     = func_get_arg(1);
            break;
        
        // serveur, identifiant, mot de passe
        case 3:
            $adresse_db = func_get_arg(0);
            $login_db   = func_get_arg(1);
            $pass_db    = func_get_arg(2);
            break;
        
        // serveur, identifiant, mot de passe, base de données
        case 4:
            $adresse_db = func_get_arg(0);
            $login_db   = func_get_arg(1);
            $pass_db    = func_get_arg(2);
            $choix_db   = func_get_arg(3);
            break;
        
        default:
            trigger_error('Nombre d\'arguments incorrect pour la fonction <strong>galettonuts_galette_db()</strong>.' , E_USER_ERROR);
            return 0;
            break;
    }
    
    // Aucun identifiant d'une précédente connexion SQL, il nous
    // faut en créer une.
    if (is_null($link))
    {
        $link = mysql_connect($adresse_db, $login_db, $pass_db, true);
        
        if (!$link)
        {
            return -1;
        }
        else if (0 != mysql_errno($link))
        {
            mysql_close($link);
            return -1;
        }
    }
    
    if (!is_null($choix_db))
    {
        if (!mysql_select_db($choix_db, $link))
            return -2;
    }
    
    return $link;
}


/**
 * Converti une date MySQL en Timestamp Unix.
 * 
 * @param  string $mysqlDate Date MySQL à convertir (format Y-m-d G:i:s).
 * @return int
 * @author Tiago Valdo
 * @see http://www.php.net/manual/fr/function.mktime.php#80470
 **/
function MySQLtoTimestamp($mysqlDate)
{
    if (strlen($mysqlDate) > 10)
    {
        list($year, $month, $day_time) = explode('-', $mysqlDate);
        list($day, $time) = explode(" ", $day_time);
        list($hour, $minute, $second) = explode(":", $time);
        $ts = mktime($hour, $minute, $second, $month, $day, $year);
    }
    else
    {
        list($year, $month, $day) = explode('-', $mysqlDate);
        $ts = mktime(0, 0, 0, $month, $day, $year);
    }
    return $ts;
}

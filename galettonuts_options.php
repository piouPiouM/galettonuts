<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Gestionnaire d'accès à MySQL.
 * 
 * @return mixed Retourne l'identifiant de connexion MySQL en cas de succès, 
 *               -1 en cas d'échec de connexion, 
 *               -2 en cas d'échec de sélection de la base.
 **/
function galettonuts_galette_db()
{
    $link     = null;
    $reset    = false;
    $num_args = func_num_args();
    
    switch ($num_args)
    {
        case 2:
            $choix_db = func_get_arg(0);
            $link     = func_get_arg(1);
            break;
        
        case 3:
            $adresse_db = func_get_arg(0);
            $login_db   = func_get_arg(1);
            $pass_db    = func_get_arg(2);
            break;
        
        case 4:
            $adresse_db = func_get_arg(0);
            $login_db   = func_get_arg(1);
            $pass_db    = func_get_arg(2);
            $choix_db   = func_get_arg(3);
            break;
        
        default:
            trigger_error('Nombre d\'arguments incorrect pour la fonction <strong>galettonuts_galette_db()</strong>.' ,E_USER_ERROR);
            return 0;
            break;
    }
    
    if (is_null($link))
    {
        $link  = @mysql_connect($adresse_db, $login_db, $pass_db, true);
        
        if (!$link)
        {
            $link = null;
            return -1;
        }
        else if (0 !== @mysql_errno($link))
        {
            mysql_close($link);
            $link = null;
            return -1;
        }
    }
    
    if (isset($choix_db) && !@mysql_select_db($choix_db, $link))
    {
        return -2;
    }
    
    return $link;
}

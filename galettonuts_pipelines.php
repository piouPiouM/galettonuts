<?php
/**
 * This file is part of Galettonuts.
 * 
 * Galettonuts is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 * 
 * Galettonuts is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Galettonuts. If not, see <http://www.gnu.org/licenses/gpl2.html>.
 */

/**
 * Déclaration des pipelines.
 *
 * PHP versions 4 and 5
 *
 * @package   Galettonuts
 * @author    Mehdi Kabab <pioupioum@tuxfamily.org>
 * @copyright Copyright (C) 2008 Mehdi Kabab
 * @license   http://www.gnu.org/licenses/gpl2.html  GPL Licence 2.0
 * @version   0.1
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

$p = explode(basename(_DIR_PLUGINS) . '/', str_replace('\\', '/', realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_GALETTONUTS', _DIR_PLUGINS . end($p));
unset($p);

function galettonuts_affiche_milieu($flux)
{
    // On se branche sur la page Auteurs
    if ('auteurs' == $flux['args']['exec'])
    {
        include_spip('inc/galettonuts');
        // Le plugin n'est accessible qu'au(x) super-admin(s)
        if ('0minirezo' === $GLOBALS['auteur_session']['statut'] && $GLOBALS['connect_toutes_rubriques'])
            $flux['data'] .= galettonuts_formulaire_synchro();
    }
    
    return $flux;
}

function galettonuts_taches_generales_cron($taches_generales)
{
    // Par défaut le plugin réalise la synchro toutes les 10 minutes.
    if (!defined('_GALETTONUTS_DELAIS_CRON'))
    {
        if (file_exists(_DIR_TMP . 'galettonuts_cron.php'))
            include _DIR_TMP . 'galettonuts_cron.php';
        else
            define('_GALETTONUTS_DELAIS_CRON', 600);
    }
    
    $taches_generales['galettonuts_cron'] = _GALETTONUTS_DELAIS_CRON;
    return $taches_generales;
}

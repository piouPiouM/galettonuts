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
 * Gestionnaire d'installation, désinstallation.
 *
 * PHP versions 4 and 5
 *
 * @package   Galettonuts
 * @author    Mehdi Kabab <pioupioum@tuxfamily.org>
 * @copyright Copyright (C) 2008 Mehdi Kabab
 * @license   http://www.gnu.org/licenses/gpl2.html  GPL Licence 2.0
 * @version   0.1
 * @link      http://pioupioum.tuxfamily.org/scripts/spip/galettonuts
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Retourner le numéro de version du plugin.
 * 
 * @return string
 **/
function galettonuts_version()
{
    return '0.1';
}

function galettonuts_upgrade()
{
    $version_installee = (isset($GLOBALS['meta']['galettonuts_version']) ? $GLOBALS['meta']['galettonuts_version'] : '0.0');

    // La version installée est la dernière en date
    if ($version_installee == galettonuts_version())
        return;

    // Nouvelle installation
    else if (version_compare($version_installee, '0.0', 'eq'))
    {
        include_spip('base/galettonuts_tables');
        include_spip('base/create');
        include_spip('base/abstract_sql');
        creer_base();
        
        ecrire_meta('galettonuts_version', galettonuts_version());
        ecrire_meta('galettonuts_config', serialize(array(
            'adresse_db'    => 'localhost',
            'prefix_db'     => 'galette_',
            'db_ok'         => false,
            'activer_cron'  => true,
            'heures'        => 0,
            'minutes'       => 30
        )));
        ecrire_meta('galettonuts_synchro', serialize(array(
            'frequence' => 600
        )));
        ecrire_metas();
        
        echo '<br />',
             debut_boite_info(true),
             '<strong>', _T('galettonuts:installation_succes'),'</strong>',
             '<p>', _T('galettonuts:texte_installation_succes'), '</p>',
             fin_boite_info(true);
        
        return;
    }

    $version_comparaison = version_compare(galettonuts_version(), $version_installee);

    // Mise à jour
    if (-1 == $version_comparaison)
    {
        // TODO: Gérer un Upgrade
        ecrire_meta('galettonuts_version', galettonuts_version());
        return;
    }
    // Régression
    else if (1 == $version_comparaison)
    {
        // TODO: Gérer un Downgrade
        return;
    }
}

function galettonuts_vider_tables()
{
    $res = spip_query("SELECT `id_auteur` FROM `spip_galettonuts` WHERE 1;");
    while ($row = @mysql_fetch_array($res))
    {
        spip_query("DELETE FROM `spip_auteurs` WHERE `spip_auteurs`.`id_auteur`={$row['id_auteur']};");
    }

    if (isset($GLOBALS['meta']['galettonuts_config']))
    {
        $config = unserialize($GLOBALS['meta']['galettonuts_config']);
        if (isset($config['zones']))
        {
            include dirname(__FILE__) . '/../inc/galettonuts_fonctions.php';
            galettonuts_dissocier_zones($config['zones']);
        }
        unset($config);
    }

    spip_query('DROP TABLE IF EXISTS spip_galettonuts');
    effacer_meta('galettonuts_version');
    effacer_meta('galettonuts_config');
    effacer_meta('galettonuts_synchro');
    ecrire_metas();
    if (file_exists(_DIR_TMP . 'galettonuts_cron.lock'))
        unlink(_DIR_TMP . 'galettonuts_cron.lock');
    if (file_exists(_DIR_TMP . 'galettonuts_cron.php'))
        unlink(_DIR_TMP . 'galettonuts_cron.php');
}

function galettonuts_install($action)
{
    switch ($action)
    {
        case 'test':
            return (isset($GLOBALS['meta']['galettonuts_version']) && (galettonuts_version() <= $GLOBALS['meta']['galettonuts_version']));
            break;
        case 'install':
            galettonuts_upgrade();
            break;
        case 'uninstall':
            galettonuts_vider_tables();
            break;
    }
}

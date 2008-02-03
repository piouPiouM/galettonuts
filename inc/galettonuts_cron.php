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
 * Cron de Galettonuts.
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

function cron_galettonuts_cron($t)
{
    if (!class_exists('L2_Spip_Plugin_Metas'))
        include_spip('lib/L2/Spip/Plugin/Metas.class');
    
    $config = new L2_Spip_Plugin_Metas('galettonuts_config');
    
    // La synchronisation automatique est désactivée
    if (!$config->lire('activer_cron'))
    {
        spip_log('cron: galettonuts_cron, desactivee.');
        return null;
    }
    
    include_spip('inc/galettonuts_fonctions');
    
    $code    = (int) galettonuts_synchroniser();
    $synchro = new L2_Spip_Plugin_Metas('galettonuts_synchro');
    
    if (-2 == $code || -1 == $code || 0 == $code)
    {
        spip_log('cron: galettonuts_cron, echec.');
        return (0 - $t);
    }
    else if (-10 == $code)
    {
        spip_log('cron: galettonuts_cron, auteurs a jour.');
        return null;
    }
    else
    {
        spip_log('cron: galettonuts_cron, synchronisation de ' . $code . ' auteurs galette dans spip.');
        return 1;
    }
}

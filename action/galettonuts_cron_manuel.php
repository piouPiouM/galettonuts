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
 * Synchronisation manuelle.
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

function action_galettonuts_cron_manuel_dist()
{
    $securiser_action = charger_fonction('securiser_action', 'inc');
    $arg = $securiser_action('galettonuts-0.1');
    
    $voir_statut = _request('statut');
    if ($voir_statut) $voir_statut = '&statut=' . $voir_statut;
    
    include_spip('inc/galettonuts_fonctions');
    $code = (int) galettonuts_synchroniser();
    redirige_par_entete(generer_url_ecrire(
        _request('redirect'),
        'galettonuts_synchro_ok=oui&code_retour=' . $code . $voir_statut, true)
    );
}

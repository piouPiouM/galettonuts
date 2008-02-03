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
 * Description des tables SQL.
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

global $tables_principales;
global $table_des_tables;

// Déclaration de la structure de la table SQL
$spip_galettonuts = array(
    'id_auteur' => "BIGINT(21) NOT NULL",
    'id_adh'    => "INT(10) UNSIGNED NOT NULL"
);

$spip_galettonuts_keys = array(
    'PRIMARY KEY'   => "id_auteur, id_adh",
    'KEY id_adh'    => "id_adh"
);

$tables_principales['spip_galettonuts'] = array(
    'field'    => &$spip_galettonuts,
    'key'      => &$spip_galettonuts_keys
);

// Table des tables
$table_des_tables['galettonuts'] = 'galettonuts';

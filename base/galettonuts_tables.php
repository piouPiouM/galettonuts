<?php
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

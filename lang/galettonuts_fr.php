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
 * Localisation française du plugin.
 *
 * PHP versions 4 and 5
 *
 * @package   Galettonuts
 * @author    Mehdi Kabab <pioupioum@tuxfamily.org>
 * @copyright Copyright (C) 2008 Mehdi Kabab
 * @license   http://www.gnu.org/licenses/gpl2.html  GPL Licence 2.0
 * @version   0.1
 */

$GLOBALS[$GLOBALS['idx_lang']] = array(
    // A
    'avis_connexion_echec_1' => 'La connexion au serveur MySQL de votre installation Galette a &eacute;chou&eacute;.',
    'avis_connexion_echec_2' => 'La s&eacute;lection de la base de votre installation Galette a &eacute;chou&eacute;.',
    
    // B
    'bouton_supprimer'    => 'Supprimer',
    'bouton_synchroniser' => 'Synchroniser',
    
    // C
    'configuration_lien'      => 'Rendez-vous sur sa <a href="@url@">page de configuration</a> afin d&#x27;y renseigner les param&egrave;tres d&#x27;acc&egrave;s &agrave; Galette.',
    'configuration_manquante' => 'Le plugin Galettonuts n&#x27;a pas encore &eacute;t&eacute; configur&eacute; !',
    
    // D
    'derniere_maj'  => 'La derni&egrave;re mise &agrave; jour a eu lieu le @jour@/@mois@/@annee@ &agrave; @heures@h@minutes@m@secondes@s.',
    
    // E
    'entree_cron_utiliser'      => '&nbsp;Utiliser la synchronisation automatique',
    'entree_cron_utiliser_non'  => '&nbsp;Ne pas utiliser la synchronisation automatique',
    'entree_db_adresse' => 'Adresse de la base de donn&eacute;es&nbsp;:',
    'entree_db_choix'   => 'Nom de la base&nbsp;:',
    'entree_db_login'   => 'Le login de connexion&nbsp;:',
    'entree_db_mdp'     => 'Le mot de passe de connexion&nbsp;:',
    'entree_db_prefix'  => 'Prefix&nbsp;:',
    'entree_synchroniser' => 'Synchroniser',
    'etat_synchro_echec'        => 'La synchronisation a &eacute;chou&eacute;e !',
    'etat_synchro_erreur'       => 'Erreur de synchronisation !<br />Une erreur inconnue est survenue.',
    'etat_synchro_erreur_bdd'   => 'Erreur de synchronisation !<br />La connexion &agrave; la base de donn&eacute;es Galette a &eacute;chou&eacute;e.',
    'etat_synchro_inutile'      => 'Aucune fiche adh&eacute;rent Galette n&#x27;a &eacute;t&eacute; modifi&eacute; depuis la derni&egrave;re synchronisation.',
    'etat_synchro_ok'           => 'La synchronisation s&#x27;est correctement d&eacute;roul&eacute;e.<br />Au total, @nb@ fiche(s) ont &eacute;t&eacute; mises &agrave; jour.',
    
    // F
    'frequence' => 'R&eacute;aliser la synchronisation toutes les ',
    
    // G
    // H
    'heures'    => '&nbsp;heure(s) ',
    
    // I
    'icone_db_erreur'   => '&Eacute;chec d&#x27;acc&egrave;s &agrave; la base de donn&eacute;es de Galette',
    'icone_db_ok'       => 'L&#x27;acc&egrave;s &agrave; la base de donn&eacute;es de Galette est active',
    'info_admin'            => 'Liaison avec Galette',
    'info_bdd'              => 'Acc&egrave;s &agrave; la base de donn&eacute;es',
    'info_bdd_nom'          => 'Nom de la base de donn&eacute;es&nbsp;:',
    'info_cron' => 'Synchronisation automatique',
    'info_information'  => 'Information',
    'info_liaison_access_restreint' => 'Associer des zones d&#x27;acc&egrave;s restreint',
    'installation_echec'    => '&Eacute;chec de l&#x27;installation de Galettonuts.',
    'installation_succes'   => 'Galettonuts a &eacute;t&eacute; install&eacute; avec succ&egrave;s.',
    'icone_db_config'   => 'En attente d&#x27;un acc&egrave;s &agrave; la base de donn&eacute;es de Galette',
    
    // J
    // K
    // L
    // M
    'minutes'   => '&nbsp;minute(s)',
    
    // N
    'ne_rien_faire' => 'Ne rien faire',
    
    // O
    // P
    // Q
    // R
    // S
    // T
    'texte_erreur'      => 'Erreur',
    'texte_erreurs'     => 'Erreur(s)',
    'texte_erreur_1'    => 'Un ou plusieurs champs ne sont pas renseign&eacute;s.',
    'texte_info_admin'  => 'Cette page vous permet de g&eacute;rer la connexion du plugin avec Galette ainsi que la synchronisation p&eacute;riodique.<br /><br />En pr&eacute;sence du plugin <em>Acc&egrave;s Restreint</em>, il vous sera propos&eacute; d&#x27;ajouter les adh&eacute;rents &agrave; vos zones.',
    'texte_info_bdd'    => 'Renseignez ci-contre les informations de connexion &agrave; la BDD de Galette.<br /><br />Actuellement, MySQL est le seul type de base support&eacute;.',
    'texte_info_cron'   => 'La synchronisation automatique permet de tenir &agrave; jour la base utilisateurs de Spip sans aucune intervention de votre part. Pour en profiter, il vous suffit de l&#x27;activer puis de d&eacute;finir la fr&eacute;quence &agrave; laquelle sera lanc&eacute;e la proc&eacute;dure.',
    'texte_installation_succes' => 'Se rendre sur sa <a href="?exec=admin_galettonuts">page de configuration</a>.',
    'texte_liaison_access_restreint_1' => 'Le plugin <em>Acc&egrave;s Restreint</em> &eacute;tant actif, vous avez la possibilit&eacute; d&#x27;ajouter automatiquement les utilisateurs qui seront synchronis&eacute;s &agrave; une ou plusieurs zones.',
    'texte_liaison_access_restreint_2' => '<strong>Note&nbsp;:</strong> tout nouveau choix se r&eacute;percute sur l&#x27;int&eacute;gralit&eacute; des utilisateurs synchronis&eacute;s.',
    'texte_synchro_manuelle'    => 'Lancer une synchronisation manuelle des utilisateurs de votre installation Galette vous permet de mettre &agrave; jour expressement les auteurs de Spip.<br /> Vous pouvez  y avoir recours lorsque vous ne souhaitez pas attendre que la synchronisation automatique se fasse.',
    'titre_admin'               => 'Configuration de Galettonuts',
    'titre_formulaire_synchro'  => 'Synchronisation Galette',
    'titre_page_admin'          => 'Galettonuts',
    
    // U
    // V
    // W
    // X
    // Y
    // Z
);

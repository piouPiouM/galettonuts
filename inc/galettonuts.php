<?php
/**
 * Interface de synchronisation manuelle.
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 2.0 of the GPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/gpl2.html.
 * You should have received a copy of the GNU General Public License
 * along with Galettonuts (LICENCE.txt); if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package    Galettonuts
 * @author     Mehdi Kabab
 * @license    http://www.gnu.org/licenses/gpl2.html  GPL Licence 2.0
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function galettonuts_formulaire_synchro()
{
    global $spip_lang_left, $spip_lang_right;
    include_spip('inc/config');
    
    // Détermine si on dépli le cadre
    if ('oui' == _request('galettonuts_synchro_ok'))
        $visibilite = 'visible';
    else
        $visibilite = 'invisible';
    
    $return  = debut_cadre_couleur('synchro-24.gif', true, '', call_user_func('bouton_block_' . $visibilite, 'galettonuts_synchro') . _T('galettonuts:titre_formulaire_synchro'));
    $return .= call_user_func('debut_block_' . $visibilite, 'galettonuts_synchro');
    
    // Le plugin n'a pas encore été configuré
    if (!class_exists('L2_Spip_Plugin_Metas'))
        include_spip('lib/L2/Spip/Plugin/Metas.class');
    $config = new L2_Spip_Plugin_Metas('galettonuts_config');
    if (!$config || !$config->lire('db_ok'))
    {
        $return .= debut_cadre_relief('', true);
        $return .= http_img_pack('warning.gif', _T('info_avertissement'), 'style="width:48px;height:48px;float:right;margin:5px;"');
        $return .= '<p class="verdana2"><strong>';
        $return .= _T('galettonuts:configuration_manquante');
        $return .= '</strong><br />';
        $return .= _T('galettonuts:configuration_lien', array('url' => generer_url_ecrire('admin_galettonuts')));
        $return .= '</p>';
        $return .= fin_cadre_relief(true);
        $return .= fin_block();
        $return .= fin_cadre_couleur(true);
        return $return;
    }
    unset($config);
    
    // Affichage des erreurs
    if ($statut = (int) _request('code_retour'))
    {
        switch ($statut)
        {
            // Une erreur inconnue est survenue.
            case 0:
                $msg  = '<p class="verdana2"><strong>' . _T('info_avertissement') . '</strong><br />';
                $msg .= _T('galettonuts:etat_synchro_erreur') . '</p>';
                $alt = _T('info_avertissement');
                $img = _DIR_PLUGIN_GALETTONUTS . 'img_pack/error-48.png';
                break;

            // Des erreurs sont survenues lors de la connexion à la BDD.
            case -2:
                $msg  = '<p class="verdana2"><strong>' . _T('info_avertissement') . '</strong><br />';
                $msg .= _T('galettonuts:etat_synchro_erreur_bdd') . '</p>';
                $alt = _T('info_avertissement');
                $img = _DIR_PLUGIN_GALETTONUTS . 'img_pack/error-48.png';
                break;

            // La synchronisation a échouée.
            case -1:
                $msg  = '<p class="verdana2"><strong>' . _T('info_avertissement') . '</strong><br />';
                $msg .= _T('galettonuts:etat_synchro_echec') . '</p>';
                $alt = _T('info_avertissement');
                $img = _DIR_PLUGIN_GALETTONUTS . 'img_pack/error-48.png';
                break;

            // La synchronisation est inutile.
            case -10:
                $msg  = '<p class="verdana2"><strong>' . _T('galettonuts:info_information') . '</strong><br />';
                $msg .= _T('galettonuts:etat_synchro_inutile') . '</p>';
                $alt = _T('galettonuts:info_information');
                $img = _DIR_PLUGIN_GALETTONUTS . 'img_pack/information-48.png';
                break;

            // La synchronisation s'est déroulée correctement
            default:
                $msg  = '<p class="verdana2"><strong>' . _T('galettonuts:info_information') . '</strong><br />';
                $msg .= _T('galettonuts:etat_synchro_ok', array('nb' => $statut)) . '</p>';
                $alt = _T('galettonuts:info_information');
                $img = _DIR_PLUGIN_GALETTONUTS . 'img_pack/information-48.png';
                break;
        }
        
        $return .= debut_cadre_relief('', true);
        $return .= http_img_pack($img, $alt, 'style="width:48px;height:48px;float:right;margin:5px;"');
        $return .= $msg;
        $return .= fin_cadre_relief(true);
    }
    
    $voir_statut = _request('statut');
    if ($voir_statut) $voir_statut = '&statut=' . $voir_statut;
    
    $action = generer_action_auteur('galettonuts_cron_manuel', 'galettonuts-0.1&' . $voir_statut, 'auteurs');
    
    $return .= '<p class="verdana2">' . _T('galettonuts:texte_synchro_manuelle') . '</p>';
    $return .= '<form action="' . $action . '" method="post>"';
    $return .= form_hidden($action);
    
    // Dernière mise à jour
    $synchro = new L2_Spip_Plugin_Metas('galettonuts_synchro');
    
    if ('' != $maj = $synchro->lire('maj'))
    {
        $return .= '<p class="verdana2">' . _T('galettonuts:derniere_maj', array(
            'annee' => date('Y', $maj),
            'mois'  => date('m', $maj),
            'jour'     => date('d', $maj),
            'heures'   => date('G', $maj),
            'minutes'  => date('i', $maj),
            'secondes' => date('s', $maj),
        ));
        $return .= '</p>';
    }
    
    // Bouton de validation
    $return .= '<div style="text-align:right;padding:0 2px;margin-top:.5em" id="buttons">';
    $return .= '<input type="submit" name="_galettonuts_ok" value="' . _T('galettonuts:entree_synchroniser') . '" class="fondo" style="cursor:pointer"/></div>';
    $return .= '</form>';
    $return .= fin_block();
    $return .= fin_cadre_couleur(true);
    
    return $return;
}

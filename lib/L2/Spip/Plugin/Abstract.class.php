<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Lyon2 - Plugin pour Spip
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 2.0 of the GPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt.
 * You should have received a copy of the GNU General Public License
 * along with L2 Spip Plugin (LICENCE.txt); if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category   L2_Spip
 * @package    L2_Spip_Plugin
 * @author     Mehdi Kabab <mehdi.kabab@univ-lyon2.fr>
 * @copyright  Copyright (c) 2007 Université Lumière Lyon 2 (http://www.univ-lyon2.fr)
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt  GPL Licence 2.0
 * @version    $Id:$
 */

if (!class_exists('L2_Spip_Plugin_Metas'))
    include dirname(__FILE__) . '/Metas.class.php';

/**
 * Couche d'abstraction.
 *
 * @category   L2_Spip
 * @package    L2_Spip_Plugin
 * @author     Mehdi Kabab <mehdi.kabab@univ-lyon2.fr>
 * @copyright  Copyright (c) 2007 Université Lumière Lyon 2 (http://www.univ-lyon2.fr)
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt  GPL Licence 2.0
 * @version    Release: @package_version@
 */
class L2_Spip_Plugin_Abstract
{

// {{{ Propriétés

    /**#@+
     * @access public
     */
    /**
     * Configuration du plugin.
     * @var array
     **/
    var $config;
    
    /**
     * Objet
     * @var StdClass
     **/
    var $obj;
    /**#@-*/
    
    /**#@+
     * @access protected
     */
    var $_bouton = array('ok', 'supprimer', 'restaurer');
    var $_contexte;
    /**#@-*/
    
    /**#@+
     * @access private
     */
    var $_donneesInfos;
    var $_contenu;
    /**#@-*/

// }}}
// {{{ Méthodes publiques
    
    /**
     * @access public
     */
    function L2_Spip_Plugin_Abstract($obj)
    {
        // Test d'authorisation d'accès
        $this->_testSiAuthorise();
        
        // Chargement des fichiers utiles
        $this->_chargerIncludes();
        
        // Initialisation des propriétés
        $this->obj = $obj;
        $this->config        = new L2_Spip_Plugin_Metas($this->obj->prefix . '_' . 'config');
        $this->_contenu      = '';
        $this->_donneesInfos = '';
        
        // Lecture des données éventuellement stockées
        $this->_contexte = $this->config->lire();
        
        // Générer des champs prédéfinis à ajouter dans les squelettes
        // de la manière suivante : [(#ENV{_mutualiser_}|form_hidden)]
        $this->_genererChampsMasques();
        
        // Création de la liste des boutons d'actions.
        // Avant : Array(ok, delete, initialize)
        // Après : Array(ok => _prefix_ok, delete => _prefix_delete, initialize => _prefix_initialize)
        array_walk($this->_bouton = array_flip($this->_bouton), create_function('&$v,$k,$u', '$v = "_".$u."_".$k;'), $this->obj->prefix);
    }
    
    /**
     * Traite les données reçues ou à afficher.
     * 
     * @return void
     * @access public
     **/
    function traiter()
    {
        // En cas d'envoi de données, on récupère les valeurs pour les stocker
        if (_request($this->_bouton['ok']))
        {
            $this->_actionValider();
        }
        
        // Demande d'annulation
        else if (_request($this->_bouton['supprimer']))
        {
            $this->_actionSupprimer();
        }
        
        // $securiser_action = charger_fonction('securiser_action', 'inc');
        // $securiser_action();
    }
    
    /**
     * Génére l'affichage.
     * 
     * @param  boolean $return Définie à true la méthode retoune le résultat, l'affiche sinon.
     * @return string
     * @access public
     **/
    function afficher($return = false)
    {
        ob_start();
        echo $this->_debutPage();
        echo $this->_afficherInfos();
        echo $this->_afficherContenu();
        echo $this->_finPage();
        $donnees = ob_get_clean();
        
        if ($return)
            return $donnees;
        
        echo $donnees;
    }
    
    /**
     * Logguer des informations dans le fichier spip.log.
     * 
     * @param  string $msg Informations à logguer
     * @return void
     * @access public
     **/
    function log($msg)
    {
        ($GLOBALS['auteur_session'] && ($qui = $GLOBALS['auteur_session']['login'])) || ($qui = $GLOBALS['ip']);
        spip_log('[plugin] ' . $this->obj->prefix .' (' . $this->obj->nom . ') par ' . $qui . ' : ' . $msg);
    }

// }}}
// {{{ Méthodes protégées

    
    function _stocker(){}
    
    
    function _ajouterInfos($msg)
    {
        $this->_donneesInfos .= (string) $msg;
    }
    
    
    function _ajouterContenu($msg)
    {
        $this->_contenu .= (string) $msg;
    }
    
    
    function _afficherContenu()
    {
        return debut_droite('', true) . $this->_contenu;
    }
    
    
    function _afficherInfos()
    {
        return (!empty($this->_donneesInfos)) ? $this->_donneesInfos : '';
    }
    
    
    
    function _chargerIncludes()
    {
        include_spip('inc/meta');
        include_spip('inc/presentation');
        include_spip('public/assembler');
    }
        
    function _recupererDonnees($var = null)
    {
        $champs = array();
        $var    = (is_null($var)) ? $_REQUEST : $var;
        
        // Parcours des données soumises
        foreach ($var as $nom => $valeur)
        {
            $champs[$nom] = _request($nom);
        }
        
        // Population du contexte
        $this->_contexte = array_merge($this->_contexte, $champs);
    }
    
    function _actionValider()
    {
        $this->_recupererDonnees();
        $this->_stocker();
    }
    
    function _actionSupprimer()
    {
        $status          = $this->config->supprimer($_REQUEST);
        $this->_contexte = $this->config->lire();
    }

    function _ajouterChampsMasques($champs)
    {
        foreach ($champs as $key => $value)
        {
            $this->_contexte['_' . $this->obj->prefix . '_'] .= '&' . (string) $key . '=' . (string) $value;
        }
    }

// }}}
// {{{ Méthodes privées

    function _testSiAuthorise()
    {
        if (!('0minirezo' == $GLOBALS['connect_statut'] && $GLOBALS['connect_toutes_rubriques']))
        {
            include_spip('inc/minipres');
            echo minipres(_T('forum_titre_erreur'), _T('avis_non_acces_page'));
            exit;
        }
    }
    
    function _debutPage()
    {
        $commencer_page  = charger_fonction('commencer_page', 'inc');
        $return  = $commencer_page(_T(strtolower($this->obj->prefix) . ':titre_page_' . $this->obj->nom), $this->obj->prefix, $this->obj->nom);
        $return .= '<br/><br/><br/>' . PHP_EOL;
        $return .= gros_titre(_T(strtolower($this->obj->prefix) . ':titre_' . $this->obj->nom), null, false);
        $return .= $this->_barreOnglets();
        $return .= debut_gauche('', true);
        return $return;
    }
    
    function _finPage()
    {
        return fin_gauche() . fin_page();
    }
    
    function _barreOnglets()
    {
        if (function_exists($this->obj->prefix . '_ajouter_onglets'))
            return barre_onglets($this->obj->prefix, $this->obj->nom);
        else
            return '';
    }
    
    function _genererChampsMasques()
    {
        include_spip('inc/securiser_action');
        $arg = $this->obj->prefix . '0.0.0-' . $this->obj->nom;
        $this->_contexte['_' . $this->obj->prefix . '_'] =
                '?exec=' . $this->obj->exec
            .   '&arg='  . $arg
            .   '&hash=' . calculer_action_auteur('-' . $arg);
    }
    
// }}}

}

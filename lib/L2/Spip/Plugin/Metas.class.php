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
 * @copyright  Copyright (c) 2007 Université Lumière Lyon 2 (http://www.univ-lyon2.fr)
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt  GPL Licence 2.0
 * @version    $Id:$
 */

/**
 * Gérer la configuration via les meta.
 *
 * @category   L2_Spip
 * @package    L2_Spip_Plugin
 * @author     Mehdi Kabab <mehdi.kabab@univ-lyon2.fr>
 * @copyright  Copyright (c) 2007 Université Lumière Lyon 2 (http://www.univ-lyon2.fr)
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt  GPL Licence 2.0
 * @version    Release: @package_version@
 */
class L2_Spip_Plugin_Metas
{

// {{{ Propriétés

    /**#@+
     * @access private
     */
    var $_config = array();
    var $_isLoad = false;
    var $_nom;
    /**#@-*/

// }}}
// {{{ Méthodes publiques

    function L2_Spip_Plugin_Metas($nom)
    {
        $this->_nom = $nom;
        $this->_charger();
    }

    /**
     * Lire la configuration.
     * 
     * Retourne par défaut l'intégralité de la configuration stockée dans 
     * la base meta de Spip. Si une clé lui est fourni et que cette dernière 
     * existe dans la configuration chargée, elle retourne alors sa valeur 
     * associée, une chaîne vide sinon.
     * Dans le cas où la configuration est innexistante, NULL est retourné.
     * 
     * @param  string $key
     * @return mixed
     * @access public
     **/
    function lire($key = null)
    {
        if (!$this->_isLoad && !$this->_charger())
        {
            spip_log('[Attention] (L2_Spip_Plugin_Metas) La configuration ' . $this->_nom . ' n\'existe pas.');
            return null;
        }
        
        if ($key)
        {
            return ($this->existe($key) ? $this->_config[$key] : '');
        }
        else
        {
            return $this->_config;
        }
    }
    
    /**
     * Ajouter un élément à la configuration.
     * 
     * @param  mixed   $config     Elément à ajouter.
     * @param  boolean $maintenant Déclenche l'écriture de la configuration 
     *                             dans la table meta de Spip.
     * @return boolean
     * @access public
     **/
    function ajouter($config, $maintenant = true)
    {
        $this->_config = array_merge($this->_config, (array) $config);
        if ($maintenant)
            return $this->_ecrire();
        return true;
    }
    
    /**
     * Supprimer un élément de la configuration.
     * 
     * Supprime par défaut totalement la configuration de la table meta 
     * de Spip. Si {@see $config} est fourni, seuls les index identiques
     * seront supprimés.
     * 
     * @param  array Tableau associatif des éléments à supprimer.
     * @return boolean
     * @access public
     **/
    function supprimer($config = array())
    {
        // Suppression de certaines entrées de la configuration
        if (count($config))
        {
            foreach ($config as $key => $value)
            {
                if ($this->existe($key))
                    unset($this->_config[$key]);
            }
            return $this->_ecrire();
        }
        // Effacement total de la configuration
        else
        {
            effacer_meta($this->_nom);
            ecrire_metas();
            return (isset($GLOBALS['meta'][$this->_nom]) ? false : true);
        }
    }
    
    /**
     * Tester l'existance d'une clée dans la configuration.
     * 
     * @param  string $key
     * @return boolean
     * @access public
     **/
    function existe($key)
    {
        if (count($this->_config))
            return array_key_exists($key, $this->_config);
        else if ($this->lire())
            return array_key_exists($key, $this->_config);
        else
            return false;
    }

// }}}
// {{{ Méthodes protégées



// }}}
// {{{ Méthodes privées

    /**
     * Charger la configuration à partir de la table meta de Spip.
     * 
     * @return boolean
     * @access private
     **/
    function _charger()
    {
        if (isset($GLOBALS['meta'][$this->_nom]))
        {
            $this->_config = array_merge($this->_config, unserialize($GLOBALS['meta'][$this->_nom]));
            $this->_isLoad = true;
            return true;
        }
        $this->_isLoad = false;
        return false;
    }

    /**
     * Ecrire la configuration dans la base meta de Spip.
     * 
     * @return boolean
     * @access private
     **/
    function _ecrire()
    {
        $serialize = serialize($this->_config);
        ecrire_meta($this->_nom, $serialize);
        ecrire_metas();
        return (($serialize === $GLOBALS['meta'][$this->_nom]) ? true : false);
    }

// }}}

}

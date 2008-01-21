<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Lyon2 - Plugin pour Spip
 *
 * PHP versions 4
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
 * Système de débuguage rudimentaire.
 *
 * @category   L2_Spip
 * @package    L2_Spip_Plugin
 * @author     Mehdi Kabab <mehdi.kabab@univ-lyon2.fr>
 * @copyright  Copyright (c) 2007 Université Lumière Lyon 2 (http://www.univ-lyon2.fr)
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt  GPL Licence 2.0
 * @version    Release: @package_version@
 */
class L2_Spip_Plugin_Debug
{

// {{{ Propriétés

    /**
     * Pile de stockage des données de débuguage
     * @var array
     * @access private
     **/
    var $_debug;
    
// }}}
// {{{ Méthodes publiques

    /**
     * Récupérer toutes les informations de la pile.
     * 
     * @param  string  $format Format de sortie?
     * @param  boolean $reset  Faut-il effacer la pile ? (defaut : true).
     * @return string
     * @access public
     * @static
     */
    function get($format = 'html', $reset = true)
    {
        $instance =& L2_Spip_Plugin_Debug::_getInstance();
        
        switch ($format)
        {
            case 'txt':
                $return = L2_Spip_Plugin_Debug::_getInText();
                break;
            case 'html':
            default:
                $return = L2_Spip_Plugin_Debug::_getInHtml();
                break;
        }
        
        if ($reset) $instance->_debug = array();
        return $return;
    }
    
    /**
     * Ajouter une information de debug dans la pile.
     *
     * @param  mixed  $var   Variable à afficher sous forme brute.
     * @param  string $title Description courte de {@see $var}.
     * @return void
     * @access public
     * @static
     */
    function log($var, $title = '')
    {
        $instance =& L2_Spip_Plugin_Debug::_getInstance();
        
        if (!empty($title))
            $instance->_debug[] = array('_title_' => $title, (array) $var);
        else
            $instance->_debug[] = (array) $var;
    }

// }}}
// {{{ Méthodes privées
    
    /**
     * @access private
     **/
    function L2_Spip_Plugin_Debug($directCall = true)
    {
        if ($directCall)
        {
            include_spip('inc/minipres');
            echo minipres(_T('forum_titre_erreur'), _T('zbug_erreur_execution_page'));
            spip_log('[L2_Spip_Plugin_Debug] Attention : L2_Spip_Plugin_Debug n\'est pas directement instanciable !');
            exit;
        }
        
        $this->_debug = array();
    }
    
    /**
     * Retourne une instance de la classe.
     * 
     * @return L2_Spip_Plugin_Debug
     * @access private
     * @static
     **/
    function &_getInstance()
    {
        static $instance;
        if (!$instance)
            $instance = new L2_Spip_Plugin_Debug(false);
        return $instance;
    }
    
    /**
     * @access private
     * @static
     */
    function _getInHtml()
    {
        $instance =& L2_Spip_Plugin_Debug::_getInstance();
        
        if (0 === count($instance->_debug))
            return;
        
        $return  = '<div style="background-color:#ddd;border:1px solid #111; color:#000;padding:0;margin:.5em">';
        $return .= '<h3 style="background-color:#fcc;border-bottom:1px solid #aaa;margin:0">' . _T('admin_debug') . '</h3>';
        $return .= '<div style="padding:0 .5em 0">';
        
        foreach($instance->_debug as $array)
        {
            $return .= '<div style="background-color:#ffc;border:1px solid #aaa;color:#000;margin:.3em 0;text-align:left;">';
            
            foreach($array as $key => $value)
            {
                
                if ('_title_' == (string) $key)
                {
                    $return .= '<strong style="background-color:#CF6;display:block;padding:.2em .5em">' . $value . '</strong>';
                    continue;
                }
                $return .= '<pre style="padding:.5em;margin:.5em">' . print_r($value, 1) . '</pre>';
            }
            
            $return .= '</div>';
        }
        
        return $return . '</div></div>';
    }
    
    /**
     * @access private
     * @static
     */
    function _getInText(){}

// }}}

}

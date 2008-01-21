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
 * Fabrique.
 *
 * @category   L2_Spip
 * @package    L2_Spip_Plugin
 * @author     Mehdi Kabab <mehdi.kabab@univ-lyon2.fr>
 * @copyright  Copyright (c) 2007 Université Lumière Lyon 2 (http://www.univ-lyon2.fr)
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt  GPL Licence 2.0
 * @version    Release: @package_version@
 **/
class L2_Spip_Plugin_Factory
{

// {{{ Méthodes publiques

    /**
     * Créer un objet de type L2_Spip_Plugin_Abstract.
     * 
     * @param   string $nom
     * @param   string $prefix
     * @param   string $repertoire
     * @return  L2_Spip_Plugin_Abstract
     * @access public
     * @static
     **/
    function creer($nom, $prefix = null, $repertoire = 'inc')
    {        
        if (!$prefix)
        {
            include_spip('inc/minipres');
            echo minipres(_T('forum_titre_erreur'), _L('<code>L2_Spip_Plugin_Factory::creer(\''. $nom .'\')</code> : Pr&eacute;fix non renseign&eacute;'));
            exit;
        }
        
        $nomFormate    = str_replace(' ', '', ucwords(str_replace('_', ' ', $nom)));
        $prefixFormate = str_replace(' ', '', ucwords(str_replace('_', ' ', $prefix)));
        $className     = $prefixFormate . '_' . $nomFormate;
        include_spip((string) $repertoire . '/' . $prefixFormate . '/' . $nomFormate . '.class');
        
        $obj = new StdClass();
        $obj->nom                   = $nom;
        $obj->prefix                = $prefix;
        $obj->nomComplet            = $prefix . '_' . $nom;
        $obj->nomCompletMinuscules  = strtolower($obj->nomComplet);
        $obj->exec       = _request('exec');
        $obj->action     = _request('action');
        
        return new $className($obj);
    }

// }}}

}

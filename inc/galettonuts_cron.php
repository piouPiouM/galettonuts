<?php
/**
 * Cron de Galettonuts.
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

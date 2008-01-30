<?php
/**
 * Synchronisation manuelle.
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

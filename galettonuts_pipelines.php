<?php
/**
 * Déclaration des pipelines.
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

$p = explode(basename(_DIR_PLUGINS) . '/', str_replace('\\', '/', realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_GALETTONUTS', _DIR_PLUGINS . end($p));
unset($p);

function galettonuts_affiche_milieu($flux)
{
    // On se branche sur la page Auteurs
    if ('auteurs' == $flux['args']['exec'])
    {
        include_spip('inc/galettonuts');
        // Le plugin n'est accessible qu'au(x) super-admin(s)
        if ('0minirezo' === $GLOBALS['auteur_session']['statut'] && $GLOBALS['connect_toutes_rubriques'])
            $flux['data'] .= galettonuts_formulaire_synchro();
    }
    
    return $flux;
}

function galettonuts_taches_generales_cron($taches_generales)
{
    // Par défaut le plugin réalise la synchro toutes les 10 minutes.
    if (!defined('_GALETTONUTS_DELAIS_CRON'))
    {
        if (file_exists(_DIR_TMP . 'galettonuts_cron.php'))
            include _DIR_TMP . 'galettonuts_cron.php';
        else
            define('_GALETTONUTS_DELAIS_CRON', 600);
    }
    
    $taches_generales['galettonuts_cron'] = _GALETTONUTS_DELAIS_CRON;
    return $taches_generales;
}

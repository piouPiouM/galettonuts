<?php
/**
 * Gestionnaire d'installation, désinstallation.
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

$GLOBALS['galettonuts_version'] = 0.1;

function galettonuts_upgrade()
{
    $version_base      = $GLOBALS['galettonuts_version'];
    $version_installee = (isset($GLOBALS['meta']['galettonuts_version']) ? $GLOBALS['meta']['galettonuts_version'] : '0.0');
    
    // Si la version installée est la dernière en date
    if ($version_base == $version_installee)
        return;
    
    // Sinon s'il s'agit d'une nouvelle installation
    else if (version_compare($version_installee, '0.0', 'eq'))
    {
        include_spip('base/galettonuts_tables');
        include_spip('base/create');
        include_spip('base/abstract_sql');
        creer_base();
        
        ecrire_meta('galettonuts_version', $version_base);
        ecrire_meta('galettonuts_config', serialize(array(
            'adresse_db'    => 'localhost',
            'prefix_db'     => 'galette_',
            'db_ok'         => false,
            'activer_cron'  => true,
            'heures'        => 0,
            'minutes'       => 10
        )));
        ecrire_meta('galettonuts_synchro', serialize(array(
            'frequence'     => 600
        )));
        ecrire_metas();
        
        echo '<br />',
             debut_boite_info(true),
             '<strong>', _T('galettonuts:installation_succes'),'</strong>',
             '<p>', _T('galettonuts:texte_installation_succes'), '</p>',
             fin_boite_info(true);
        
        return;
    }
    
    $version_comparaison = version_compare($version_base, $version_installee);
    
    // S'il s'agit d'une mise à jour
    if (-1 == $version_comparaison)
    {
        // TODO: Gérer un Upgrade
        return;
    }
    // Ou si on est en situation d'une régression
    else if (1 == $version_comparaison)
    {
        // TODO: Gérer un Downgrade
        return;
    }
}

function galettonuts_vider_tables()
{
    $res = spip_query("SELECT `id_auteur` FROM `spip_galettonuts` WHERE 1;");
    while ($row = @mysql_fetch_array($res))
    {
        spip_query("DELETE FROM `spip_auteurs` WHERE `spip_auteurs`.`id_auteur`={$row['id_auteur']};");
    }
    
    if (isset($GLOBALS['meta']['galettonuts_config']))
    {
        $config = unserialize($GLOBALS['meta']['galettonuts_config']);
        if (isset($config['zones']))
        {
            include dirname(__FILE__) . '/../inc/galettonuts_fonctions.php';
            galettonuts_dissocier_zones($config['zones']);
        }
        unset($config);
    }
    
    spip_query("DROP TABLE IF EXISTS spip_galettonuts");
    effacer_meta('galettonuts_version');
    effacer_meta('galettonuts_config');
    effacer_meta('galettonuts_synchro');
    ecrire_metas();
    if (file_exists(_DIR_TMP . 'galettonuts_cron.lock'))
        unlink(_DIR_TMP . 'galettonuts_cron.lock');
    if (file_exists(_DIR_TMP . 'galettonuts_cron.php'))
        unlink(_DIR_TMP . 'galettonuts_cron.php');
}

function galettonuts_install($action)
{
    switch ($action)
    {
        case 'test':
            return (isset($GLOBALS['meta']['galettonuts_version']) AND ($GLOBALS['galettonuts_version'] <= $GLOBALS['meta']['galettonuts_version']));
        break;
        case 'install':
            galettonuts_upgrade();
        break;
        case 'uninstall':
            galettonuts_vider_tables();
        break;
    }
}

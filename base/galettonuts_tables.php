<?php
/**
 * Description des tables SQL.
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

global $tables_principales;
global $table_des_tables;

// Déclaration de la structure de la table SQL
$spip_galettonuts = array(
    'id_auteur' => "BIGINT(21) NOT NULL",
    'id_adh'    => "INT(10) UNSIGNED NOT NULL"
);

$spip_galettonuts_keys = array(
    'PRIMARY KEY'   => "id_auteur, id_adh",
    'KEY id_adh'    => "id_adh"
);

$tables_principales['spip_galettonuts'] = array(
    'field'    => &$spip_galettonuts,
    'key'      => &$spip_galettonuts_keys
);

// Table des tables
$table_des_tables['galettonuts'] = 'galettonuts';

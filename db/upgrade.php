<?php
// This file is part of Moodle - http://moodle.org/.
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published
// by the Free Software Foundation, either version 3 of the License,
// or (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Upgrade steps for local_metatags.
 *
 * @package    local_metatags
 * @copyright  2026 https://santoshmagar.com.np/
 * @author     santoshmagar.com.np
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade steps for local_metatags.
 *
 * @param int $oldversion Previous plugin version.
 * @return bool Whether the upgrade completed successfully.
 */
function xmldb_local_metatags_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    // if ($oldversion < 2026092401) {
    //     $table = new xmldb_table('local_metatags_tags');

    //     // Older installations used pagetype alone as a unique index.
    //     $legacyindex = new xmldb_index('pagetype', XMLDB_INDEX_UNIQUE, ['pagetype']);
    //     if ($dbman->index_exists($table, $legacyindex)) {
    //         $dbman->drop_index($table, $legacyindex);
    //     }

    //     $compositeindex = new xmldb_index(
    //         'pagetype_urlpath',
    //         XMLDB_INDEX_UNIQUE,
    //         ['pagetype', 'urlpath']
    //     );
    //     if (!$dbman->index_exists($table, $compositeindex)) {
    //         $dbman->add_index($table, $compositeindex);
    //     }

    //     upgrade_plugin_savepoint(true, 2026092401, 'local', 'metatags');
    // }

    return true;
}

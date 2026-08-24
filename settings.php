<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Admin settings registration for local_metatags.
 *
 * @package    local_metatags
 * @copyright  2026 https://santoshmagar.com.np/
 * @author     santoshmagar.com.np
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Single entry point: a link under Appearance that goes straight to the
 * management/overview page. All actual configuration (global tags, per-scope
 * overrides, the "automatic tags" toggle) lives inside manage.php / edit.php
 * themselves rather than in a separate admin settings form.
 */
if ($hassiteconfig) {
    $ADMIN->add('appearance', new admin_externalpage(
        'local_metatags_manage',
        get_string('pluginname', 'local_metatags'),
        new moodle_url('/local/metatags/manage.php'),
        'local/metatags:manage'
    ));
}

// This plugin does not register a standard admin_settingpage.
// so make sure the settings loader does not try to render one for local_metatags.
$settings = null;

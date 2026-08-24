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
 * Hook callbacks for local_metatags.
 *
 * @package    local_metatags
 * @copyright  2026 https://santoshmagar.com.np/
 * @author     santoshmagar.com.np
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_metatags\hook;

defined('MOODLE_INTERNAL') || die();

use core\hook\output\before_standard_head_html_generation;
use local_metatags\tag_applier;

/**
 * Callback handlers for Moodle output hooks.
 */
class hook_callbacks {

    /**
     * Hook callback for Moodle 4.5+.
     *
     * @param before_standard_head_html_generation $hook Output hook instance.
     * @return void
     */
    public static function before_standard_head_html_generation(before_standard_head_html_generation $hook): void {
        global $PAGE, $CFG, $SITE;

        if (during_initial_install() || !empty($CFG->upgraderunning)) {
            return;
        }

        try {
            $tags = tag_applier::get_effective_tags($PAGE);
            $html = tag_applier::apply_tags($tags, $PAGE);
            if ($html !== '') {
                $hook->add_html($html);
            }
        } catch (\Throwable $e) {
            debugging('local_metatags: failed to render tags – ' . $e->getMessage(), DEBUG_DEVELOPER);
        }
    }
}

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
 * Privacy provider for local_metatags.
 *
 * @package    local_metatags
 * @copyright  2026 https://santoshmagar.com.np/
 * @author     santoshmagar.com.np
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_metatags\privacy;

/**
 * Privacy provider for local_metatags.
 *
 * This plugin only stores configuration text entered by administrators and
 * teachers (meta tag names/content, e.g. "description" or "og:title"). It
 * does not store or process any personal data about site users, so it
 * implements the null_provider.
 */
class provider implements \core_privacy\local\metadata\null_provider {
    /**
     * Return the reason this plugin does not store user data.
     *
     * @return string Privacy language string identifier.
     */
    public static function get_reason(): string {
        return 'privacy:metadata';
    }
}

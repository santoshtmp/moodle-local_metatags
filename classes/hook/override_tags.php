<?php
// This file is part of Moodle - http://moodle.org/.
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

namespace local_metatags\hook;

use moodle_page;

/**
 * Allows plugins and themes to modify resolved meta tags before rendering.
 *
 * @package    local_metatags
 * @copyright  2026 https://santoshmagar.com.np/
 * @author     santoshmagar.com.np
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class override_tags {

    /**
     * @param array $tags Resolved tags as tagname, attribute and content arrays.
     * @param moodle_page $page Current Moodle page.
        * @return void
     */
    public function __construct(
        protected array $tags,
        protected moodle_page $page
    ) {
    }

    /**
     * Return the current resolved tags.
     *
        * @return array Resolved tags as tagname, attribute and content arrays.
     */
    public function get_tags(): array {
        return $this->tags;
    }

    /**
     * Replace the resolved tags.
     *
     * @param array $tags Resolved tags as tagname, attribute and content arrays.
        * @return void
     */
    public function set_tags(array $tags): void {
        $this->tags = $tags;
    }

    /**
     * Return the page for which tags are being generated.
     *
        * @return moodle_page Current Moodle page.
     */
    public function get_page(): moodle_page {
        return $this->page;
    }
}

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
 * Applies resolved meta tags to a Moodle page.
 *
 * @package    local_metatags
 * @copyright  2026 https://santoshmagar.com.np/
 * @author     santoshmagar.com.np
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_metatags;

defined('MOODLE_INTERNAL') || die();

/**
 * Resolves configured tags and renders them into document head markup.
 */
class tag_applier {

    /**
     * Resolve the effective list of meta tags for the given Moodle page,
     * honouring the inheritance chain: activity/module > course > category
     * (all the way up nested categories) > system(global), with page-type
     * specific overrides taking priority over context-wide ones at the same
     * context level.
     *
     * @param \moodle_page $page
     * @return array list of ['attribute'=>, 'tagname'=>, 'content'=>]
     */
    public static function get_effective_tags(\moodle_page $page): array {
        global $DB;

        $isenablemetatags = (bool) get_config('local_metatags', 'enablemetatags');
        if (!$isenablemetatags) {
            return [];
        }

        $pagetype = (string) $page->pagetype;

        // Get current URL path.
        $urlpath = self::get_current_url_path();

        // Build pagetype hierarchy.
        //
        // Example:
        // admin-local-metatags-manage
        //
        // Results:
        // admin-local-metatags-manage
        // admin-local-metatags-*
        // admin-local-*
        // admin-*
        $pagetypes = [$pagetype];

        $parts = explode('-', $pagetype);

        for ($i = count($parts) - 1; $i > 0; $i--) {
            $pagetypes[] = implode('-', array_slice($parts, 0, $i)) . '-*';
        }

        // Global fallback.
        $pagetypes[] = '*';

        // Remove duplicates while preserving order.
        $pagetypes = array_values(array_unique($pagetypes));

        /*
        * Build SQL conditions.
        *
        * We fetch:
        * - All matching pagetypes.
        * - All custom configurations so we can match urlpath below.
        */
        [$insql, $inparams] = $DB->get_in_or_equal(
            $pagetypes,
            SQL_PARAMS_NAMED,
            'pagetype'
        );

        $inparams['custompagetype'] = '__custom__';
        $inparams['customurlpath'] = $urlpath;
        $inparams['status'] = 1;

        $sql = "SELECT *
            FROM {local_metatags_tags}
            WHERE (
                    pagetype $insql
                    OR (
                        pagetype = :custompagetype
                        AND urlpath = :customurlpath
                    )
                )
                AND status = :status
            ORDER BY id ASC";

        $rows = $DB->get_records_sql($sql, $inparams);

        // Index rows by pagetype.
        $rowsbytype = [];
        foreach ($rows as $row) {
            $rowsbytype[$row->pagetype] = $row;
        }

        $selected_row = $rowsbytype['__custom__'] ?? null;

        if ($selected_row === null) {
            foreach ($pagetypes as $type) {
                if (isset($rowsbytype[$type])) {
                    $selected_row = $rowsbytype[$type];
                    break;
                }
            }
        }


        $tags = null;
        if ($selected_row && $selected_row->tags) {
            $tags = json_decode($selected_row->tags);
        }


        $resolved = [];
        if (is_array($tags)) {
            foreach ($tags as $tag) {

                if (is_object($tag)) {
                    $tag = (array) $tag;
                }

                if (!is_array($tag)) {
                    continue;
                }

                $tagname = $tag['tagname'] ?? '';
                $attribute = $tag['attribute'] ?? '';
                $content = $tag['content'] ?? '';

                if (empty($tagname)) {
                    continue;
                }

                $resolved[] = [
                    'tagname' => $tagname,
                    'attribute' => $attribute,
                    'content' => $content,
                ];
            }
        }

        // Allow other plugin or theme to override tags
        $hook = new \local_metatags\hook\override_tags($resolved, $page);
        \core\di::get(\core\hook\manager::class)->dispatch($hook);
        $resolved = $hook->get_tags();

        /*
        * Replace tokens such as:
        *
        * {sitename}
        * {coursename}
        * {fullname}
        */
        return self::replace_tokens_in_all($resolved, $page);
    }

    /**
     * Return the current request path relative to the Moodle installation.
     *
     * @return string Normalized request path.
     */
    private static function get_current_url_path(): string {
        global $CFG;

        $requestpath = parse_url(
            $_SERVER['REQUEST_URI'] ?? '',
            PHP_URL_PATH
        );

        if (!$requestpath) {
            return '/';
        }

        $basepath = parse_url($CFG->wwwroot, PHP_URL_PATH) ?: '';

        if ($basepath !== '' && $basepath !== '/') {
            $basepath = '/' . trim($basepath, '/');

            if (str_starts_with($requestpath, $basepath)) {
                $requestpath = substr($requestpath, strlen($basepath));
            }
        }

        return self::normalize_url_path($requestpath);
    }

    /**
     * Normalize a URL path for custom route matching.
     *
     * @param string $path URL path.
     * @return string Normalized URL path.
     */
    private static function normalize_url_path(string $path): string {
        $path = parse_url($path, PHP_URL_PATH) ?: '/';

        $path = '/' . ltrim($path, '/');

        if ($path !== '/') {
            $path = rtrim($path, '/');
        }

        return $path;
    }


    /**
     * Replace tokens inside every tag content.
     *
     * @param array $flat Tag definitions.
     * @param \moodle_page $page Current Moodle page.
     * @return array Tag definitions with tokens replaced.
     */
    protected static function replace_tokens_in_all(array $flat, \moodle_page $page): array {
        $tokens = self::build_tokens($page);
        foreach ($flat as &$tag) {
            $tag['content'] = strtr($tag['content'], $tokens);
        }
        unset($tag);
        return $flat;
    }

    /**
     * Available tokens.
     *
     * @param \moodle_page $page Moodle page.
     * @return array Available tokens.
     */
    protected static function build_tokens(\moodle_page $page): array {
        global $SITE, $CFG, $DB;

        $tokens = [
            '[sitename]' => format_string($SITE->fullname ?? '', true),
            '[siteurl]'  => (string) new \moodle_url('/'),
            '[pageurl]'  => (string) $page->url,
            '[pagetitle]'  => (string) $page->title,
            '[description]' => '',
            '[coursename]' => '',
            '[courseshortname]' => '',
            '[categoryname]' => '',
            '[activityname]' => '',
            '[userfullname]' => '',
            '[image]' => '',
        ];

        $context = $page->context ?? null;
        if (!$context) {
            return $tokens;
        }

        $descriptionlimit = 225;

        // Course context.
        if ($context->contextlevel == CONTEXT_COURSE && $context->instanceid != SITEID) {
            $course = get_course($context->instanceid);

            $tokens['[coursename]'] = format_string($course->fullname, true, ['context' => $context]);
            $tokens['[courseshortname]'] = format_string($course->shortname, true, ['context' => $context]);
            $description = strip_tags(format_text($course->summary, FORMAT_HTML));
            $description = preg_replace('/\s+/', ' ', $description);
            $tokens['[description]'] = shorten_text(trim($description), $descriptionlimit);

            $image = self::get_course_image_url($course);
            if (empty($image)) {
                $image = self::get_course_category_image_url(
                    \context_coursecat::instance($course->category, IGNORE_MISSING)
                );
            }
            $tokens['[image]'] = $image;
        }

        // Course category context.
        if ($context->contextlevel == CONTEXT_COURSECAT) {
            $category = \core_course_category::get($context->instanceid, IGNORE_MISSING);
            if ($category) {
                $tokens['[categoryname]'] = format_string($category->name, true, ['context' => $context]);
                $tokens['[image]'] = self::get_course_category_image_url($context);
                $description = strip_tags(format_text($category->description, FORMAT_HTML));
                $description = preg_replace('/\s+/', ' ', $description);
                $tokens['[description]'] = shorten_text(trim($description), $descriptionlimit);
            }
        }

        // User context.
        if ($context->contextlevel == CONTEXT_USER) {
            $user = \core_user::get_user($context->instanceid, '*', IGNORE_MISSING);
            if ($user) {
                $tokens['[userfullname]'] = fullname($user);
                $description = strip_tags(format_text($user->description, FORMAT_HTML));
                $description = preg_replace('/\s+/', ' ', $description);
                $tokens['[description]'] = shorten_text(trim($description), $descriptionlimit);
            }
        }

        // Activity/module context.
        if ($context->contextlevel == CONTEXT_MODULE && $page->cm) {
            $tokens['[activityname]'] = format_string($page->cm->name, true, ['context' => $context]);
            $course = get_course($page->cm->course);
            $image = self::get_activity_image_url($context, $page->cm);
            if (empty($image)) {
                $image = self::get_course_image_url($course);
            }
            if (empty($image)) {
                $image = self::get_course_category_image_url(
                    \context_coursecat::instance($course->category, IGNORE_MISSING)
                );
            }
            $tokens['[image]'] = $image;

            /*
            * Get the actual activity instance.
            *
            * cm->modname gives us the module type and cm->instance
            * gives us the activity instance ID.
            */
            $modname = $page->cm->modname;
            $instanceid = $page->cm->instance;

            if ($modname && $instanceid) {
                $module = $DB->get_record($modname, ['id' => $instanceid], '*', IGNORE_MISSING);
                if ($module) {
                    /*
                    * Most Moodle activities use:
                    *   intro
                    *   introformat
                    *
                    * for their description.
                    */
                    if (isset($module->intro)) {
                        $description = strip_tags(format_text($module->intro, FORMAT_HTML));
                        $description = preg_replace('/\s+/', ' ', $description);
                        $tokens['[description]'] = shorten_text(trim($description), $descriptionlimit);
                    }
                }
            }
        }

        if (empty($tokens['[description]'])) {
            $tokens['[description]'] = s(strip_tags(format_text($SITE->summary, FORMAT_HTML)));
        }

        return $tokens;
    }

    /**
     * Return the course image URL when one exists.
     *
     * @param \stdClass $course Course record.
     * @return string Image URL or an empty string.
     */
    private static function get_course_image_url(\stdClass $course): string {
        global $CFG;

        require_once($CFG->dirroot . '/course/lib.php');
        $courseimage = course_get_courseimage($course);
        if ($courseimage) {
            return self::get_file_url($courseimage);
        }
        return '';
    }

    /**
     * Return the first valid image in a category description.
     *
     * @param \context_coursecat|null $context Category context.
     * @return string Image URL or an empty string.
     */
    private static function get_course_category_image_url($context): string {
        if (!$context) {
            return '';
        }

        $files = get_file_storage()->get_area_files(
            $context->id,
            'coursecat',
            'description',
            0,
            'filename',
            false
        );
        foreach ($files as $file) {
            if ($file->is_valid_image()) {
                return self::get_file_url($file);
            }
        }
        return '';
    }

    /**
     * Return the first valid image in an activity introduction.
     *
     * @param \context_module $context Activity context.
     * @param \cm_info $cm Course-module information.
     * @return string Image URL or an empty string.
     */
    private static function get_activity_image_url($context, \cm_info $cm): string {
        $files = get_file_storage()->get_area_files(
            $context->id,
            'mod_' . $cm->modname,
            'intro',
            0,
            'filename',
            false
        );
        foreach ($files as $file) {
            if ($file->is_valid_image()) {
                return self::get_file_url($file);
            }
        }
        return '';
    }

    /**
     * Build a public URL for a stored Moodle file.
     *
     * @param \stored_file $file Stored file.
     * @return string Public file URL.
     */
    private static function get_file_url(\stored_file $file): string {
        return \moodle_url::make_pluginfile_url(
            $file->get_contextid(),
            $file->get_component(),
            $file->get_filearea(),
            $file->get_itemid() ?: null,
            $file->get_filepath(),
            $file->get_filename()
        )->out(false);
    }

    /**
     * Convert resolved tags into HTML ready for the document <head>.
     *
     * Important: on the front page we deliberately skip name="description"
     * because Moodle core already outputs one → no duplicates.
     *
     * @param array $tags Resolved tag definitions.
     * @param \moodle_page $page Current Moodle page.
     * @return string HTML head markup.
     */
    public static function apply_tags(array $tags, \moodle_page $page): string {
        global $SITE;
        $isfrontpage = ($page->pagelayout === 'frontpage');
        $html = '';

        foreach ($tags as $tag) {
            $content   = trim((string) $tag['content']);
            $tagname   = $tag['tagname'];
            $attribute = $tag['attribute'];

            if ($content === '') {
                continue;
            }

            // -------------------------------------------------
            // Prevent duplicate description on the front page
            // -------------------------------------------------
            if ($isfrontpage && $attribute === 'name' && $tagname === 'description') {
                $summary = s(strip_tags(format_text($SITE->summary, FORMAT_HTML)));
                if (!empty($summary) &&  $summary == $content) {
                    continue;
                }
            }

            // -------------------------------------------------
            // Make image URLs absolute when needed
            // -------------------------------------------------
            if (in_array($tagname, [
                'canonical',
                'image',
                'og:image',
                'og:image:secure_url',
                'twitter:image',
            ], true)) {
                if ($content !== '' && strpos($content, '://') === false && strpos($content, '//') !== 0) {
                    $content = (new \moodle_url($content))->out(false);
                }
            }

            // -------------------------------------------------
            // Output
            // -------------------------------------------------
            if ($attribute === 'link') {
                $html .= \html_writer::empty_tag('link', [
                    'rel'  => $tagname,
                    'href' => $content,
                ]) . "\n";
                continue;
            }

            $html .= \html_writer::empty_tag('meta', [
                $attribute => $tagname,
                'content'  => $content,
            ]) . "\n";
        }

        return $html;
    }
}

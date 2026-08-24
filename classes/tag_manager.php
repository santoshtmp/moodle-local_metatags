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
 * Tag manager for local_metatags.
 *
 * @package   local_metatags
 * @copyright 2026 https://santoshmagar.com.np/
 * @author    santoshmagar.com.np
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_metatags;

use stdClass;

/**
 * Core logic for local_metatags definitions and storage.
 */
class tag_manager {
    /**
     * Database table name for storing meta tags.
     *
     * @var string
     */
    public const TABLENAME = 'local_metatags_tags';

    /**
     * Catalogue of well-known tags the UI offers as dedicated fields.
     * Admins can still add arbitrary custom tags beyond this list.
     *
     * @return array tagname => [attribute, label, default, group]
     */
    public static function get_definitions(): array {
        return [
            'description'            => [
                'attribute' => 'name',
                'label' => 'tag_description',
                'default' => '[description]',
                'group' => 'basic',
            ],
            'keywords'               => [
                'attribute' => 'name',
                'label' => 'tag_keywords',
                'default' => '',
                'group' => 'basic',
            ],
            'author'                 => [
                'attribute' => 'name',
                'label' => 'tag_author',
                'default' => '[sitename]',
                'group' => 'basic',
            ],
            'robots'                 => [
                'attribute' => 'name',
                'label' => 'tag_robots',
                'default' => '',
                'group' => 'basic',
            ],
            'canonical'              => [
                'attribute' => 'link',
                'label' => 'tag_canonical',
                'default' => '',
                'group' => 'basic',
            ],
            'image'                  => [
                'attribute' => 'name',
                'label' => 'tag_image',
                'default' => '',
                'group' => 'basic',
            ],

            'og:title'                => [
                'attribute' => 'property',
                'label' => 'tag_ogtitle',
                'default' => '[pagetitle]',
                'group' => 'opengraph',
            ],
            'og:description'         => [
                'attribute' => 'property',
                'label' => 'tag_ogdescription',
                'default' => '[description]',
                'group' => 'opengraph',
            ],
            'og:type'                 => [
                'attribute' => 'property',
                'label' => 'tag_ogtype',
                'default' => 'website',
                'group' => 'opengraph',
            ],
            'og:image'                => [
                'attribute' => 'property',
                'label' => 'tag_ogimage',
                'default' => false,
                'group' => 'opengraph',
            ],
            'og:url'                  => [
                'attribute' => 'property',
                'label' => 'tag_ogurl',
                'default' => '[pageurl]',
                'group' => 'opengraph',
            ],
            'og:site_name'           => [
                'attribute' => 'property',
                'label' => 'tag_ogsitename',
                'default' => '[sitename]',
                'group' => 'opengraph',
            ],
            'og:locale'              => [
                'attribute' => 'property',
                'label' => 'tag_oglocale',
                'default' => '',
                'group' => 'opengraph',
            ],
            'og:image:secure_url'    => [
                'attribute' => 'property',
                'label' => 'tag_ogimagesecureurl',
                'default' => '',
                'group' => 'opengraph',
            ],
            'og:image:type'          => [
                'attribute' => 'property',
                'label' => 'tag_ogimagetype',
                'default' => '',
                'group' => 'opengraph',
            ],
            'og:image:width'         => [
                'attribute' => 'property',
                'label' => 'tag_ogimagewidth',
                'default' => '',
                'group' => 'opengraph',
            ],
            'og:image:height'        => [
                'attribute' => 'property',
                'label' => 'tag_ogimageheight',
                'default' => '',
                'group' => 'opengraph',
            ],
            'og:image:alt'           => [
                'attribute' => 'property',
                'label' => 'tag_ogimagealt',
                'default' => '',
                'group' => 'opengraph',
            ],

            'twitter:card'            => [
                'attribute' => 'name',
                'label' => 'tag_twittercard',
                'default' => '',
                'group' => 'twitter',
            ],
            'twitter:title'           => [
                'attribute' => 'name',
                'label' => 'tag_twittertitle',
                'default' => '[pagetitle]',
                'group' => 'twitter',
            ],
            'twitter:description'    => [
                'attribute' => 'name',
                'label' => 'tag_twitterdescription',
                'default' => '[description]',
                'group' => 'twitter',
            ],
            'twitter:image'           => [
                'attribute' => 'name',
                'label' => 'tag_twitterimage',
                'default' => '',
                'group' => 'twitter',
            ],
            'twitter:image:alt'       => [
                'attribute' => 'name',
                'label' => 'tag_twitterimagealt',
                'default' => '',
                'group' => 'twitter',
            ],
            'twitter:site'            => [
                'attribute' => 'name',
                'label' => 'tag_twittersite',
                'default' => '',
                'group' => 'twitter',
            ],
            'twitter:creator'         => [
                'attribute' => 'name',
                'label' => 'tag_twittercreator',
                'default' => '',
                'group' => 'twitter',
            ],
        ];
    }

    /**
     * Group keys in display order, mapped to their section heading string.
     *
     * @return array group key => lang string identifier
     */
    public static function get_definition_groups(): array {
        return [
            'basic'     => 'grouptitle_basic',
            'opengraph' => 'grouptitle_opengraph',
            'twitter'   => 'grouptitle_twitter',
        ];
    }

    /**
     * Return the form field name for a tag.
     *
     * @param string $tagname Meta tag name.
     * @return string Form field name.
     */
    public static function get_tag_fieldname(string $tagname): string {
        return 'tag_' . preg_replace('/[^a-z0-9]/i', '_', $tagname);
    }

    /**
     * Build a grouped list of page-type options for the form.
     *
     * @return array group label => [pagetype => label]
     */
    public static function get_pagetype_options(): array {
        $groups = [];

        $groups[get_string('pagetypegroup_general', 'local_metatags')] = [
            '*' => get_string('pagetype_all', 'local_metatags'),
        ];

        $groups[get_string('pagetypegroup_site', 'local_metatags')] = [
            'site-index'  => get_string('pt_site_index', 'local_metatags'),
            'login-index' => get_string('pt_login_index', 'local_metatags'),
            'my-index'    => get_string('pt_my_index', 'local_metatags'),
        ];

        // Course layout + "all activities of type X in this course/category/site" wildcards.
        $groups[get_string('pagetypegroup_course', 'local_metatags')] = [
            'course-view-*'               => get_string('pt_course_view_any', 'local_metatags'),
        ];

        $groups[get_string('pagetypegroup_activitytypes', 'local_metatags')] = [
            'mod-*'           => get_string('pt_mod_any', 'local_metatags'),
            'mod-page-view'       => get_string('pt_mod_page_view', 'local_metatags'),
            'mod-quiz-*'           => get_string('pt_mod_quiz_any', 'local_metatags'),
            'mod-forum-*'          => get_string('pt_mod_forum_any', 'local_metatags'),
        ];

        $groups[get_string('pagetypegroup_blog', 'local_metatags')] = [
            'blog-index' => get_string('pt_blog_index', 'local_metatags'),
            'blog-view'  => get_string('pt_blog_view', 'local_metatags'),
        ];

        $groups[get_string('pagetypegroup_user', 'local_metatags')] = [
            'user-profile' => get_string('pt_user_profile', 'local_metatags'),
            'user-view'    => get_string('pt_user_view', 'local_metatags'),
        ];

        $groups[get_string('pagetypegroup_custom', 'local_metatags')] = [
            '__custom__' => get_string('pt_custom', 'local_metatags'),
        ];

        return $groups;
    }

    /**
     * Attribute to use in the rendered HTML for a given tagname, falling
     * back sensibly for custom tags.
     *
     * @param string $tagname Meta tag name.
     * @return string HTML attribute name.
     */
    public static function attribute_for(string $tagname): string {
        $defs = self::get_definitions();
        if (isset($defs[$tagname])) {
            return $defs[$tagname]['attribute'];
        }
        if ($tagname === 'canonical') {
            return 'link';
        }
        // Heuristic: og: and fb: namespaced tags use "property", everything else "name".
        if (strpos($tagname, 'og:') === 0 || strpos($tagname, 'fb:') === 0 || strpos($tagname, 'article:') === 0) {
            return 'property';
        }
        return 'name';
    }

    /**
     * Validate a meta tag name.
     *
     * @param string $tagname Meta tag name.
     * @return bool True when valid.
     */
    public static function is_valid_tagname(string $tagname): bool {
        return preg_match(
            '/^[a-zA-Z0-9:_-]+$/',
            $tagname
        ) === 1;
    }

    /**
     * Check whether the current user can manage the plugin.
     *
     * @param \context $context Context in which to check the capability.
     * @return bool Whether the user can manage the plugin.
     */
    public static function can_manage(\context $context): bool {
        return has_capability('local/metatags:manage', $context);
    }

    /**
     * Save or update a tag configuration.
     *
     * @param stdClass $mformdata Form data object containing tag details.
     * @param string $returnurl URL to redirect after saving.
     * @param string $updatereturnurl URL to redirect after updating an existing record.
     * @return void Redirects to the specified URL after operation.
     * @throws \coding_exception
     */
    public static function save_data($mformdata, $returnurl, $updatereturnurl) {
        $message = '';
        try {
            global $DB;

            $pagetype = (string) ($mformdata->pagetype ?? '');
            $customurlpath = trim((string) ($mformdata->custom_urlpath ?? ''));
            if ($pagetype === '__custom__') {
                $customurlpath = '/' . trim($customurlpath, '/');
                if ($customurlpath === '/') {
                    $customurlpath = '';
                }
            } else {
                $customurlpath = '';
            }

            // Known meta tag definitions.
            $tags = [];
            $definitions = self::get_definitions();
            foreach ($definitions as $tagname => $definition) {
                $fieldname = self::get_tag_fieldname($tagname);
                if (isset($mformdata->$fieldname) && !empty(trim((string) $mformdata->$fieldname))) {
                    $tags[] = [
                        'tagname' => $tagname,
                        'attribute' => $definition['attribute'],
                        'content' => trim((string) $mformdata->$fieldname),
                    ];
                }
            }
            $transaction = $DB->start_delegated_transaction();

            $now = time();

            $record = new \stdClass();
            $record->id = isset($mformdata->id) ? $mformdata->id : 0;
            $record->pagetype = $pagetype;
            $record->urlpath = $customurlpath;
            $record->tags = json_encode($tags);
            $record->status = !empty($mformdata->status) ? 1 : 0;
            $record->timemodified = $now;

            // Insert or update.
            if (!empty($record->id) && ($mformdata->action ?? '') === 'edit') {
                if ($DB->record_exists(self::TABLENAME, ['id' => $record->id])) {
                    $status = $DB->update_record(self::TABLENAME, $record);
                    if ($status) {
                        $message = get_string('updated', 'local_metatags');
                    }
                    $returnurl = $updatereturnurl;
                } else {
                    $message = get_string('update_id_missing', 'local_metatags');
                }
            } else {
                $record->timecreated = $now;
                $status = $DB->insert_record(self::TABLENAME, $record);
                if ($status) {
                    $message = get_string('saved', 'local_metatags');
                    $returnurl = $updatereturnurl;
                }
            }
            $transaction->allow_commit();
        } catch (\Throwable $th) {
            $message = get_string('error_submit', 'local_metatags');
            $message .= "\n :: " . $th->getMessage();
        }

        redirect($returnurl, $message);
    }

    /**
     * Load tag configuration data into a form for editing.
     *
     * @param \moodleform $mform Moodle form instance.
     * @param int $id Tag configuration ID to edit.
     * @param string $returnurl URL to redirect in case of error.
     * @return \moodleform The form instance with data prefilled.
     */
    public static function edit_form($mform, $id, $returnurl) {
        try {
            global $DB;
            if (!$id) {
                return $mform;
            }
            $data = $DB->get_record(self::TABLENAME, ['id' => $id]);
            if ($data) {
                $entry = new stdClass();
                $entry->id = $id;
                $entry->action = 'edit';
                $entry->pagetype = $data->pagetype;
                $entry->custom_urlpath = $data->urlpath;
                $entry->status = $data->status;

                $tags = json_decode($data->tags);
                if ($tags && is_array($tags)) {
                    foreach ($tags as $key => $tag) {
                        if (is_object($tag)) {
                            $tag = (array)$tag;
                        }
                        if (!is_array($tag)) {
                            continue;
                        }
                        if (!empty($tag['tagname']) && !empty($tag['content'])) {
                            $fieldname = self::get_tag_fieldname($tag['tagname']);
                            $entry->$fieldname = $tag['content'];
                        }
                    }
                }

                $mform->set_data($entry);
                return $mform;
            } else {
                $message = get_string('data_missing', 'local_metatags');
            }
        } catch (\Throwable $th) {
            $message = get_string('data_missing', 'local_metatags');
            $message .= $th->getMessage();
        }
        redirect($returnurl, $message);
    }

    /**
     * Delete a tag configuration.
     *
     * @param int $id Menu entry ID to delete.
     * @param string $returnurl URL to redirect after deletion.
     * @return void Redirects to the specified URL after operation.
     */
    public static function delete_data($id, $returnurl) {
        try {
            global $DB;
            if (!$id) {
                $message = get_string('delete_missing', 'local_metatags');
                redirect($returnurl, $message);
            }
            $data = $DB->get_record(self::TABLENAME, ['id' => $id]);
            if ($data) {
                $delete = $DB->delete_records(self::TABLENAME, ['id' => $data->id]);
                if ($delete) {
                    $message = get_string('deleted', 'local_metatags');
                } else {
                    $message = get_string('error_delete', 'local_metatags');
                }
            } else {
                $message = get_string('delete_missing', 'local_metatags');
            }
        } catch (\Throwable $th) {
            $message = get_string('error_delete', 'local_metatags');
            $message .= "\n" . $th->getMessage();
        }

        redirect($returnurl, $message);
    }
}

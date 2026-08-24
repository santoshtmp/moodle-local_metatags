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
 * Meta tags form.
 *
 * @package    local_metatags
 * @copyright  2026 https://santoshmagar.com.np/
 * @author     santoshmagar.com.np
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_metatags\form;

use local_metatags\tag_manager;
use moodleform;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/formslib.php');
/**
 * Form for creating and editing page-type tag configurations.
 */
class tags_form extends moodleform {
    /**
     * Define the form fields.
     *
     * @return void
     */
    public function definition() {
        $mform = $this->_form;
        $customdata = $this->_customdata;

        // Page type options.
        $pagetypeoptions = tag_manager::get_pagetype_options();

        // Keep the selected Moodle scope when the form is submitted.
        $mform->addElement('header', 'hdrtargeting', get_string('targeting', 'local_metatags'));
        $mform->setExpanded('hdrtargeting', true);
        $mform->addElement('static', 'targetinghelp', '', get_string('targeting_intro', 'local_metatags'));
        $mform->addElement('selectgroups', 'pagetype', get_string('pagetype_target', 'local_metatags'), $pagetypeoptions);
        $mform->addRule('pagetype', null, 'required', null, 'client');
        $mform->setType('pagetype', PARAM_TEXT);
        $mform->addHelpButton('pagetype', 'pagetype', 'local_metatags');

        $mform->addElement('text', 'custom_urlpath', get_string('pt_custom_value', 'local_metatags'), ['size' => 40]);
        $mform->setType('custom_urlpath', PARAM_TEXT);
        $mform->hideIf('custom_urlpath', 'pagetype', 'neq', '__custom__');

        $mform->addElement(
            'advcheckbox',
            'status',
            get_string('status', 'local_metatags'),
            get_string('status_enabled', 'local_metatags')
        );
        $mform->addHelpButton('status', 'status', 'local_metatags');
        $mform->setDefault('status', 1);
        $mform->setType('status', PARAM_BOOL);

        $definitions = \local_metatags\tag_manager::get_definitions();
        $groups = \local_metatags\tag_manager::get_definition_groups();

        foreach ($groups as $groupkey => $grouptitlestring) {
            $mform->addElement('header', 'hdr_' . $groupkey, get_string($grouptitlestring, 'local_metatags'));
            $mform->setExpanded('hdr_' . $groupkey, $groupkey === 'basic');

            foreach ($definitions as $tagname => $def) {
                if ($def['group'] !== $groupkey) {
                    continue;
                }
                $fieldname = tag_manager::get_tag_fieldname($tagname);
                if (in_array($def['label'], ['tag_description', 'tag_ogdescription', 'tag_twitterdescription'], true)) {
                    $mform->addElement('textarea', $fieldname, get_string($def['label'], 'local_metatags'), [
                        'rows' => 2,
                        'cols' => 60,
                    ]);
                } else {
                    $mform->addElement('text', $fieldname, get_string($def['label'], 'local_metatags'), ['size' => 60]);
                }
                $mform->setType($fieldname, PARAM_RAW_TRIMMED);
                if (get_string_manager()->string_exists($def['label'] . '_help', 'local_metatags')) {
                    $mform->addHelpButton($fieldname, $def['label'], 'local_metatags');
                }
                if (!empty($def['default'])) {
                    $mform->setDefault($fieldname, $def['default']);
                }
            }
        }

        // Hidden id field.
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        // Hidden action field.
        $mform->addElement('hidden', 'action');
        $mform->setType('action', PARAM_TEXT);

        $this->add_action_buttons(true, get_string('save', 'local_metatags'));
    }

    /**
     * Validate form data.
     *
     * Checks for duplicate page-type/path combinations and validates custom
     * URL paths.
     *
     * @param array $data Submitted form data.
     * @param array $files Uploaded files (not used).
     * @return array Array of validation errors, empty if no errors.
     */
    public function validation($data, $files) {
        global $DB;

        $errors = parent::validation($data, $files);

        if (empty($data['pagetype'])) {
            $errors['pagetype'] = get_string('required');
        }

        $pagetype = (string) ($data['pagetype'] ?? '');
        $customurlpath = trim((string) ($data['custom_urlpath'] ?? ''));
        if ($pagetype === '__custom__') {
            if ($customurlpath === '') {
                $errors['custom_urlpath'] = get_string('required');
            } else if ($customurlpath[0] !== '/') {
                $errors['custom_urlpath'] = get_string('custom_url_invalid', 'local_metatags');
            } else {
                $customurlpath = '/' . trim($customurlpath, '/');
                if ($customurlpath === '/') {
                    $errors['custom_urlpath'] = get_string('custom_url_invalid', 'local_metatags');
                }
            }
        } else {
            $customurlpath = '';
        }

        $existing = $DB->get_record(
            'local_metatags_tags',
            ['pagetype' => $pagetype, 'urlpath' => $customurlpath]
        );
        if ($existing) {
            if ($existing->id != ($data['id'] ?? 0)) {
                if ($pagetype === '__custom__') {
                    $errors['custom_urlpath'] = get_string('alradyexist', 'local_metatags');
                } else {
                    $errors['pagetype'] = get_string('alradyexist', 'local_metatags');
                }
            }
        }

        return $errors;
    }
}

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
 * Meta tag management page for local_metatags.
 *
 * @package    local_metatags
 * @copyright  2026 https://santoshmagar.com.np/
 * @author     santoshmagar.com.np
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

require_login();

$context = context_system::instance();

require_capability('local/metatags:manage', $context);

$PAGE->set_context($context);
$pageurl = new moodle_url('/local/metatags/manage.php');
$PAGE->set_url($pageurl);

admin_externalpage_setup('local_metatags_manage');

global $DB;

// Quick inline toggle for the automatic/dynamic tags behaviour.
$toggledynamic = optional_param('toggledynamic', null, PARAM_BOOL);

if ($toggledynamic !== null) {
    require_sesskey();
    set_config('enablemetatags', $toggledynamic ? 1 : 0, 'local_metatags');
    redirect($PAGE->url);
}

/*
 * Group existing rows by page type.
 *
 * An empty pagetype represents a global/default configuration.
 */
$buckets = $DB->get_records(
    'local_metatags_tags',
    [],
    'timemodified DESC, id DESC'
);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'local_metatags'));
echo html_writer::tag('p', get_string('manage_intro', 'local_metatags'));

/*
 * Action buttons.
 */
echo html_writer::start_div('mb-4');

$addurl = new moodle_url('/local/metatags/edit.php');

echo html_writer::link(
    $addurl,
    get_string('addmetatag', 'local_metatags'),
    [
        'class' => 'btn btn-primary mr-2',
    ]
);

/*
 * Dynamic tags toggle.
 */
$isenablemetatags = (bool) get_config('local_metatags', 'enablemetatags');
$toggleurl = new moodle_url('/local/metatags/manage.php', [
    'toggledynamic' => $isenablemetatags ? 0 : 1,
    'sesskey'       => sesskey(),
]);
$togglelabel = $isenablemetatags
    ? get_string('enablemetatags', 'local_metatags') . ' (' . get_string('yes') . ')'
    : get_string('enablemetatags', 'local_metatags') . ' (' . get_string('no') . ')';

echo html_writer::link(
    $toggleurl,
    $togglelabel,
    [
        'class' => 'btn ' .
            ($isenablemetatags
                ? 'btn-outline-success'
                : 'btn-outline-secondary'),
        'title' => get_string(
            'enablemetatags_desc',
            'local_metatags'
        ),
    ]
);

echo html_writer::end_div();

/*
 * No metadata configured.
 */
if (empty($buckets)) {
    echo $OUTPUT->notification(get_string('manage_none', 'local_metatags'), \core\output\notification::NOTIFY_INFO);
} else {
    $table = new html_table();

    $table->head = [
        get_string('manage_col_pagetype', 'local_metatags'),
        get_string('status', 'moodle'),
        get_string('action', 'moodle'),
    ];

    $table->data = [];
    $table->responsive = false;

    $corerenderer = $PAGE->get_renderer('core');
    $actionpagepath = '/local/metatags/edit.php';
    foreach ($buckets as $bucket) {
        $pagetype = $bucket->pagetype;

        // Action menu.
        $actionmenu = new action_menu();
        $actionmenu->set_kebab_trigger('Action', $corerenderer);
        $actionmenu->set_additional_classes('fields-actions');
        $actionurlparam = ['id' => $bucket->id, 'sesskey' => sesskey()];

        $actionmenu->add(new \action_menu_link(
            new moodle_url($actionpagepath, ['action' => 'edit'] + $actionurlparam),
            new pix_icon('i/edit', 'edit'),
            get_string('edit', 'local_metatags'),
            false,
            ['data-id' => $bucket->id]
        ));

        $actionmenu->add(new \action_menu_link(
            new moodle_url($actionpagepath, ['action' => 'delete'] + $actionurlparam),
            new pix_icon('i/delete', 'delete'),
            get_string('delete', 'local_metatags'),
            false,
            [
                'class' => 'text-danger delete-action',
                'data-id' => $bucket->id,
                'data-title' => format_string($pagetype),
                'data-heading' => get_string('delete_confirm_heading', 'local_metatags'),
            ]
        ));


        /*
         * Empty page type means global/default metadata.
         */

        if ($pagetype == '*') {
            $langkey = 'pagetype_all';
        } elseif ($pagetype == '__custom__') {
            $langkey = 'pt_custom';
        } else {
            $langkey = str_replace('-', '_', $pagetype);
            $langkey = 'pt_' . str_replace('*', 'any', $langkey);
        }

        $pagetypelabel = get_string($langkey, 'local_metatags') . ($bucket->urlpath) ? ' : ' . $bucket->urlpath : '';

        $table->data[] = [
            $pagetypelabel,
            ($bucket->status == '1') ? get_string('active') : get_string('inactive'),
            $corerenderer->render($actionmenu),
        ];
    }

    echo html_writer::table($table);
}

echo $OUTPUT->footer();

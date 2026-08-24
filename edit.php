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

/**
 * Edit and create local metatags entries.
 *
 * @package    local_metatags
 * @copyright  2026 https://santoshmagar.com.np/
 * @author     santoshmagar.com.np
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_metatags\tag_manager;

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

require_login();

$context = context_system::instance();

require_capability('local/metatags:manage', $context);

$action = optional_param('action', '', PARAM_ALPHA);
$id = optional_param('id', 0, PARAM_INT);

$PAGE->set_context($context);

/**
 * Set Page Information
 */
$url = new moodle_url('/local/metatags/edit.php');
$tagmanagelisturl = new moodle_url('/local/metatags/manage.php');
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');

$pagetitle = get_string('editingtags', 'local_metatags');

$PAGE->set_title($pagetitle);
$PAGE->set_heading($pagetitle);

$PAGE->navbar->add(
    get_string('pluginname', 'local_metatags'),
    new moodle_url('/local/metatags/manage.php')
);

$PAGE->navbar->add($pagetitle);

global $DB;



$customdata = [];

/*
 * Create form.
 */
$form = new \local_metatags\form\tags_form(null, $customdata);

// Handle delete/reset action.
// Deleting a bucket simply removes the saved configuration.
if ($action && $id) {
    // Verify sesskey.
    $sesskey = required_param('sesskey', PARAM_ALPHANUM);
    if ($sesskey != sesskey()) {
        redirect($tagmanagelisturl, get_string('invalidsesskey', 'local_metatags'));
    }
    // For delete.
    if ($action == 'delete') {
        require_sesskey();

        $confirm = optional_param('confirm', 0, PARAM_INT);
        if ($confirm) {
            tag_manager::delete_data($id, $tagmanagelisturl);
        }

        echo $OUTPUT->header();

        $confirmurl = new moodle_url(
            $PAGE->url,
            [
                'action' => 'delete',
                'id' => $id,
                'confirm' => 1,
                'sesskey' => sesskey(),
            ]
        );
        $cancelurl = new moodle_url('/local/metatags/manage.php');

        echo $OUTPUT->confirm(
            get_string('resetconfirm', 'local_metatags'),
            $confirmurl,
            $cancelurl
        );

        echo $OUTPUT->footer();
        exit;
    }
    // For edit.
    if ($action == 'edit') {
        tag_manager::edit_form($form, $id, $tagmanagelisturl);
    }
}

// Cancel.
if ($form->is_cancelled()) {
    redirect(
        new moodle_url('/local/metatags/manage.php')
    );
}

// Save.
if ($formdata = $form->get_data()) {
    tag_manager::save_data($formdata, $url, $tagmanagelisturl);
}

// Output page.
echo $OUTPUT->header();

echo $OUTPUT->heading(
    get_string('editingtags', 'local_metatags')
);

echo $OUTPUT->notification(
    get_string('tokenhelp_desc', 'local_metatags'),
    \core\output\notification::NOTIFY_INFO,
    false
);

$form->display();

echo $OUTPUT->footer();

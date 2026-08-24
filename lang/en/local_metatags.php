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
 * Language strings for local_metatags.
 *
 * @package    local_metatags
 * @copyright  2026 https://santoshmagar.com.np/
 * @author     santoshmagar.com.np
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// phpcs:ignoreFile moodle.Files.LangFilesOrdering.IncorrectOrder
defined('MOODLE_INTERNAL') || die();

$string['pluginname']          = 'Meta tags';
$string['metatags:manage']         = 'Manage global meta tags and all overrides';
$string['metatags:managecategory'] = 'Manage meta tags for a course category';
$string['metatags:managecourse']   = 'Manage meta tags for a course';
$string['metatags:managemodule']   = 'Manage meta tags for an activity';
$string['metatags:manageuser']     = 'Manage meta tags for a user profile';

$string['alreadyexist']       = 'Already exists';
$string['addmetatag']        = 'Add meta tag';
$string['enablemetatags']    = 'Enable Meta tags';
$string['enablemetatags_desc'] = 'Enable automatic meta tags for Moodle pages.';

$string['tokenhelp']           = 'Available tokens';
$string['tokenhelp_desc'] = '
<p>You can use the following placeholders inside any tag content. They will be automatically replaced with the corresponding value for the current page:</p>

<ul>
    <li><strong>[sitename]</strong> — The full name of the Moodle site.</li>
    <li><strong>[siteurl]</strong> — The base URL of the Moodle site.</li>
    <li><strong>[pageurl]</strong> — The full URL of the current page.</li>
    <li><strong>[pagetitle]</strong> — The title of the current page.</li>
    <li><strong>[description]</strong> — The most relevant description for the current context, such as the course summary, category description, activity description, or site description.</li>
    <li><strong>[coursename]</strong> — The full name of the current course.</li>
    <li><strong>[courseshortname]</strong> — The short name of the current course.</li>
    <li><strong>[categoryname]</strong> — The name of the current course category.</li>
    <li><strong>[activityname]</strong> — The name of the current activity or resource.</li>
    <li><strong>[userfullname]</strong> — The full name of the current user, when the page has a user context.</li>
    <li><strong>[image]</strong> — The most relevant available image URL: course image, or course category description image or activity description image.</li>
</ul>

<p><strong>Example:</strong></p>

<pre>Welcome to [sitename] — [coursename]</pre>

<p>This could be automatically replaced with:</p>

<pre>Welcome to My Moodle Site — Introduction to Programming</pre>

<p>The <strong>[description]</strong> placeholder automatically adapts to the current page context, making it particularly useful for generating dynamic SEO meta descriptions.</p>

<p>Another plugin or theme may also inject/manage the same meta tags. Check for existing tags before enabling duplicate fields, especially canonical, description, OpenGraph, and Twitter tags.</p>
';

$string['editingtags']         = 'Editing meta tags';
$string['pagetype']            = 'Page type / layout';
$string['pagetype_target']     = 'Apply only to';
$string['pagetype_help']       = 'Choose "Global scope" to apply these tags everywhere under this context, pick one of the suggested page types/layouts, or choose "Custom / other" to type an exact Moodle page type (e.g. course-view-topics) or a wildcard pattern using * (e.g. blog-*, mod-forum-*, mod-quiz-*). Wildcard and exact matches are always overridden by anything more specific defined at the same or a more specific context.';
$string['targeting']           = 'Where these tags apply';
$string['targeting_intro']     = 'Choose the Moodle page type or layout that should receive these tags. Select “Global scope” to apply them everywhere in the selected scope.';
$string['status']              = 'Status';
$string['status_enabled']      = 'Enabled';
$string['status_help']         = 'Enable this configuration to output its meta tags on matching pages. Clear this checkbox to keep the configuration saved but inactive.';
$string['pagetype_all']        = 'Global scope';
$string['pt_custom']           = 'Custom URL route';
$string['pt_custom_value']     = 'Enter custom URL route';
$string['custom_url_invalid']  = 'Enter a valid custom URL path beginning with /.';

$string['pagetypegroup_general']       = 'General';
$string['pagetypegroup_course']        = 'Course layouts';
$string['pagetypegroup_activitytypes'] = 'Course modules (Activity types)';
$string['pagetypegroup_site']          = 'Site pages';
$string['pagetypegroup_blog']          = 'Blog pages';
$string['pagetypegroup_user']          = 'User profile pages';
$string['pagetypegroup_custom']        = 'Custom';

$string['pt_course_view_any']              = 'Course page (any type)';

$string['pt_mod_any']         = 'Any activity';
$string['pt_mod_quiz_any']         = 'Quiz - any page';
$string['pt_mod_page_view']        = 'Page view';
$string['pt_mod_resource_view']    = 'File/Resource activity';
$string['pt_mod_hvp_view']         = 'H5P (hvp) activity';
$string['pt_mod_h5pactivity_view'] = 'H5P (core h5pactivity) activity';
$string['pt_mod_forum_any']        = 'Forum - any page';
$string['pt_mod_x_any']            = '{$a} - any page';
$string['pt_mod_x_view']           = '{$a} - view page';

$string['pt_site_index']    = 'Site front page';
$string['pt_login_index']   = 'Login page';
$string['pt_my_index']      = 'Dashboard (My home)';
$string['pt_blog_index']    = 'Blog listing';
$string['pt_blog_view']     = 'Single blog post';
$string['pt_user_profile']  = 'User profile page';
$string['pt_user_view']     = 'User course profile page';

$string['grouptitle_basic']    = 'Basic tags';
$string['grouptitle_opengraph'] = 'OpenGraph tags (Facebook, LinkedIn, etc.)';
$string['grouptitle_twitter']  = 'Twitter Card tags';

$string['save']                = 'Save changes';
$string['saved']               = 'Meta tags saved';
$string['updated']             = 'Meta tags updated';
$string['deleted']             = 'Meta tags deleted';
$string['edit']                = 'Edit';
$string['delete']              = 'Delete';
$string['delete_confirm_heading'] = 'Delete meta tag configuration?';
$string['invalidsesskey']      = 'Your session key is missing or invalid.';
$string['error_submit']        = 'There was an error saving the meta tag configuration.';
$string['error_delete']        = 'There was an error deleting the meta tag configuration.';
$string['delete_missing']      = 'The meta tag configuration to delete was not found.';
$string['data_missing']        = 'The requested meta tag configuration was not found.';
$string['update_id_missing'] = 'The meta tag configuration could not be updated because it no longer exists.';
$string['reset']               = 'Clear tags for this scope';
$string['resetconfirm']        = 'Are you sure you want to delete all meta tag overrides for this scope and page type?';

$string['manage_intro']        = 'Start by adding site-wide meta tags. They apply across the site unless you later create a more-specific override.';
$string['manage_col_pagetype'] = 'Page type';
$string['manage_none']         = 'No overrides have been defined yet. Global defaults apply everywhere.';

$string['tag_description']         = 'Description';
$string['tag_description_help']    = 'A short plain-text summary of the page. You can use [description] to use the current course, activity, category, user, or site description. Example value: Learn how to create and manage Moodle courses.';
$string['tag_keywords']            = 'Keywords';
$string['tag_keywords_help']       = 'Optional comma-separated keywords describing the page, for example: Moodle, online learning, courses.';
$string['tag_author']              = 'Author';
$string['tag_author_help']         = 'The person or organisation responsible for the page. Example value: Bardiya Learning Management System.';
$string['tag_robots']              = 'Robots directives';
$string['tag_robots_help']         = 'Search-engine crawling instructions, such as index,follow, noindex,nofollow, or noarchive. Example value: index,follow.';
$string['tag_canonical']           = 'Canonical URL';
$string['tag_canonical_help']      = 'The preferred absolute URL for this page. Keep [pageurl] unless multiple URLs show the same content. Example value: [pageurl] or https://lms.example.com/course/view.php?id=2.';
$string['tag_image']               = 'Image URL';
$string['tag_image_help']          = 'An absolute or site-relative URL for a general page image, for example: /local/metatags/pix/course.jpg.';
$string['tag_ogtitle']             = 'OpenGraph title';
$string['tag_ogtitle_help']        = 'Leave this empty to inherit a title from a parent scope. When automatic tags are enabled and no OpenGraph title is configured at any scope, the plugin uses the current context title, then the page title, and finally the site name. Example value: Introduction to Programming.';
$string['tag_ogdescription']       = 'OpenGraph description';
$string['tag_ogdescription_help']  = 'Leave this empty to inherit a description from a parent scope. When automatic tags are enabled and no OpenGraph description is configured at any scope, the plugin uses the current page or context description, then the site summary, and finally the page title. Example value: Learn programming through this practical online course.';
$string['tag_ogtype']              = 'OpenGraph type';
$string['tag_ogtype_help']         = 'The content type used by social platforms, usually website. Use article only for genuine article pages. Example value: website.';
$string['tag_ogimage']             = 'OpenGraph image URL';
$string['tag_ogimage_help']        = 'An absolute or site-relative URL to the image used in social previews. Use a public image that social platforms can fetch. Example value: https://lms.example.com/pluginfile.php/123/course/overview.jpg.';
$string['tag_ogurl']               = 'OpenGraph URL';
$string['tag_ogurl_help']          = 'The exact absolute URL of the page being shared. Keep [pageurl] so each page gets its own sharing URL. Example value: [pageurl] or https://lms.example.com/course/view.php?id=2.';
$string['tag_ogsitename']          = 'OpenGraph site name';
$string['tag_ogsitename_help']     = 'The name of the Moodle site or organisation. You can use [sitename]. Example value: Bardiya LMS.';
$string['tag_oglocale']            = 'OpenGraph locale';
$string['tag_oglocale_help']       = 'The language and region code for the page. Example value: en_US.';
$string['tag_ogimagesecureurl']    = 'OpenGraph secure image URL';
$string['tag_ogimagesecureurl_help'] = 'The HTTPS version of the OpenGraph image URL. Use this when the image is available over HTTPS. Example value: https://lms.example.com/pluginfile.php/123/course/overview.jpg.';
$string['tag_ogimagetype']         = 'OpenGraph image MIME type';
$string['tag_ogimagetype_help']    = 'The image MIME type. Example value: image/jpeg.';
$string['tag_ogimagewidth']        = 'OpenGraph image width';
$string['tag_ogimagewidth_help']   = 'The image width in pixels. Example value: 1200.';
$string['tag_ogimageheight']       = 'OpenGraph image height';
$string['tag_ogimageheight_help']  = 'The image height in pixels. Example value: 630.';
$string['tag_ogimagealt']          = 'OpenGraph image alt text';
$string['tag_ogimagealt_help']     = 'Briefly describe the image for accessibility, for example: Students attending an online course.';
$string['tag_twittercard']         = 'Twitter card type';
$string['tag_twittercard_help']    = 'The X/Twitter preview layout. Use summary for a small image or summary_large_image for a large image preview. Example value: summary_large_image.';
$string['tag_twittertitle']        = 'Twitter title';
$string['tag_twittertitle_help']   = 'Leave this empty to inherit a title from a parent scope. Configure this separately when you need a Twitter-specific title. Example value: Introduction to Programming.';
$string['tag_twitterdescription']  = 'Twitter description';
$string['tag_twitterdescription_help'] = 'Leave this empty to inherit a description from a parent scope. Configure this separately when you need a Twitter-specific description. Example value: Learn programming through this practical online course.';
$string['tag_twitterimage']        = 'Twitter image URL';
$string['tag_twitterimage_help']   = 'An absolute or site-relative URL to the image used in an X/Twitter card. Use a public image that the platform can fetch. Example value: https://lms.example.com/pluginfile.php/123/course/overview.jpg.';
$string['tag_twitterimagealt']     = 'Twitter image alt text';
$string['tag_twitterimagealt_help'] = 'Briefly describe the Twitter card image for accessibility. Example value: Screenshot of a Moodle course dashboard.';
$string['tag_twittersite']         = 'Twitter site account';
$string['tag_twittersite_help']    = 'The site owner account, usually written as a handle. Example value: @bardiyalms.';
$string['tag_twittercreator']      = 'Twitter creator account';
$string['tag_twittercreator_help'] = 'The content creator account, usually written as a handle. Example value: @santoshmagar.';

$string['privacy:metadata']    = 'The Meta tags plugin stores only site configuration entered by administrators. It does not collect or store data about site users.';

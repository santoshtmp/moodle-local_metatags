# local_metatags

A Moodle 4.5+ local plugin that adds Drupal Metatag-style `<meta>`/`<link>`
tag management for configured Moodle page types and exact URL paths:

- **Global** defaults using the `*` page type
- **Specific Moodle page types** such as `site-index`, `course-view-*`,
  `mod-forum-*`, and `user-profile`
- **Exact URL paths** using the Custom URL route option

Page-type patterns are matched from the most specific configured pattern to
the global `*` pattern. A custom URL route takes priority over page-type
matching. Configurations are site-wide and are managed by users with the
`local/metatags:manage` capability.

Examples include `blog-*`, `site-index`, `login-index`, and `course-view-*`.

When automatic tags are enabled, missing description and title variants are
derived from the current page context, course, activity, category, or site.

## Installation

1. Copy the `metatags` folder into `local/` so you have `local/metatags/`.
2. Visit *Site administration > Notifications* to install.
3. Go to *Site administration > Plugins > Local plugins > Meta tags* to
   configure global defaults and browse overrides.

Users with the `local/metatags:manage` capability can manage tags from the
plugin administration page.

## Overriding tags from another plugin or theme

Register a callback for `local_metatags\hook\override_tags` in the component's
`db/hooks.php`. The callback can modify the resolved tag arrays before tokens
are replaced and the tags are rendered:

```php
$callbacks = [
   [
      'hook' => \local_metatags\hook\override_tags::class,
      'callback' => [\local_example\hook_callbacks::class, 'override_meta_tags'],
   ],
];
```

```php
public static function override_meta_tags(\local_metatags\hook\override_tags $hook): void {
   $tags = $hook->get_tags();

   foreach ($tags as &$tag) {
      if ($tag['tagname'] === 'og:title') {
         $tag['content'] = 'Custom title';
      }
   }
   unset($tag);

   $hook->set_tags($tags);
}
```

Each tag contains `tagname`, `attribute`, and `content`. Call `set_tags()` to
add, remove, or replace tags. The current `moodle_page` is available through
`$hook->get_page()`.

## Capabilities

| Capability                     | Context level | Default roles |
|--------------------------------|---------------|---------------|
| `local/metatags:manage`        | System        | Manager       |

## Tokens

Tag content can include placeholders that are substituted at render time:
`[sitename]`, `[siteurl]`, `[pageurl]`, `[pagetitle]`, `[description]`,
`[coursename]`, `[courseshortname]`, `[categoryname]`, `[activityname]`,
`[userfullname]`, and `[image]`.

## Suggested roadmap / feature ideas

See the chat response for the full list — highlights: tag presets/import-export,
per-tag "lock" so overrides below a level can't remove a mandatory global tag,
a live front-end preview (Google/Facebook/Twitter card mockups), scheduled
tags, multi-language tag values, bulk CSV import for courses, and a
`report_metatags` companion report listing pages with missing descriptions.

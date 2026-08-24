# local_metatags

The Meta tags plugin gives Moodle site administrators a central way to define
and manage HTML metadata for their pages. It can be used to improve search
engine optimization (SEO), search result descriptions, social sharing
previews, canonical URLs, indexing directives, and other metadata without
modifying Moodle core or theme files. It helps search engines and social
platforms understand and display Moodle pages more effectively, but does not
guarantee higher search rankings.

Configurations can target the whole site, a Moodle page type, a wildcard page
type, or an exact URL path:

- **Global** defaults using the `*` page type
- **Specific Moodle page types** such as `site-index`, `course-view-*`,
  `mod-forum-*`, and `user-profile`
- **Exact URL paths** using the Custom URL route option

Page-type patterns are matched from the most specific configured pattern to
the global `*` pattern. A custom URL route takes priority over page-type
matching. Configurations are site-wide and are managed by users with the
`local/metatags:manage` capability.

Examples include `blog-*`, `site-index`, `login-index`, and `course-view-*`.

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

- `[sitename]` - The full name of the Moodle site.
- `[siteurl]` - The base URL of the Moodle site.
- `[pageurl]` - The full URL of the current page.
- `[pagetitle]` - The title of the current page.
- `[description]` - The most relevant description for the current page,
  such as the course summary, activity introduction, category description, or
  site summary.
- `[coursename]` - The full name of the current course.
- `[courseshortname]` - The short name of the current course.
- `[categoryname]` - The name of the current course category.
- `[activityname]` - The name of the current activity or resource.
- `[userfullname]` - The full name of the user for a user-context page.
- `[image]` - The most relevant available image URL for the current page,
  such as a course, category, or activity image.

Tokens are replaced when the page is rendered. If a token has no value in the
current context, it is replaced with an empty value.


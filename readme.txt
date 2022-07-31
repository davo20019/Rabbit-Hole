=== Rabbit Hole ===
Contributors: davo20019
Donate link: https://davidloor.com/
Tags: redirection, posts
Requires at least: 4.5
Tested up to: 6.0.1
Requires PHP: 5.6+
Stable tag: 0.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Rabbit Hole is a WordPress plugin that adds the ability to control what should happen when a post is being viewed at its own page.

== Description ==

Perhaps you have a post type that never should be displayed on its own page, like an image content type that's displayed in a carousel. Rabbit Hole can prevent this node from being accessible on its own page, through node/xxx.

Options
This works by providing multiple options to control what should happen when the post type is being viewed at its own page. You have the ability to

Deliver an access denied page.
Deliver a page not found page.
Issue a page redirect to any path or external url.
Or simply display the entity (regular behavior).
This is configurable per post type.

This plugin was inspired by the rabbit hole module for Drupal. https://www.drupal.org/project/rabbit_hole

== Installation ==

1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to the 'Settings' menu and configure each post type (/wp-admin/options-general.php?page=rabbit-hole-plugin)

== Frequently Asked Questions ==

 Q: How do I configure Rabbit Hole?
  A: Go to the 'Settings' menu and configure each post type (/wp-admin/options-general.php?page=rabbit-hole-plugin)

 Q: How do I disable Rabbit Hole?
  A: Go to the 'Settings' menu and deactivate the plugin (/wp-admin/plugins.php?action=deactivate&plugin=rabbit-hole-plugin)

 Q: Do you have an issue?
  A: You can contact me at https://davidloor.com/contact/


== Changelog ==

= 1.0 =
* Basic configuration per post type.

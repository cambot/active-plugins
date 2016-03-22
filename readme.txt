=== Active Plugins Fork ===
Contributors: jcmaci03, trepmal 
Tags: mutlisite, plugins, utility
Requires at least: 3.2.1
Tested up to: 4.2
Stable tag: 1.6.1

Generates a list of plugins that are currently in use

== Description ==

This plugin is a fork of [Active Plugins on MultiSite](https://wordpress.org/plugins/active-plugins-on-multisite/).

Generates a list of plugins that are currently in use, excluding Network-Activated

Untested with anything older than WP 3.2.1.

* [Cameron Macintosh's WordPress profile](http://profiles.wordpress.org/support/profile/jcmaci03/)
* [Plugin Repository](https://github.com/cambot/active-plugins)
* [Other plugins by Kailey Lampert](http://wordpress.org/extend/plugins/profile/trepmal)
* [Kailey Lampert's WordPress profile](http://profiles.wordpress.org/users/trepmal/)
* [Kailey Lampert's website](http://kaileylampert.com)

== Installation ==

1. Upload `active-plugins.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Find Active Plugins under Settings in the Network Admin

== Screenshots ==

1. Totals
2. tagged plugins

== Frequently Asked Questions ==

= How do I tag a plugin? =

Upload a file to the plugin's directory in the format `tag-name.tag`. For example, you might add a file to this plugin's directory called `utility.tag`
Note that this only supports one tag per plugin.

== Upgrade Notice ==

= 1.6 =
Not pretty, but not as ugly either. Code cleanup.

= 1.5 =
A little prettier.

= 1.4 =
plugin now shows inactive plugins (plugins with 0 users).

== Changelog ==

= 1.6.1 =
* List correct version of inactive plugins.
* Move "Active Plugins" menu from settings to plugins.

= 1.6 =
* Verified agains 3.5
* More code clean up
* Slight de-uglification.

= 1.5 =
* Finally updated for 3.2 (and 3.3-beta3 while we're at it)
* Cleaned up code
* A little bit prettier. Not that it's pretty, just "er"

= 1.4.1 =
* forgot to remove that part that said this plugin doesn't list inactive plugins

= 1.4 =
* now shows inactive plugins

= 1.3 =
* sorts the 'totals' list  alphabetically
* adds tagged sorting

= 1.2 =
* adds link to each site's plugin page for convenience

= 1.1 =
* quick fix to use table prefix variable instead of assuming default

= 1.0 =
* Initial Release.

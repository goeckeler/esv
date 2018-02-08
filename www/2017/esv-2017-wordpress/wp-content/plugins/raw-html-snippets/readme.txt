=== Raw HTML Snippets ===
Contributors: theandystratton
Donate link: http://theandystratton.com
Tags: raw html, html, embed html, autoformatting
Requires at least: 2.6
Tested up to: 4.1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Stable tag: trunk

Create a library of raw HTML snippets that you can easily insert into any page/post content using a shortcode.

== Description ==

Create a library of raw HTML snippets that you can easily insert into any page/post content using a shortcode:

[raw_html_snippet id="my-snippet"]

Snippets consist of a unique ID (e.g. "my-snippet") and raw HTML code. This plugin was written to stop using hacks that override WordPress' core content filters and affect shortcode output.

This plugin will NOT taint your content or the output of other shortcodes. If you delete a snippet, any existing shortcodes with that snipet's ID will output an empty string.

Remember, this allows you to output raw HTML. Use at your own risk. It will not check for malicious HTML/CSS/Javascript!

== Installation ==

1. Download and unzip to the 'wp-content/plugins/' directory 
2. Activate the plugin.
3. Use Settings > Raw HTML Snippets to add, edit and remove your snippets.
4. Insert snippets at your will

== Frequently Asked Questions ==

None.

== Screenshots ==

1. Plugin admin screen.
2. Editing a snippet
3. Inserting a snippet

== Changelog ==

= 1.1.3 =
* Works with latest version of WP (4.1.1)

= 1.1.2 =
* Fixed error where snippet id (slug) containing an ampersand (&) would break the plugin's admin UI.

= 1.1.1 =
* Updated some code for textarea direction attributes in admin panels (left to right)
* Added isset() to avoid PHP errors / warnings

= 1.1 =
* Fixed a bug that broke the ability to save when editing an existing snippet.

= 1.0 =
* Released version 1.0

== Upgrade Notice ==

= 1.1 =
A bug fix has been released, please upgrade to the latest version.

= 1.0 =
Upgrade to a new version of Raw HTML Snippets.  Thanks.

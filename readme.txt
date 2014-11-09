=== Age Verify ===
Contributors: ChaseWiseman
Tags: age, restrict, verify
Requires at least: 3.2
Tested up to: 4.0
Stable tag: 0.2.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A simple way to ask visitors for their age before viewing your content.

== Description ==

Whatever your reasons, you may need your site's visitors to confirm their age before viewing the content of your site. Age Verify does just that. It's a simple plugin that lets you specify a minimum age and add an age-gate between a potential visitor and your content, just to be safe. Enjoy!

== Installation ==

1. Upload the 'age-verify' folder to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Visit 'Settings > Age Verify' and adjust your configuration.

== Screenshots ==

1. The settings screen.
2. This is what your visitors see, using the default styling.

== Changelog ==

= 0.2.8 =
* Fixes a bug that failed to load translations properly. Props to @Nukleo!
* Added the French translation. Props to @Nukleo!

= 0.2.7 =
* Fixes a bug that displayed the minimum age incorrectly in some cases
* Fixes some PHP warnings on activation

= 0.2.6 =
* Major code refactor
* Add W3 Total Cache compatibility
* Add WordPress 4.0 compatibility
* Documentation cleanup

= 0.2.5 =
* Added WordPress 3.8 compatibility 
* Fixed a bit of text domain loading
* Added additional actions for further extensibility
* Adjusted some default modal styling

= 0.2.4 =
* Fixed a double slash when enqueueing the stylesheet
* Added some missing text domains
* Fixed a PHP warning

= 0.2.3 =
* Added Remember Me checkbox to all input types
* Escaped all strings for proper security

= 0.2.2 =
* Fixed the loading of language files

= 0.2.1 =
* Cleaned up folders filenames

= 0.2 =
* Added ability to age-restrict only specific content
* Security enhancements (nonces!)
* Squashed some PHP warnings

= 0.1.5 =
* Fixed support for PHP 5.2 and greater

= 0.1 =
* Just getting started...
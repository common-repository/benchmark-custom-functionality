=== Plugin Name ===
Contributors: oranblackwell
Tags: custom debug
Requires at least: 2.0.2
Tested up to: 3.2.1
Stable tag: 1.2

Two functions debug and debug_mail used for outputing the content of variables

== Description ==

Two functions debug and debug_mail used for outputing the content of variables. No Long description yet.

== Installation ==

1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
2. Edit the $allowedIPs array found in the debug function to include your own IP.
3. Edit the mail function found in the debug_mail function to include your own email address.
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Pass any variable to the function: eg. debug($var); or debug($unknown)

== Changelog ==

= 1.2 =
* Added the var type checking and colour coding
* Commented out the exit() functionality

= 1.0 =
* Initial version
=== WP PHP Console ===
Contributors: nekojira
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=GSTFUY3LMCA5W
Tags: development, debug, debugging
Requires at least: 3.6.0
Tested up to: 4.1
Stable tag: 1.3.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

An implementation of PHP Console for WordPress. Use Chrome Dev Tools to debug your WordPress installation!

== Description ==

WP PHP Console allows you to handle PHP errors & exceptions, dump variables, execute PHP code remotely and many other things using Google Chrome extension PHP Console and PhpConsole server library.

[PHP Console](https://github.com/barbushin/php-console) is a PHP library by barbushin.

This implementation for WordPress installs the PhpConsole server library and provides a WP PHP Console Settings page to Administrators.

For support and pull requests, please refer to [WP PHP Console Github repo](https://github.com/nekojira/wp-php-console) and read the instructions there - thank you.

== Installation ==

Install as any other WordPress plugin from your WordPress dashboard or through direct upload to server. In the latter case:

1. Upload the plugin to your `/wp-content/plugins/` directory (make sure the plugin resides in `wp-php-console` directory).
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. In the `Settings` menu go to `PHP Console`, edit any options and activate the console. You must also set a password otherwise the plugin won't work.

To make use of this plugin, you need to have PHP Console installed in your Google Chrome browser.
To install the extension, take the following steps:

1. Go to https://chrome.google.com/webstore/ and browse for PHP Console (this is normally located at https://chrome.google.com/webstore/detail/php-console/nfhmhhlpfleoednkpnnnkolmclajemef).
2. Follow the extension instructions.
3. Once installed, you need to navigate to your WordPress installation.
4. If you configured WP PHP Plugin correctly, your browser address bar should show a "key" icon, which, if clicked, will prompt for an authentication. Enter the password you have set earlier in the plugin options page.
5. "Key" icon will change into a terminal icon, click on and open the eval terminal.
6. You should be ready to go. You can use the PHP Console to debug your WordPress installation through your javascript console bundled with Chrome Dev Tools.

== Frequently Asked Questions ==

= Is this an official plugin from PHP Console author? =

No, but it makes use of Sergey's PHP Console library as it is.

= Does it work with Firefox? Internet Explorer? Opera? Other browsers? =

No it doesn't, unless PHP Console is ported as a Firefox add-on for example.

== Screenshots ==

None yet.

== Changelog ==

<<<<<<< HEAD
= 1.2.4 =
* Added configuration options nekojira/wp-php-console#5
=======
= 1.3.0 =
* Enhancement: added configuration options (props @polfo)
>>>>>>> GithubPolfo/wp-php-console/master
* - Register PC class
* - Show Call Stack
* - Short Path Names
* Fixes IP mask nekojira/wp-php-console#7

= 1.2.3 =
* Fixes "Wrong PHP Console eval request signature" error when executing WordPress code from terminal

= 1.2.2 =
* Bugfixes
* Submission to WordPress.org plugins repo

= 1.2.0 =
* Updated dependencies

= 1.1.0 =
* Added donation link/button.
* PHP Console server is now instantiated later, allowing to catch all your theme functions too.
* Included PHP Console server library as git submodule rather than a composer dependency.

= 1.0.0 =
* Added three options to set a custom password, enable on SSL only, authorized IP ranges.
* First public release.


== Upgrade Notice ==

= 1.0.0 =
First public release.

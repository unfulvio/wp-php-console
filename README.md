#WP PHP Console

This is an implementation of **[PHP Console](https://github.com/barbushin/php-console)** as a [WordPress](http://www.wordpress.org) plugin.  Use Chrome Dev Tools to debug your WordPress installation!

> PHP Console allows you to handle PHP errors & exceptions, dump variables, execute PHP code remotely and many other things using [Google Chrome PHP Console extension](https://chrome.google.com/webstore/detail/php-console/nfhmhhlpfleoednkpnnnkolmclajemef) and [PhpConsole server library](https://github.com/barbushin/php-console).

[PHP Console](https://github.com/barbushin/php-console) is a PHP library by barbushin.

This implementation for WordPress installs the PhpConsole server library and provides a WP PHP Console Settings page to Administrators.

PHP Console allows you to test any WordPress specific function or class (including those introduced by your active theme and plugins!) from Remote PHP Eval Terminal and inspect results, catch error and warnings with call stack trace straight from the Chrome JavaScript console.

PHP Console also allows you to _`print_r()`_ PHP variables from the WordPress server to the JavaScript console and optionally filter some of them from the Eval Terminal based on a tag mechanism.

PHP Console uses an http(s) side channel for communication between Browser and Server. The debug information from the server does not appear in the HTML content of your pages but directly in the Browser's JavaScript console.

[![Download from WordPress.org](https://github.com/nekojira/wp-php-console/blob/master/assets/wordpress-download-btn.png)](https://wordpress.org/plugins/wp-php-console/)


## Installation

Follow these steps:

1. First, install **[PHP Console for Google Chrome](https://chrome.google.com/webstore/detail/php-console/nfhmhhlpfleoednkpnnnkolmclajemef)**.

2. Then, add this plugin to your WordPress installation (by placing it into your `wp-content/plugins/` directory or corresponding plugins directory in your installation).

3. Once installed:

  - Activate WP PHP Console from WordPress `Plugins` menu as you would do with any other plugin;

  - Then go to `WP PHP Console` settings page from the `Settings` menu. From here you need to enter a password for the Eval Terminal (required, otherwise the WP PHP Console simply won't work).

  - You can also set other options.

### Options

In `Settings` `WP PHP Console` settings page

- You can tick a checkbox to force PHP Console to a SSL connection (of course then if you don't actually have SSL (https), PHP Console simply won't work).

- You can secure your server by specifying IP addresses to restrict the accessibility from the Eval Terminal (a single address eg. `192.168.0.4`; or an address mask eg. `192.168.*.*` or multiple IPs, comma separated `192.168.1.22,192.168.1.24,192.168.3.*`). In case of having issues in connecting with the Remote PHP Eval Terminal, try entering a generic wildcard `*` in this field.

- You can tick a checkbox to register `PC` in the global PHP namespace. This allows to write PC::debug($var, $tag) or PC::magic_tag($var) instructions in PHP to inspect $var in the JavaScript console.

- You can tick a checkbox to also see the call stack when PHP Console server writes to the JavaScript console.

- You can tick a checkbox to shorten PHP Console error sources and traces paths in the JavaScript console. Paths like /server/path/to/document/root/WP/wp-admin/admin.php:31 will be displayed as /WP/wp-admin/admin.php:31

## Usage

Once you have set up a password, you can navigate any of your WordPress pages (including WordPress admin dashboard pages) and try the Remote PHP Eval Terminal. You will see a "key" icon in your browser address bar. By clicking on it, it will prompt for the password you have set in the Settings page. After entering the correct password, you can use the Eval Terminal and run any PHP code from it, including WordPress's own functions: enter one or more lines of PHP code in the black Eval terminal screen, press Ctrl+Enter and see the result in Chrome Dev Tools JavaScript console.

In your PHP code on the Server, you can call PHP Console debug statements like `PC::debug( $var, $tag )` to display PHP variables in the JavaScript console and optionally filter selected tags through the browser's Remote PHP Eval Terminal screen's Ignore Debug options.

In JavaScript console you will see printed any PC::debug() information, PHP errors, warnings, notices with optional stack trace, which will be useful to debug your plugin or theme.

### Caveats

PHP Console allows execution of any remote PHP code. Use IP address protection. Make your password strong enough. Be aware that the password is passed in clear if not using SSL encryption (https).

You should NOT activate this plugin or PHP Console library in a production environment, rather a development/testing environment. You will otherwise add more load to your server and put your site at risk.

The JavaScript console shows PHP variables converted to JavaScript variables. A consequence of this is that associative PHP arrays like ['one'=>1, 'two'] are shown as object, automatically index arrays like [1, 'two'] are shown as array.

PC::debug( $var, $tag ) can only be called after the plugin has initialized.

### Browser support

Currently PHP Console only supports Google Chrome browser. If you're using, developing or testing with Firefox or Opera this plugin won't be of much use to you at the moment.

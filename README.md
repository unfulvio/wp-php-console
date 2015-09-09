# WP PHP Console

[![GitHub version](https://badge.fury.io/gh/nekojira%2Fwp-php-console.svg)](http://badge.fury.io/gh/nekojira%2Fwp-php-console)
[![Dependency Status](https://gemnasium.com/nekojira/wp-php-console.svg)](https://gemnasium.com/nekojira/wp-php-console)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/nekojira/wp-php-console/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/nekojira/wp-php-console/?branch=master)
[![Join the chat at https://gitter.im/nekojira/wp-php-console](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/nekojira/wp-php-console?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

An implementation of [PHP Console](https://github.com/barbushin/php-console "PHP Console") as a [WordPress](http://www.wordpress.org) plugin. Use Chrome Dev Tools to debug your WordPress installation!

## Description

> PHP Console allows you to handle PHP errors & exceptions, dump variables, execute PHP code remotely and many other things using [Google Chrome extension PHP Console](https://chrome.google.com/webstore/detail/php-console/nfhmhhlpfleoednkpnnnkolmclajemef) and [PHP Console server library](https://github.com/barbushin/php-console).

This implementation of PHP Console makes easy to debug a WordPress installation from Chrome browser JavaScript console and test any WordPress specific function or class (including those introduced by your active theme and plugins!) from a terminal. You can run any PHP or WordPress specific function and inspect results, catch errors and warnings with call stack trace straight which will be displayed in the Chrome JavaScript console. You can do PHP debugging alongside your JavaScript debugging in one place, without having PHP to print errors and warnings in your HTML page.

[![Download from WordPress.org](https://github.com/nekojira/wp-php-console/blob/master/assets/wordpress-download-btn.png)](https://wordpress.org/plugins/wp-php-console/)


## Installation

Note: you will need PHP 5.4.0 minimum version on your machine or server to run this plugin.

1. First, install [Google Chrome extension PHP Console](https://chrome.google.com/webstore/detail/php-console/nfhmhhlpfleoednkpnnnkolmclajemef) from the [Chrome WebStore](https://chrome.google.com/webstore/search/php%20console?_category=extensions).
Make sure the PHP Console Chrome extension is enabled through [chrome://extensions/](chrome://extensions/ "chrome://extensions/").

2. Then, add this plugin to your WordPress installation either by:

  - Installing it as any other WordPress plugin from your WordPress admin Plugins page (`Add New`)

  - Uploading it in `wp-php-console` directory into your `wp-content/plugins/` directory or corresponding plugins directory in your installation

3. Activate the plugin through the `Plugins` admin page in WordPress

4. In the `Settings` menu go to `WP PHP Console`:

  - Enter a password for the Eval Terminal (this setting is needed or the terminal feature simply won't work).

  - You can also set other options.

## Options

##### Allow only on SSL	
> Forces PHP Console to connect on a SSL connection only (of course then if you don't actually have SSL (https), PHP Console simply won't work).

##### Allowed IP Masks
> You can secure your server by specifying IP addresses to restrict the accessibility from the Eval Terminal (a single address eg. `192.168.0.4` or an address mask eg. `192.168.*.*` or multiple IPs, comma separated `192.168.1.22,192.168.1.24,192.168.3.*`). In case of having issues connecting with the Remote PHP Eval Terminal, try leaving this blank.

##### Register PC Class
> Tick this option to register `PC` in the global PHP namespace. This allows to write `PC::debug($var, $tag)` or `PC::magic_tag($var)` instructions in PHP to inspect `$var` in the JavaScript console.

##### Show Call Stack	
> Tick this option to see the call stack when PHP Console server writes to the JavaScript console.

##### Short Path Names
> Tick this checkbox to shorten PHP Console error sources and traces paths in the JavaScript console. E.g. paths like `/server/path/to/document/root/WP/wp-admin/admin.php:38` will be displayed as `/WP/wp-admin/admin.php:38`

## Usage

After you entered WP PHP Plugin password, your browser address bar should show a yellow "key" icon, which, if clicked, will prompt for the password you have set earlier.
The "key" icon will change into a "terminal" icon, click on it to open the PHP Console eval & options form.

After entering the correct password, you can use the Eval Terminal in the PHP Console eval & options form and run any PHP code from it, including WordPress's own functions: enter one or more lines of PHP code in the black Eval terminal screen, press Ctrl+Enter and see the result in Chrome Dev Tools JavaScript console.
The result includes the output, the return value and the net server execution time.

In your PHP code on the Server, you can call PHP Console debug statements like `PC::debug( $var, $tag )` to display PHP variables in the JavaScript console and optionally filter selected tags through the PHP Console eval & options form opened from the address bar in your browser.

In the JavaScript console you will see printed any PC::debug() information, PHP errors, warnings, notices with optional stack trace, which will be useful to debug your plugin or theme.

## FAQ

#### Is this an official plugin from PHP Console author?
>No, but it makes use of Sergey's PHP Console library as it is.

#### Does it work with Firefox, IE, Opera or other browsers?
>No it doesn't, unless PHP Console browser extension is ported, for example, as a Firefox add-on.

#### Can I use PHP Console in a live production environment?
>You *can* but it is definitely not a good idea. You should do your debugging and testing on a development/testing environment on a staging server or local machine. Likewise, you normally wouldn't want to turn on PHP error reporting or set `WP_DEBUG` to `true` in a live site as you wouldn't want to display error information to public. Furthermore, PHP Console allows execution of any remote PHP code through terminal - for this you can set a strong password and restrict the IP address range to access the terminal, but still it's not advisable. Besides putting your site **at risk**, you will also add more load to your server.

#### Why are PHP arrays shown as objects?
>The JavaScript console prints PHP variables as JavaScript variables. Associative PHP arrays such as `['key1' => 'var2', 'key2' => 'var2', ... ]` are shown as objects; automatically indexed arrays like `[ 'var1', 'var2', ... ]` are shown as arrays.

#### I got `Fatal error: Class 'PC' not found in 'my code'` - what's that?

`PC::debug( $my_var, $my_tag )` can only be called after the WordPress core included the WP PHP Console plugin.

You could move your debug code or either do something like

     // delay use of PC class until WP PHP Console plugin is included
     add_action('plugins_loaded', function () use ($my_var) {
         // send $my_var with tag 'my_tag' to the JavaScript console through PHP Console Server  Library and PHP Console Chrome Plugin
         PC::my_tag($my_var);
     });

or

    // PHP Console autoload
    require_once dirname( __FILE__ ) . '/wp-php-console/vendor/autoload.php';
    // make PC class available in global PHP scope
    if( !class_exists( 'PC', false ) ) { 
        PhpConsole\Helper::register();
    }
    // send $my_var with tag 'my_tag' to the JavaScript console through PHP Console Server Library and PHP Console Chrome Plugin
    PC::my_tag($my_var);

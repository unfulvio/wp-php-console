# === WP PHP Console ===

An implementation of PHP Console as a WordPress plugin.
Use Chrome Dev Tools to debug your WordPress installation!

## == Description ==

> PHP Console allows you to handle PHP errors & exceptions, dump variables, execute PHP code remotely and many other things using [Google Chrome extension PHP Console](https://chrome.google.com/webstore/detail/php-console/nfhmhhlpfleoednkpnnnkolmclajemef) and [PHP Console server library](https://github.com/barbushin/php-console), both from [Sergey Barbushin](https://github.com/barbushin).

This WP PHP Console [WordPress](http://www.wordpress.org) plugin includes and initialises the PHP Console server library and provides a WP PHP Console Settings page to Administrators.

PHP Console allows you to test any WordPress specific function or class (including those introduced by your active theme and plugins!) from Remote PHP Eval Terminal and inspect results, catch error and warnings with call stack trace straight from the Chrome JavaScript console.

PHP Console also allows you to "`print_r()`" PHP variables from the WordPress server to the JavaScript console and optionally filter some of them from the Eval Terminal based on a tag mechanism.

PHP Console uses an http(s) side channel for communication between Browser and Server.
The debug information from the server does not appear in the HTML content of your pages but directly in the Browser's JavaScript console.

[![Download from WordPress.org](https://github.com/nekojira/wp-php-console/blob/master/assets/wordpress-download-btn.png)](https://wordpress.org/plugins/wp-php-console/)


## == Installation ==

1. First, install [Google Chrome extension PHP Console](https://chrome.google.com/webstore/detail/php-console/nfhmhhlpfleoednkpnnnkolmclajemef) from the [Chrome WebStore](https://chrome.google.com/webstore/search/php%20console?_category=extensions).
Make sure the PHP Console Chrome extension is enabled through chrome://extensions/.

2. Then, add this plugin to your WordPress installation

  - Install as any other WordPress plugin from your WordPress `Plugins` `Add New` menu or

  - Upload it in `wp-php-console` directory into your `wp-content/plugins/` directory or corresponding plugins directory in your installation

3. Activate the plugin through the `Plugins` menu in WordPress.

4. In the `Settings` menu go to `WP PHP Console`

  - Activate WP PHP Console from WordPress `Plugins` menu as you would do with any other plugin.

  - Then go to `WP PHP Console` settings page from the `Settings` menu. From here you need to enter a password for the Eval Terminal.

  - You can also set other options.

### = Options =

In `Settings` `WP PHP Console` page

- You can tick a checkbox to force PHP Console to a SSL connection (of course then if you don't actually have SSL (https), PHP Console simply won't work).

- You can secure your server by specifying IP addresses to restrict the accessibility from the Eval Terminal
(a single address eg. `192.168.0.4` or an address mask eg. `192.168.*.*` or multiple IPs, comma separated `192.168.1.22,192.168.1.24,192.168.3.*`).
In case of having issues connecting with the Remote PHP Eval Terminal, temporarily leave this blank.

- You can tick a checkbox to register `PC` in the global PHP namespace.
This allows to write PC::debug($var, $tag) or PC::magic_tag($var) instructions in PHP to inspect $var in the JavaScript console.

- You can tick a checkbox to also see the call stack when PHP Console server writes to the JavaScript console.

- You can tick a checkbox to shorten PHP Console error sources and traces paths in the JavaScript console.
Paths like `/server/path/to/document/root/WP/wp-admin/admin.php:38` will be displayed as `/WP/wp-admin/admin.php:38`

## == Usage ==

After you entered WP PHP Plugin password, your browser address bar should show a yellow "key" icon, which, if clicked, will prompt for the password you have set earlier.
The "key" icon will change into a "terminal" icon, click on it to open the PHP Console eval & options form.

After entering the correct password, you can use the Eval Terminal in the PHP Console eval & options form and run any PHP code from it, including WordPress's own functions: enter one or more lines of PHP code in the black Eval terminal screen, press Ctrl+Enter and see the result in Chrome Dev Tools JavaScript console.
The result includes the output, the return value and the net server execution time.

In your PHP code on the Server, you can call PHP Console debug statements like `PC::debug( $var, $tag )` to display PHP variables in the JavaScript console and optionally filter selected tags through the PHP Console eval & options form opened from the address bar in your browser.

In the JavaScript console you will see printed any PC::debug() information, PHP errors, warnings, notices with optional stack trace, which will be useful to debug your plugin or theme.

## == Frequently Asked Questions ==

### = Is this an official plugin from PHP Console author? =

No, but it makes use of Sergey's PHP Console library as it is.

### = Does it work with Firefox? Internet Explorer? Opera? Other browsers? =

No it doesn't, unless PHP Console extension is ported as a Firefox add-on for example.

### = Tell me about security risks =

PHP Console allows execution of any remote PHP code. Use IP address protection. Choose a strong password.

You should NOT activate this plugin or PHP Console library in a production environment, rather a development/testing environment.
You will otherwise add more load to your server and put your site at risk.

It may also be safe to disable the browser plugin when not needed.

### = Why are my PHP arrays shown as objects? =

The JavaScript console shows PHP variables converted to JavaScript variables.
A consequence of this is that associative PHP arrays like ['one'=>1, 'two'] are shown as object, automatically index arrays like [1, 'two'] are shown as array.

### = Fatal error: Class 'PC' not found in 'my code' =

`PC::debug( $my_var, $my_tag )` can only be called after the WordPress core included the WP PHP Console plugin.

You could move your debug code or either do something like

```
  // delay use of PC class until WP PHP Console plugin is included
  add_action('plugins_loaded', function () use ($my_var) {
    // send $my_var with tag 'my_tag' to the JavaScript console through PHP Console Server Library and PHP Console Chrome Plugin
    PC::my_tag($my_var);
  });
```

or

```
  // PHP Console autoload
  require_once dirname( __FILE__ ) . '/wp-php-console/vendor/autoload.php';

  // make PC class available in global PHP scope
  if( !class_exists( 'PC', false ) ) PhpConsole\Helper::register();

    // send $my_var with tag 'my_tag' to the JavaScript console through PHP Console Server Library and PHP Console Chrome Plugin
  PC::my_tag($my_var);

```

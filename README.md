#WP PHP Console

This is an implementation of **[PHP Console](https://github.com/barbushin/php-console)** as a [WordPress](http://www.wordpress.org) plugin.

> PHP Console allows you to handle PHP errors & exceptions, dump variables, execute PHP code remotely and many other things using [Google Chrome PHP Console extension](https://chrome.google.com/webstore/detail/php-console/nfhmhhlpfleoednkpnnnkolmclajemef) and [PhpConsole server library](https://github.com/barbushin/php-console).

This implementation for WordPress allows you to test any WordPress specific function or class (including those introduced by your active theme and plugins!) from PHP Console terminal and inspect results, catch error and warnings with stack trace straight from Chrome Dev Tools console. 


## Installation

Follow these steps: 

1. First, install **[PHP Console for Google Chrome](https://chrome.google.com/webstore/detail/php-console/nfhmhhlpfleoednkpnnnkolmclajemef)**. 

2. Then, add this plugin to your WordPress installation (by placing it into your `wp-content/plugins/` directory or corresponding plugins directory in your installation). 

3. Once installed, activate WP PHP Console from WordPress plugins dashboard page as you would do with any other plugin; then go to `PHP Console` settings page from the `Settings` menu. From here you need to enter a password for the eval terminal (required, otherwise the eval terminal simply won't work). You can also set other options.

### Options

In WP PHP Console settings page, you can tick a checkbox to use the terminal only on a SSL connection (of course then if you don't actually have SSL, PHP Console simply won't work). You can also specify IP addresses to restrict the accessibility to the eval terminal (a single address eg. `192.168.0.4`; or an address mask eg. `192.168.*.*` or multiple IPs, comma separated `192.168.1.22,192.168.1.24,192.168.3.*`). In case of having issues in connecting with the PHP terminal, try entering a  generic wildcard `*` in this field.


## Usage

Once you have set up a password, you can navigate any of your WordPress page (including WordPress admin dashboard pages) and try the console. You will se a "key" icon in your browser address bar. By clicking on it, it will prompt for the password you have set just before. After entering the correct password, you can use the eval terminal and run any PHP code from it, including WordPress own functions. Furthermore, in Chrome Dev Tools console you will also see printed any PHP errors, warnings, notices with stack trace, which will be useful to debug your plugin or theme.  

### Caveats

You should NOT use this plugin or PHP Console library in a production environment, rather a development/testing environment. You will otherwise add more load to your server and even put your site at risk since you're exposing PHP code publicly.

### Browser support

Currently PHP Console only supports Google Chrome browser. If you're using, developing or testing with Firefox or Opera this plugin won't be of much use to you at the moment.
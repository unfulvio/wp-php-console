#WP PHP Console

This is an implementation of [PHP Console](https://github.com/barbushin/php-console) as a [WordPress](http://www.wordpress.org) plugin.

PHP Console allows you to handle PHP errors & exceptions, dump variables, execute PHP code remotely and many other things using [Google Chrome PHP Console extension](https://chrome.google.com/webstore/detail/php-console/nfhmhhlpfleoednkpnnnkolmclajemef) and [PhpConsole server library](https://github.com/barbushin/php-console).

This implementation for WordPress allows you to test any WordPress specific function or class from PHP Console terminal and inspect results straight from Chrome Dev Tools console. 


## Installation

First, install [PHP Console for Google Chrome](https://chrome.google.com/webstore/detail/php-console/nfhmhhlpfleoednkpnnnkolmclajemef). Then, add and activate this plugin to your WordPress installation (e.g. by copying it into your `wp-content/plugins/` folder).

Once activated from WordPress plugins dashboard page, go to `PHP Console` menu in `Settings`. From here you need to enter a password for the eval terminal (required, or the eval terminal simply won't work).

Once you have set up a password, you can navigate anywhere in your WordPress installation and try the console. You will se a "key" icon in your browser address bar. By clicking on it, it will prompt for the password you entered before. After entering the correct password, you can use the eval terminal and run any PHP code from it, including WordPress own functions. Furthermore, in Chrome Dev Tools console you will also see displayed PHP errors, warnings, notices and a stack trace, which will be useful to debug your plugin or theme.  

### Options

In WP PHP Console settings page, you can also tick a checkbox to use the terminal only on a SSL connection. You can also specify IP addresses to restrict the accessibility to the eval terminal (a single address eg. `192.168.0.4`; or an address mask eg. `192.168.*.*` or multiple IPs, comma separated `192.168.1.22,192.168.1.24,192.168.3.*`).   


### Browser support

Currently PHP Console only supports Chrome browser. If you're using Firefox or Opera this plugin won't be of much use to you at the moment.
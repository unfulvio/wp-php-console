# Changelog

### 1.5.5 (unreleased)
 * Misc: Add plugin admin action links

### 1.5.4 (30 May 2019)
 * Fix: Temporarily suppress PHP warnings while connecting with PHP Console to avoid headers already sent warnings, then restore all errors reporting
 * Misc: Improved PHP and WordPress compatibility loader

### 1.5.3 (22 May 2019)
 * Fix: Try to get rid of PHP errors related to "Unable to set PHP Console server cookie" and "Cannot modify header information - headers already sent"
 * Misc: Require PHP 5.6

### 1.5.2 (12 Sep 2018)
 * Misc: Updates PHP Console core library to v3.1.7

### 1.5.1 (19 Feb 2018)
 * Misc: Bump WordPress compatibility to mark support for the latest versions

### 1.5.0 (03 Jun 2016)
 * Fix: Fixes "PHP Warning: session_start(): Cannot send session cache limiter - headers already sent" notice in logs
 * Misc: Internal changes, new Settings class, deprecated methods and properties in main Plugin class
 * Misc: Updated PHP Console Library to 3.1.6
 * Misc: Tested up to WordPress 4.5.2

### 1.4.0 (11 Jan 2016)
 * Enhancement: Support for WordPress language packs
 * Misc: Improved error and exception handling and usage of Composer in plugin development
 * Misc: Updated PHP Console Library to 3.1.5
 * Misc: Tested up to WordPress 4.4.1

### 1.3.9 (09 sep 2015)
 * Misc: Use WP Requirements as Composer dependency

### 1.3.8 (14 jul 2015)
 * Misc: Internal changes (alternate PHP version check, automated SVN deploys)

### 1.3.7 (09 jul 2015)
 * Fix: Fixes a bug `Cannot send session cache limiter - headers already sent`
 * Misc: Updated PHP Console Library to 3.1.4

### 1.3.5 (10 jun 2015)
 * Misc: PHP 5.4.0 is the minimum required version to activate the plugin
 * Misc: Updated PHP Console library to 3.1.3

### 1.3.3 (30 apr 2015)
 * Misc: Supports WordPress 4.2

### 1.3.2 (03 mar 2015)
 * Fix: Fixes "Fatal error: Using $this when not in object context" upon activation in some installations.

### 1.3.1 (09 feb 2015)
 * Enhancement: earlier PC initialisation - props @Polfo
 * Misc: Updated readme files.

### 1.3.0 (05 feb 2015)
 * Fix: IP mask
 * Enhancement: added configuration options - props @Polfo
   - Register PC class
   - Show Call Stack
   - Short Path Names

### 1.2.3 (21 jan 2015)
 * Fix: Fixes "Wrong PHP Console eval request signature" error when executing WordPress code from terminal, props @Polfo @barbushin

### 1.2.2 (15 jan 2015)
 * Fix: Bugfixes
 * Misc: Submission to WordPress.org plugins repository.

### 1.2.1 (12 dec 2014) 
 * Fix: Fixed allowed IPs bug.

### 1.2.0 (11 dec 2014) 
 * Misc: Updated dependencies and got rid of git submodules.

### 1.1.0 (07 nov 2014) 
 * Fix: PHP Console server is now instantiated later, allowing to catch all your theme functions too.
 * Misc: Included PHP Console server library as git submodule rather than a composer dependency.

### 1.0.0 (06 nov 2014) 
 * First public release.

# Changelog

### 1.3.5 (10 jun 2015)
* Updated PHP Console library to 3.1.3
* PHP 5.4.0 is the minimum required version to activate the plugin

### 1.3.3 (30 apr 2015)
* Supports WordPress 4.2

### 1.3.2 (03 mar 2015)
* Fixes "Fatal error: Using $this when not in object context" upon activation in some installations.

### 1.3.1 (09 feb 2015)
* Enhancement: earlier PC initialisation - props @Polfo
* Updated readme files.

### 1.3.0 (05 feb 2015)
* Fix: IP mask
* Enhancement: added configuration options - props @Polfo
  - Register PC class
  - Show Call Stack
  - Short Path Names

### 1.2.3 (21 jan 2015)

* Fixes "Wrong PHP Console eval request signature" error when executing WordPress code from terminal, props @Polfo @barbushin

### 1.2.2 (15 jan 2015)
* Bugfixes
* Submission to WordPress.org plugins repository.

### 1.2.1 (12 dec 2014) 
* Fixed allowed IPs bug.

### 1.2.0 (11 dec 2014) 
* Updated dependencies and got rid of git submodules.

### 1.1.0 (07 nov 2014) 
* Added donation link/button.
* PHP Console server is now instantiated later, allowing to catch all your theme functions too.
* Included PHP Console server library as git submodule rather than a composer dependency.

### 1.0.0 (06 nov 2014) 
* Added three options to set a custom password, enable on SSL only, authorized IP ranges.
* First public release.
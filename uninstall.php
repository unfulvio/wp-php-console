<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @link       https://github.com/nekojira/wp-php-console
 * @since      1.0.0
 *
 * @package    WP_PHP_Console
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;

delete_option( 'wp_php_console' );

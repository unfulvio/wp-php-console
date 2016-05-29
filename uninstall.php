<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @link    https://github.com/nekojira/wp-php-console
 * @since   1.0.0
 * @package WP_PHP_Console
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit; 
}

delete_option( 'wp_php_console' );

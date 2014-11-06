<?php
/**
 * @link              https://github.com/nekojira/wp-php-console/
 * @since             1.0.0
 * @package           WP_PHP_Console
 *
 * @wordpress-plugin
 * Plugin Name:       WP PHP Console
 * Plugin URI:        https://github.com/nekojira/wp-php-console/
 * Description:       An implementation of PHP Console for WordPress. Easily debug and trace PHP errors and warnings from your Chrome dev tools console using a Google Chrome extension.
 * Version:           1.0.0
 * Author:            nekojira
 * Author URI:        https://github.com/nekojira/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-php-console
 * Domain Path:       /languages
 */

if ( ! defined( 'WPINC' ) ) die;

require_once plugin_dir_path( __FILE__ ) . 'lib/vendor/autoload.php';
require_once plugin_dir_path( __FILE__ ) . 'lib/class-wp-php-console.php';

$WP_PHP_Console = new WP_PHP_Console();
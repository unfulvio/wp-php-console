<?php
/**
 * @link              https://github.com/nekojira/wp-php-console/
 * @since             1.0.0
 * @package           WP_PHP_Console
 * @author            Fulvio Notarstefano <fulvio.notarstefano@gmail.com>
 *
 * @wordpress-plugin
 * Plugin Name:       WP PHP Console
 * Plugin URI:        https://github.com/nekojira/wp-php-console/
 * Description:       An implementation of PHP Console for WordPress. Easily debug and trace PHP errors and warnings from your Chrome dev tools console using a Google Chrome extension.
 * Version:           1.3.6
 * Author:            Fulvio Notarstefano
 * Author URI:        https://github.com/nekojira/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-php-console
 * Domain Path:       /languages
 */

/**
 * WP PHP Console
 * Copyright (c) 2014-2015 Fulvio Notarstefano <fulvio.notarstefano@gmail.com>
 * and contributors
 *
 * PhpConsole server library.
 * Copyright (c) 2011-2013 by Barbushin Sergey <barbushin@gmail.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

if ( ! defined( 'WPINC' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WP PHP Console requires PHP 5.4.0 minimum.
 * WordPress supports 5.2.4 and recommends 5.4.0.
 * @link https://make.wordpress.org/plugins/2015/06/05/policy-on-php-versions/
 */
if ( version_compare( PHP_VERSION, '5.4.0', '<') ) {
	add_action( 'admin_notices',
		function() {
			echo '<div class="error"><p>'.
			     sprintf( __( "WP PHP Console requires PHP 5.4 or above to function properly. Detected PHP version on your server is %s. Please upgrade PHP to activate WP PHP Console or remove the plugin.", 'wp-php-console' ), phpversion() ? phpversion() : '`undefined`' ) .
			     '</p></div>';
			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}
		}
	);
	return;
}

/**
 * Include PhpConsole server library.
 * @link https://github.com/barbushin/php-console
 * Copyright (c) 2011-2013 by Barbushin Sergey <barbushin@gmail.com>.
 */
require_once 'vendor/autoload.php';

/**
 * The main class of this plugin.
 */
require_once 'lib/class-wp-php-console.php';


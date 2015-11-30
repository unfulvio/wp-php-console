<?php
/**
 * Plugin Name:       WP PHP Console
 * Plugin URI:        https://github.com/unfulvio/wp-php-console/
 * Description:       An implementation of PHP Console for WordPress. Easily debug and trace PHP errors and warnings from your Chrome dev tools console using a Google Chrome extension.
 *
 * Version:           1.4.0
 *
 * Author:            Fulvio Notarstefano
 * Author URI:        https://github.com/unfulvio/
 *
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * Text Domain:       wp-php-console
 * Domain Path:       /languages
 */

if ( ! defined( 'WPINC' ) ) {
	exit;
}

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

// Composer fallback for PHP < 5.3.0.
if ( version_compare( PHP_VERSION, '5.3.0' ) === -1 ) {
	include_once 'vendor/autoload_52.php';
} else {
	include_once 'vendor/autoload.php';
}

/**
 * WP PHP Console requires PHP 5.4.0 minimum.
 * @link https://make.wordpress.org/plugins/2015/06/05/policy-on-php-versions/
 * @link https://github.com/nekojira/wp-requirements
 */
$this_plugin_checks = new WP_Requirements(
	'WP PHP Console',
	plugin_basename( __FILE__ ),
	array(
		'PHP' => '5.4.0',
	)
);

if ( $this_plugin_checks->pass() === false ) {
	// Stop.
	$this_plugin_checks->halt();
	return;
} else {
	// Load the main class of this plugin.
	require_once 'includes/class-wp-php-console.php';
	new \WP_PHP_Console\Plugin();
}

<?php
/**
 * WP PHP Console
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @author    Fulvio Notarstefano <fulvio.notarstefano@gmail.com>
 * @copyright Copyright (c) 2014-2019 Fulvio Notarstefano
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;


/**
 * Gets the WP PHP Console instance.
 *
 * @since 1.5.4
 *
 * @return WP_PHP_Console\Plugin
 */
function wp_php_console() {

	return new \WP_PHP_Console\Plugin();
}

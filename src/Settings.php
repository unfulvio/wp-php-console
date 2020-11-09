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
 * @copyright Copyright (c) 2014-2020 Fulvio Notarstefano
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace WP_PHP_Console;

defined( 'ABSPATH' ) or exit;

/**
 * WP PHP Console settings handler.
 *
 * @since 1.5.0
 */
class Settings {


	/** @var array settings options */
	private static $options = [];


	/**
	 * Gets the settings option key.
	 *
	 * @since 1.6.0
	 *
	 * @return string
	 */
	public static function get_settings_key() {

		return str_replace( '-', '_', Plugin::ID );
	}


	/**
	 * Gets the plugin settings.
	 *
	 * @since 1.6.0
	 *
	 * @return array associative array of key-values
	 */
	public static function get_settings() {

		if ( empty( self::$options ) ) {

			self::$options = wp_parse_args(
				(array) get_option( self::get_settings_key(), [] ),
				[
					'ip'       => '',
					'password' => '',
					'register' => false,
					'short'    => false,
					'ssl'      => false,
					'stack'    => false,
				]
			);
		}

		return self::$options;
	}


	/**
	 * Gets the restricted IP(s), if set.
	 *
	 * @since 1.6.0
	 *
	 * @return string[] array of individual IPs or ranges of IPs
	 */
	public static function get_allowed_ip_masks() {

		$allowed_ips = self::get_settings()['ip'];

		return ! empty( $allowed_ips['ip'] ) ? explode( ',', $allowed_ips['ip'] ) : [];
	}


	/**
	 * Gets the PHP Console terminal password.
	 *
	 * @since 1.6.0
	 *
	 * @return string
	 */
	public static function get_eval_terminal_password() {

		return self::get_settings()['password'];
	}


	/**
	 * Determines whether a password exists and is not empty.
	 *
	 * @since 1.6.0
	 *
	 * @return bool
	 */
	public static function has_eval_terminal_password() {

		$password = self::get_eval_terminal_password();

		return is_string( $password ) && '' !== trim( $password );
	}


	/**
	 * Determines whether the PC class should be registered and made available.
	 *
	 * @since 1.6.0
	 *
	 * @return bool
	 */
	public static function should_register_pc_class() {

		return ! empty( self::get_settings()['register'] );
	}


	/**
	 * Determines whether PHP Console should only accept secure connections.
	 *
	 * @since 1.6.0
	 *
	 * @return bool
	 */
	public static function should_use_ssl_only() {

		return ! empty( self::get_settings()['ssl'] );
	}


	/**
	 * Determines whether the full call stack should be displayed.
	 *
	 * @since 1.6.0
	 *
	 * @return bool
	 */
	public static function should_show_call_stack() {

		return ! empty( self::get_settings()['stack'] );
	}


	/**
	 * Determines whether the length of PHP Console error sources should be shortened.
	 *
	 * @since 1.6.0
	 *
	 * @return bool
	 */
	public static function should_use_short_path_names() {

		return ! empty( self::get_settings()['short'] );
	}


}

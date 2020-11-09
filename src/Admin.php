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
 * WP PHP Console admin handler.
 *
 * @since 1.6.0
 */
class Admin {


	/**
	 * Initializes the plugin admin.
	 *
	 * @since 1.6.0
	 */
	public function __construct() {

		self::add_plugin_page_row_action_links();
		self::output_admin_notices();

		// init settings page
		if ( ! defined( 'DOING_AJAX' ) ) {
			new Admin\SettingsPage();
		}
	}


	/**
	 * Displays notices in admin.
	 *
	 * @since 1.6.0
	 */
	private static function output_admin_notices() {

		// display admin notice and abort if no password has been set
		add_action( 'all_admin_notices', static function() {

			if ( ! Settings::has_eval_terminal_password() ) :

				?>
				<div class="notice notice-warning">
					<p>
						<?php printf(
							/* translators: Placeholders: %1$s - WP PHP Console name, %2$s - opening HTML <a> link tag; %3$s closing HTML </a> link tag */
							__( '%1$s: Please remember to %2$sset a password%3$s if you want to enable the terminal.', 'wp-php-console' ),
							'<strong>' . Plugin::NAME . '</strong>',
							'<a href="' . esc_url( admin_url( 'options-general.php?page=wp_php_console' ) ) .'">',
							'</a>'
						); ?>
					</p>
				</div>
				<?php

			endif;

		}, -1000 );
	}


	/**
	 * Adds plugin page row action links.
	 *
	 * @since 1.6.0
	 */
	private static function add_plugin_page_row_action_links() {

		add_filter( 'plugin_action_links_wp-php-console/wp-php-console.php', static function( $actions ) {

			return array_merge( [
				'<a href="' . esc_url( admin_url( 'options-general.php?page=' . str_replace( '-', '_', Plugin::ID ) ) ) . '">' . esc_html__( 'Settings', 'wp-php-console' ) . '</a>',
				'<a href="' . esc_url( Plugin::get_wp_php_console_repository_url() ) . '">' . esc_html__( 'GitHub', 'wp-php-console' ) . '</a>',
				'<a href="' . esc_url( Plugin::get_support_page_url() ) . '">' . esc_html__( 'Support', 'wp-php-console' ) . '</a>',
				'<a href="' . esc_url( Plugin::get_reviews_page_url() ) . '">' . esc_html__( 'Review', 'wp-php-console' ) . '</a>',
			], $actions );

		} );
	}


}

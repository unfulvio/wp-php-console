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

namespace WP_PHP_Console\Admin;

use WP_PHP_Console\Plugin;
use WP_PHP_Console\Settings;

defined( 'ABSPATH' ) or exit;

/**
 * WP PHP Console settings page handler.
 *
 * @since 1.6.0
 */
class SettingsPage {


	/** @var string the plugin's settings page slug */
	private $page_id;

	/** @var string the plugin's settings option key name */
	private $option_key;

	/** @var array settings options */
	private $options;


	/**
	 * Registers settings and admin menu.
	 *
	 * @since 1.6.0
	 */
	public function __construct() {

		$this->page_id    = str_replace( '-', '_', Plugin::ID );
		$this->option_key = Settings::get_settings_key();
		$this->options    = Settings::get_settings();

		add_action( 'admin_menu', function() { $this->register_settings_page(); } );
		add_action( 'admin_init', function() { $this->register_settings(); } );
	}


	/**
	 * Adds a plugin Settings menu.
	 *
	 * @since 1.6.0
	 */
	private function register_settings_page() {

		add_options_page(
			Plugin::NAME,
			Plugin::NAME,
			'manage_options',
			$this->page_id,
			[ $this, 'output_settings_page' ]
		);
	}


	/**
	 * Registers the plugin settings.
	 *
	 * @since 1.6.0
	 */
	private function register_settings() {

		register_setting(
			$this->option_key,
			$this->option_key,
			[ $this, 'sanitize_field' ]
		);

		add_settings_section(
			$this->option_key,
			__( 'Settings', 'wp-php-console' ),
			[ $this, 'output_settings_instructions' ],
			$this->page_id
		);

		$settings_fields = [
			'password' => [
				'label' => esc_html__( 'Password', 'wp-php-console' ),
				'args'  => [
					'id'       => 'password',
					'type'     => 'password',
					'required' => true,
				]
			],
			'ssl' => [
				'label' => esc_html__( 'Allow only on SSL', 'wp-php-console' ),
				'args'  => [
					'id'   => 'ssl',
					'type' => 'checkbox',
				]
			],
			'ip' => [
				'label' => esc_html__( 'Allowed IP Masks', 'wp-php-console' ),
				'args'  => [
					'id'   => 'ip',
					'type' => 'text',
				]
			],
			'register' => [
				'label' => esc_html__( 'Register PC Class', 'wp-php-console' ),
				'args'  => [
					'id'   => 'register',
					'type' => 'checkbox',
				]
			],
			'stack' => [
				'label' => esc_html__( 'Show Call Stack', 'wp-php-console' ),
				'args'  => [
					'id'   => 'stack',
					'type' => 'checkbox',
				]
			],
			'short' => [
				'label' => esc_html__( 'Short Path Names', 'wp-php-console' ),
				'args'  => [
					'id'   => 'short',
					'type' => 'checkbox',
				]
			],
		];

		foreach ( $settings_fields as $key => $field ) {
			add_settings_field(
				$this->page_id . '['. $key . ']',
				$field['label'],
				[ $this, 'output_input_field' ],
				$this->page_id,
				$this->option_key,
				$field['args']
			);
		}
	}


	/**
	 * Outputs settings page additional info.
	 *
	 * Prints more details on the plugin settings page.
	 *
	 * @internal callback method
	 *
	 * @since 1.6.0
	 */
	public function output_settings_instructions() {

		?>
		<p><?php printf(
				/* translators: Placeholder: %s refers to the PHP Console library, pointing to its GitHub repository */
				_x( 'This plugin allows you to use %s within your WordPress installation for testing, debugging and development purposes.', 'PHP Console, the PHP Library', 'wp-php-console' ),
				'<a href="' . esc_url( Plugin::get_php_console_repository_url() ) . '" target="_blank">PHP Console</a>'
			);
		?></p>
		<h4><?php esc_html_e( 'Usage instructions:', 'wp-php-console' ); ?></h4>
		<ol>
			<?php

			$instructions = [
				sprintf(
					/* translators: Placeholder: %s - the Google Chrome PHP Console extension download link */
					_x( 'Make sure you have downloaded and installed the %s.', 'PHP Console, the Chrome Extension', 'wp-php-console' ),
					/* translators: Placeholder: %s - PHP Console extension name */
					'<a href="' . esc_url( Plugin::get_php_console_chrome_extension_web_store_url() ) . '" target="_blank">' . sprintf( __( '%s extension for Google Chrome', 'wp-php-console' ), 'PHP Console' ) . '</a>'
				),
				sprintf(
					/* translators: Placeholders: %1$s - opening PHP <a> link tag, %2$s - closing PHP </a> link tag */
					__( 'If the Chrome extension is unavailable from the web store, you may %1$sdownload and install it from the source%2$s.', 'wp-php-console' ),
					'<a href="' . esc_url( Plugin::get_php_console_chrome_extension_repository_url() ) . '" target="_blank">',
					'</a>'
				),
				esc_html__( 'Set a password for the eval terminal in the options below and hit "Save Changes".', 'wp-php-console' ),
				esc_html__( 'Reload any page of your installation and click on the key icon in your Chrome browser address bar, enter your password and access the terminal.', 'wp-php-console' ),
				esc_html__( 'From the eval terminal you can execute any PHP or WordPress specific function, including functions from your plugins and active theme.', 'wp-php-console' ),
				sprintf(
					/* translators: Placeholders: %1$s - PHP code snippet example, %2$s - Chrome javascript console shortcut */
					__( 'In your PHP code, you can call PHP Console debug statements like %1$s to display PHP variables in the browser\'s JavaScript-console (e.g. %2$s) and optionally filter selected tags through the browser\'s Remote PHP Eval Terminal screen\'s "Ignore Debug options".', 'wp-php-console' ),
					'<code>debug(&#36;var, &#36;tag)</code>',
					'<code>CTRL+SHIFT+J</code>'
				),
			];

			foreach ( $instructions as $list_item ) :
				?><li><?php echo $list_item; ?></li><?php
			endforeach;

			?>
		</ol>
		<hr>
		<?php
	}


	/**
	 * Gets the field ID.
	 *
	 * @since 1.6.0
	 *
	 * @param array $field field arguments
	 * @return string
	 */
	private function get_field_id( array $field ) {

		return $this->page_id . '-' . $field['id'];
	}


	/**
	 * Gets the field name.
	 *
	 * @since 1.6.0
	 *
	 * @param array $field field arguments
	 * @return string
	 */
	private function get_field_name( array $field ) {

		return str_replace( '-', '_', $this->page_id . '[' . $field['id'] . ']' );
	}


	/**
	 * Gets the field current value.
	 *
	 * @since 1.6.0
	 *
	 * @param array $field field arguments
	 * @return int|string|bool
	 */
	private function get_field_value( array $field ) {

		return $this->options[ $field['id'] ];
	}


	/**
	 * Outputs an input field.
	 *
	 * @internal callback method
	 *
	 * @since 1.6.0
	 *
	 * @param array $args
	 */
	public function output_input_field( array $args = [] ) {

		if ( empty( $args ) ) {
			return;
		}

		switch ( $args['type'] ) {
			case 'password' :
			case 'text' :
				$this->output_input_text_field( $args );
				break;
			case 'checkbox' :
				$this->output_checkbox_field( $args );
			break;
		}
	}


	/**
	 * Outputs a text input field.
	 *
	 * @since 1.6.0
	 *
	 * @param array $args
	 */
	private function output_input_text_field( $args = [] ) {

		?>
		<label>
			<input
				type="<?php echo isset( $args['type'] ) ? esc_attr( $args['type'] ) : 'text'; ?>"
				id="<?php echo esc_attr( $this->get_field_id( $args ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( $args ) ); ?>"
				value="<?php echo esc_attr( $this->get_field_value( $args ) ); ?>"
			>
			<?php if ( ! empty( $args['required'] ) ) : ?>
				<span style="color:red;" title="<?php esc_attr_e( 'Required', 'wp-php-console' ); ?>">*</span>
			<?php endif; ?>
		</label>
		<?php

		switch ( $args['id'] ) :

			case 'ip' :
				$this->output_ip_field_instructions();
				break;

			case 'password' :
				$this->output_password_field_instructions();
				break;

		endswitch;
	}


	/**
	 * Outputs the "Password" field instructions.
	 *
	 * @since 1.6.0
	 */
	private function output_password_field_instructions() {

		?>
		<p class="description"><?php esc_html_e( 'The password for the eval terminal. If empty, the connector will not work.', 'wp-php-console' ); ?></p>
		<?php
	}


	/**
	 * Outputs the "IP range" field instructions.
	 *
	 * @since 1.6.0
	 */
	private function output_ip_field_instructions() {

		?>
		<p class="description"><?php esc_html_e( 'You may specify any of the following, to give access to specific IPs to the eval terminal:', 'wp-php-console' ); ?></p>
		<ol>
			<li><span class="description"><?php printf(
					/* translators: Placeholders: %1$s - a single IP address, %2$s link to Varying Vagrant Vagrants project repository */
					__( 'An IP address (for example %1$s, %2$s default IP address).', 'wp-php-console' ),
					'<code>192.168.50.4</code>',
					'<a href="https://github.com/Varying-Vagrant-Vagrants/VVV">Varying Vagrant Vagrants</a>'
				); ?></span></li>
			<li><span class="description"><?php printf(
					/* translators: Placeholders: %1$s a range of IP addresses, %2$s - comma separated IP addresses */
					__( 'A range of addresses (%1$s) or multiple addresses, comma separated (%2$s).', 'wp-php-console' ),
					'<code>192.168.*.*</code>',
					'<code>192.168.10.25,192.168.10.28</code>'
				); ?></span></li>
		</ol>
		<?php
	}


	/**
	 * Outputs a checkbox input field.
	 *
	 * @since 1.6.0
	 *
	 * @param array $args
	 */
	public function output_checkbox_field( array $args = [] ) {

		$field_id = esc_attr( $this->get_field_id( $args ) );

		?>
		<label>
			<input
				type="checkbox"
				id="<?php echo $field_id; ?>"
				name="<?php echo esc_attr( $this->get_field_name( $args ) ); ?>"
				value="1"
				<?php checked( (bool) $this->get_field_value( $args ) ); ?>
			><?php esc_html_e( 'Yes', 'wp-php-console' ); ?>
		</label>
		<?php

		switch ( $args['id'] ) :

			case 'register' :
				$this->output_register_pc_class_field_instructions();
				break;

			case 'short' :
				$this->output_show_short_paths_field_instructions();
				break;

			case 'ssl' :
				$this->output_ssl_field_instructions();
				break;

			case 'stack' :
				$this->output_show_call_stack_field_instructions();
				break;

		endswitch;
	}


	/**
	 * Outputs the "SSL option" field instructions.
	 *
	 * @since 1.6.0
	 */
	private function output_ssl_field_instructions() {

		?>
		<p class="description"><?php esc_html_e( 'Enable this option if you want the eval terminal to work only on a SSL connection.', 'wp-php-console' ); ?></p>
		<?php
	}


	/**
	 * Outputs the "Register PC class" field instructions.
	 *
	 * @since 1.6.0
	 */
	private function output_register_pc_class_field_instructions() {

		?>
		<p class="description"><?php
			esc_html_e( 'Enable to register PC class in the global namespace.', 'wp-php-console' );
			echo '<br>';
			printf(
				/* translators: Placeholders: %1$s, %2$s and %3$s are PHP code snippets examples */
				__( 'Allows to write %1$s or %2$s instructions in PHP to inspect %3$s in the JavaScript console.', 'wp-php-console' ),
				'<code>PC::debug(&#36;var, &#36;tag)</code>',
				'<code>PC::magic_tag(&#36;var)</code>',
				'<code>&#36;var</code>'
			); ?></p>
		<?php
	}


	/**
	 * Outputs the "Show Call Stack" field instructions.
	 *
	 * @since 1.6.0
	 */
	private function output_show_call_stack_field_instructions() {

		?>
		<p class="description"><?php esc_html_e( 'Enable to see the full call stack when PHP Console writes to the browser JavaScript console.', 'wp-php-console' ); ?></p>
		<?php
	}


	/**
	 * Outputs the "Show Short Paths" field field instructions.
	 *
	 * @since 1.6.0
	 */
	private function output_show_short_paths_field_instructions() {

		?>
		<p class="description"><?php
			esc_html_e( 'Enable to shorten the length of PHP Console error sources and traces paths in browser JavaScript console for better readability.', 'wp-php-console' );
			echo '<br>';
			printf(
				/* translators: Placeholders: %1$s - long server path, %2$s - shortened server path */
				__( 'Paths like %1$s will be displayed as %2$s', 'wp-php-console' ),
				'<code>/server/path/to/document/root/WP/wp-admin/admin.php:31</code>',
				'<code>/WP/wp-admin/admin.php:31</code>'
			); ?></p>
		<?php
	}


	/**
	 * Sanitizes user input in the settings page.
	 *
	 * @internal callback method
	 *
	 * @since 1.6.0
	 *
	 * @param array $option user input
	 * @return array sanitized input
	 */
	public function sanitize_field( $option ) {

		$input = wp_parse_args( $option, [
			'ip'       => '',
			'password' => '',
			'register' => false,
			'short'    => false,
			'ssl'      => false,
			'stack'    => false,
		] );

		return [
			'ip'       => sanitize_text_field( $input['ip'] ),
			'password' => sanitize_text_field( $input['password'] ),
			'register' => ! empty( $input['register'] ),
			'short'    => ! empty( $input['short'] ),
			'ssl'      => ! empty( $input['ssl'] ),
			'stack'    => ! empty( $input['stack'] ),
		];
	}


	/**
	 * Outputs the settings page.
	 *
	 * @internal callback method
	 *
	 * @since 1.6.0
	 */
	public function output_settings_page() {

		?>
		<div class="wrap">
			<h2><?php echo Plugin::NAME; ?></h2>
			<hr />
			<form method="post" action="options.php">
				<?php

				settings_fields( $this->option_key );

				do_settings_sections( $this->page_id );

				submit_button();

				?>
			</form>
		</div>
		<?php
	}


}

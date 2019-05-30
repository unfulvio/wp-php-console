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

namespace WP_PHP_Console;

defined( 'ABSPATH' ) or exit;

/**
 * WP PHP Console settings handler.
 *
 * @since 1.5.0
 */
class Settings {


	/** @var string the plugin's settings page slug */
	private $page;

	/** @var string the plugin's settings option key name */
	private $option;

	/** @var array settings options */
	private $options;


	/**
	 * Registers settings and admin menu.
	 *
	 * @since 1.5.0
	 *
	 * @param array $options plugin settings options
	 */
	public function __construct( array $options ) {

		$this->page    = sanitize_title( strtolower( Plugin::NAME ) );
		$this->option  = str_replace( '-', '_', $this->page );
		$this->options = $options;

		add_action( 'admin_menu', [ $this, 'register_settings_page' ] );
	}


	/**
	 * Adds a plugin Settings menu.
	 *
	 * @internal action hook callback
	 *
	 * @since 1.5.0
	 */
	public function register_settings_page() {

		add_options_page(
			__( 'WP PHP Console', 'wp-php-console' ),
			__( 'WP PHP Console', 'wp-php-console' ),
			'manage_options',
			$this->page,
			[ $this, 'settings_page' ]
		);

		add_action( 'admin_init', [ $this, 'register_settings'  ] );
	}


	/**
	 * Registers the plugin settings.
	 *
	 * @internal action hook callback
	 *
	 * @since 1.5.0
	 */
	public function register_settings() {

		register_setting(
			$this->option,
			$this->option,
			[ $this, 'sanitize_field' ]
		);

		add_settings_section(
			$this->option,
			__( 'Settings', 'wp-php-console' ),
			[ $this, 'settings_info' ],
			$this->page
		);

		$settings_fields = [
			'password' => [
				 'label'    => esc_html__( 'Password',           'wp-php-console' ),
				 'callback' => [ $this, 'password_field' ],
			],
			'ssl'      => [
				 'label'    => esc_html__( 'Allow only on SSL',  'wp-php-console' ),
				 'callback' => [ $this, 'ssl_field' ],
			],
			'ip' => [
				 'label'    => esc_html__( 'Allowed IP Masks',   'wp-php-console' ),
				 'callback' => [ $this, 'ip_field' ],
			],
			'register' => [
				 'label'    => esc_html__( 'Register PC Class',  'wp-php-console' ),
				 'callback' => [ $this, 'register_field' ],
			],
			'stack'    => [
				 'label'    => esc_html__( 'Show Call Stack',    'wp-php-console' ),
				 'callback' => [ $this, 'stack_field' ],
			],
			'short'    => [
				 'label'    => esc_html__( 'Short Path Names',   'wp-php-console' ),
				 'callback' => [ $this, 'short_field' ],
			],
		];

		foreach ( $settings_fields as $key => $field ) {
			add_settings_field(
				$this->page . '['. $key . ']',
				$field['label'],
				$field['callback'],
				$this->page,
				$this->option
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
	 * @since 1.5.0
	 */
	public function settings_info() {

		?>
		<p><?php printf(
				/* translators: Placeholder: %s refers to the PHP Console library, pointing to its GitHub repository */
				_x( 'This plugin allows you to use %s within your WordPress installation for testing, debugging and development purposes.', 'PHP Console, the PHP Library', 'wp-php-console' ),
				'<a href="https://github.com/barbushin/php-console" target="_blank">PHP Console</a>'
			);
		?><br><?php esc_html_e( 'Usage instructions:', 'wp-php-console' ); ?></p>
		<ol>
			<?php

			$instructions = [
				sprintf(
					/* translators: Placeholder: %s represents the Google Chrome PHP Console extension download link */
					_x( 'Make sure you have downloaded and installed %s.', 'PHP Console, the Chrome Extension', 'wp-php-console' ),
					'<a target="_blank" href="https://chrome.google.com/webstore/detail/php-console/nfhmhhlpfleoednkpnnnkolmclajemef">PHP Console extension for Google Chrome</a>'
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
	 * Outputs the settings page "Password" field.
	 *
	 * @internal callback method
	 *
	 * @since 1.5.0
	 */
	public function password_field() {

		?>
		<input type="password" id="wp-php-console-password" name="wp_php_console[password]" value="<?php echo esc_attr( $this->options['password'] ); ?>">
		<label for="wp-php-console-password"><?php esc_html_e( 'Required', 'wp-php-console' ); ?></label><br>
		<p class="description"><?php esc_html_e( 'The password for the eval terminal. If empty, the plugin will not work.', 'wp-php-console' ); ?></p>
		<?php
	}


	/**
	 * Outputs the settings page "SSL option" field.
	 *
	 * @internal callback method
	 *
	 * @since 1.5.0
	 */
	public function ssl_field() {

		?>
		<input type="checkbox" id="wp-php-console-ssl" name="wp_php_console[ssl]" value="1" <?php checked( (bool) $this->options['ssl'] ); ?> />
		<label for="wp-php-console-ssl"><?php esc_html_e( 'Yes', 'wp-php-console' ); ?></label><br>
		<p class="description"><?php esc_html_e( 'Tick this option if you want the eval terminal to work only on a SSL connection.', 'wp-php-console' ); ?></p>
		<?php
	}


	/**
	 * Outputs the settings page "IP Range" field.
	 *
	 * @internal callback method
	 *
	 * @since 1.5.0
	 */
	public function ip_field() {

		?>
		<input type="text" class="regular-text" id="wp-php-console-ip" name="wp_php_console[ip]" value="<?php echo esc_attr( $this->options['ip'] ); ?>" />
		<label for="wp-php-console-ip"><?php esc_html_e( 'IP addresses (optional)', 'wp-php-console' ); ?></label><br>
		<p class="description"><?php esc_html_e( 'You may specify any of the following, to give access to specific IPs to the eval terminal:', 'wp-php-console' ); ?><br>
			<ol>
				<li><small><?php printf(
						/* translators: Placeholders: %1$s - a single IP address, %2$s link to Varying Vagrant Vagrants project repository */
						__( 'An IP address (for example %1$s, %2$s default IP address).', 'wp-php-console' ),
						'<code>192.168.50.4</code>',
						'<a href="https://github.com/Varying-Vagrant-Vagrants/VVV">Varying Vagrant Vagrants</a>'
					); ?></small></li>
				<li><small><?php printf(
						/* translators: Placeholders: %1$s a range of IP addresses, %2$s - comma separated IP addresses */
						__( 'A range of addresses (%1$s) or multiple addresses, comma separated (%2$s).', 'wp-php-console' ),
						'<code>192.168.*.*</code>',
						'<code>192.168.10.25,192.168.10.28</code>'
					); ?></small></li>
			</ol>
		</p>
		<?php
	}


	/**
	 * Outputs the settings page "Register PC Class" field.
	 *
	 * @internal callback method
	 *
	 * @since 1.5.0
	 */
	public function register_field() {

		?>
		<input type="checkbox" id="wp-php-console-register" name="wp_php_console[register]" value="1" <?php checked( (bool) $this->options['register'] ); ?> />
		<label for="wp-php-console-register"><?php esc_html_e( 'Yes', 'wp-php-console' ); ?></label><br>
		<p class="description"><?php
			esc_html_e( 'Tick to register PC class in the global namespace.', 'wp-php-console' );
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
	 * Outputs the settings page "Show Call Stack" field.
	 *
	 * @internal callback method
	 *
	 * @since 1.5.0
	 */
	public function stack_field() {

		?>
		<input type="checkbox" id="wp-php-console-stack" name="wp_php_console[stack]" value="1" <?php checked( (bool) $this->options['stack'] ); ?> />
		<label for="wp-php-console-stack"><?php esc_html_e( 'Yes', 'wp-php-console' ); ?></label><br />
		<p class="description"><?php esc_html_e( 'Tick to see the full call stack when PHP Console writes to the browser JavaScript console.', 'wp-php-console' ); ?></p>
		<?php
	}


	/**
	 * Outputs the settings page "Show Short Paths" field.
	 *
	 * @internal callback method
	 *
	 * @since 1.5.0
	 */
	public function short_field() {

		?>
		<input type="checkbox" id="wp-php-console-short" name="wp_php_console[short]" value="1" <?php checked( (bool) $this->options['short'] ); ?> />
		<label for="wp-php-console-short"><?php esc_html_e( 'Yes', 'wp-php-console' ); ?></label><br>
		<p class="description"><?php
			esc_html_e( 'Tick to shorten the length of PHP Console error sources and traces paths in browser JavaScript console for better readability.', 'wp-php-console' );
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
	 * Sanitize user input in settings page.
	 *
	 * @internal callback method
	 *
	 * @since 1.5.0
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

		$sanitized_input = [
			'ip'       => sanitize_text_field( $input['ip'] ),
			'password' => sanitize_text_field( $input['password'] ),
			'register' => ! empty( $input['register'] ),
			'short'    => ! empty( $input['short'] ),
			'ssl'      => ! empty( $input['ssl'] ),
			'stack'    => ! empty( $input['stack'] ),
		];

		return $sanitized_input;
	}


	/**
	 * Outputs the settings page.
	 *
	 * @internal callback method
	 *
	 * @since 1.5.0
	 */
	public function settings_page() {

		?>
		<div class="wrap">
			<h2><?php esc_html_e( 'WP PHP Console', 'wp-php-console' ); ?></h2>
			<hr />
			<form method="post" action="options.php">
				<?php

				settings_fields( $this->option );

				do_settings_sections( $this->page );

				submit_button();

				?>
			</form>
		</div>
		<?php
	}


}

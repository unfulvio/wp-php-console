<?php
/**
 * WP PHP Console Plugin Core Class
 *
 * @link    https://github.com/nekojira/wp-php-console
 * @since   1.0.0
 * @package WP_PHP_Console
 */
namespace WP_PHP_Console;

use PhpConsole;

if ( ! defined( 'WPINC' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WP PHP Console main class.
 *
 * @since   1.0.0
 * @package WP_PHP_Console
 */
class Plugin {


	/**
	 * The plugin name.
	 *
	 * @since  1.4.0
	 * @access public
	 * @var    string $plugin_name The name of this plugin.
	 */
	public $plugin_name = '';

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string $plugin_slug The string used to uniquely identify this plugin.
	 */
	public $plugin_slug = '';

	/**
	 * The current version of the plugin.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string $version The current version of the plugin.
	 */
	public $version = '';

	/**
	 * This plugin's settings options.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    array $options Array of this plugin settings options.
	 */
	protected $options = array();

	/**
	 * Instance of PHP Console connector object.
	 *
	 * @since 1.4.1
	 * @access public
	 * @var PhpConsole\Connector $connector Instance.
	 */
	public $connector = null;


	/**
	 * Load plugin and connect to PHP Console.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'WP PHP Console';
		$this->plugin_slug = 'wp-php-console';
		$this->version     = '1.4.1';
		$this->options     = $this->get_options();

		// Bail out if PHP Console can't be found
		if ( ! class_exists( 'PhpConsole\Connector' ) ) {
			return;
		}

		// Connect to PHP Console.
		$this->connect();

		// Apply PHP Console options.
		$this->apply_options();

		// Translations
		add_action( 'plugins_loaded', array( $this, 'set_locale' ) );

		// Admin menu
		add_action( 'admin_menu', array( $this, 'register_settings_page' ) );

		// Delay further PHP Console initialisation to have more context during Remote PHP execution
		add_action( 'wp_loaded', array( $this, 'init' ) );

	}


	/**
	 * Set plugin text domain.
	 *
	 * @since 1.0.0
	 */
	public function set_locale() {

		load_plugin_textdomain(
			$this->plugin_slug,
			false,
			dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}


	/**
	 * Connect to PHP Console.
	 *
	 * @since 1.4.1
	 */
	private function connect() {

		// By default PHP Console uses PhpConsole\Storage\Session for postponed responses,
		// so all temporary data will be stored in $_SESSION.
		// But there is some problem with frameworks like WordPress that override PHP session handler.
		// PHP Console has alternative storage drivers for this - we will write to a temporary file:
		$phpcdir  = dirname( __FILE__ ) . '/tmp';
		$make_dir = wp_mkdir_p( $phpcdir );

		if ( $make_dir === true ) {

			try {
				$storage = new PhpConsole\Storage\File( $phpcdir . '/' . md5( __FILE__ ) . '_pc.data' );
				PhpConsole\Connector::setPostponeStorage( $storage );
			} catch( \Exception $e ) {
				// TODO $storage is under DOCUMENT_ROOT - it's insecure but did not find another solution in WP
				// $this->print_notice_exception( $e );
			}

		}

		// Perform PHP Console initialisation required asap for other code to be able to output to the JavaScript console
		$this->connector = PhpConsole\Connector::getInstance();

	}


	/**
	 * Get WP PHP Console settings options.
	 *
	 * @since 1.4.0
	 * @return array
	 */
	protected function get_options() {
		return get_option( 'wp_php_console', array() );
	}


	/**
	 * Apply options.
	 *
	 * @since 1.4.1
	 */
	private function apply_options() {

		// Apply 'register' option to PHP Console
		if ( ! empty( $this->options['register'] ) && ! class_exists( 'PC', false ) ) {
			// Only if PC not registered yet
			try {
				PhpConsole\Helper::register();
			} catch( \Exception $e ) {
				$this->print_notice_exception( $e );
			}
		}

		// Apply 'stack' option to PHP Console
		if ( ! empty( $this->options['stack'] ) ) {
			$this->connector->getDebugDispatcher()->detectTraceAndSource = true;
		}

		// Apply 'short' option to PHP Console
		if ( ! empty( $this->options['short'] ) ) {
			try {
				$this->connector->setSourcesBasePath( $_SERVER['DOCUMENT_ROOT'] );
			} catch ( \Exception $e ) {
				$this->print_notice_exception( $e );
			}
		}

	}


	/**
	 * Initialize PHP Console.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		// Display admin notice and abort if no password has been set
		$password = isset( $this->options['password'] ) ? $this->options['password'] : '';
		if ( ! $password ) {
			add_action( 'admin_notices', array( $this, 'password_notice' ) );
			return;
		}

		// Selectively remove slashes added by WordPress as expected by PhpConsole
		if ( isset( $_POST[PhpConsole\Connector::POST_VAR_NAME] ) ) {
			$_POST[ PhpConsole\Connector::POST_VAR_NAME ] = stripslashes_deep( $_POST[ PhpConsole\Connector::POST_VAR_NAME ] );
		}

		$this->connector = PhpConsole\Connector::getInstance();

		try {
			$this->connector->setPassword( $password );
		} catch ( \Exception $e ) {
			$this->print_notice_exception( $e );
		}

		// PhpConsole instance
		$handler = PhpConsole\Handler::getInstance();

		if ( PhpConsole\Handler::getInstance()->isStarted() !== true ) {
			try {
				$handler->start();
			} catch( \Exception $e ) {
				$this->print_notice_exception( $e );
			}
		}

		// Enable SSL-only mode
		$enableSslOnlyMode = isset( $this->options['ssl'] ) ? ( ! empty( $this->options['ssl'] ) ? $this->options['ssl'] : '' ) : '';

		if ( $enableSslOnlyMode ) {
			$this->connector->enableSslOnlyMode();
		}

		// Restrict IP addresses
		$allowedIpMasks = isset( $this->options['ip'] ) ? ( ! empty( $this->options['ip'] ) ? explode( ',', $this->options['ip'] ) : '' ) : '';

		if ( is_array( $allowedIpMasks ) && ! empty( $allowedIpMasks ) ) {
			$this->connector->setAllowedIpMasks( (array) $allowedIpMasks );
		}

		$evalProvider = $this->connector->getEvalDispatcher()->getEvalProvider();

		try {
			$evalProvider->addSharedVar( 'uri', $_SERVER['REQUEST_URI'] );
		} catch ( \Exception $e ) {
			$this->print_notice_exception( $e );
		}

		try {
			$evalProvider->addSharedVarReference( 'post', $_POST );
		} catch ( \Exception $e ) {
			$this->print_notice_exception( $e );
		}

		$openBaseDirs = array( ABSPATH, get_template_directory() );

		try {
			$evalProvider->addSharedVarReference( 'dirs', $openBaseDirs );
		} catch ( \Exception $e ) {
			$this->print_notice_exception( $e );
		}

		$evalProvider->setOpenBaseDirs( $openBaseDirs );

		try {
			$this->connector->startEvalRequestsListener();
		} catch ( \Exception $e ) {
			$this->print_notice_exception( $e );
		}

	}


	/**
	 * Prints an exception message as WordPress admin notice
	 *
	 * @since 1.4.0
	 * @param \Exception $e Exception object
	 * @return void
	 */
	public function print_notice_exception( \Exception $e ) {

		add_action( 'admin_notices', function() use ( $e ) {

			?>
			<div class="error">
				<p><?php printf( '%1$s: %2$s', $this->plugin_name, $e->getMessage() ); ?></p>
			</div>
			<?php

		} );
	}



	/**
	 * Admin password notice.
	 * Prompts user to set a password for PHP Console upon plugin activation.
	 *
	 * @since   1.3.2
	 */
	public function password_notice() {

		?>
		<div class="update-nag">
			<?php

			$settings_page = esc_url( admin_url( 'options-general.php?page=wp-php-console' ) );

			/* translators: Placeholders: %1$s - opening HTML <a> link tag; %2$s closing HTML </a> link tag */
			printf( $this->plugin_name . ': ' . __( 'Please remember to %1$s set a password %2$s if you want to enable terminal.', 'wp-php-console' ), '<a href="' . $settings_page .'">', '</a>' );

			?>
		</div>
		<?php

	}


	/**
	 * Plugin Settings menu.
	 *
	 * @since 1.0.0
	 */
	public function register_settings_page() {

		add_options_page(
			__( 'WP PHP Console', 'wp-php-console' ),
			__( 'WP PHP Console', 'wp-php-console' ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'settings_page' )
		);

		add_action( 'admin_init', array( $this, 'register_settings'  ) );

	}


	/**
	 * Register plugin settings.
	 *
	 * @since 1.0.0
	 */
	public function register_settings() {

		register_setting(
			'wp_php_console',
			'wp_php_console',
			array( $this, 'sanitize_field' )
		);

		add_settings_section(
			'wp_php_console',
			__( 'Settings', 'wp-php-console' ),
			array( $this, 'settings_info' ),
			$this->plugin_slug
		);

		add_settings_field(
			'password',
			__( 'Password', 'wp-php-console' ),
			array( $this, 'password_field' ),
			$this->plugin_slug,
			'wp_php_console'
		);

		add_settings_field(
			'ssl',
			__( 'Allow only on SSL', 'wp-php-console' ),
			array( $this, 'ssl_field' ),
			$this->plugin_slug,
			'wp_php_console'
		);

		add_settings_field(
			'ip',
			__( 'Allowed IP Masks', 'wp-php-console' ),
			array( $this, 'ip_field' ),
			$this->plugin_slug,
			'wp_php_console'
		);

		add_settings_field(
			'register',
			__( 'Register PC Class ', 'wp-php-console' ),
			array( $this, 'register_field' ),
			$this->plugin_slug,
			'wp_php_console'
		);

		add_settings_field(
			'stack',
			__( 'Show Call Stack', 'wp-php-console' ),
			array( $this, 'stack_field' ),
			$this->plugin_slug,
			'wp_php_console'
		);

		add_settings_field(
			'short',
			__( 'Short Path Names', 'wp-php-console' ),
			array( $this, 'short_field' ),
			$this->plugin_slug,
			'wp_php_console'
		);

	}


	/**
	 * Settings Page Password field.
	 *
	 * @since 1.0.0
	 */
	public function password_field() {

		printf (
			'<input type="password" id="wp-php-console-password" name="wp_php_console[password]" value="%s" />',
			isset( $this->options['password'] ) ? esc_attr( $this->options['password'] ) : ''
		);
		echo '<label for="wp-php-console-ip">' . esc_html__( 'Required', 'wp-php-console' ) . '</label>';
		echo '<br />';
		echo '<small class="description">' . esc_html__( 'The password for the eval terminal. If empty, the plugin will not work.', 'wp-php-console' ) . '</small>';

	}


	/**
	 * Settings Page SSL option field.
	 *
	 * @since 1.0.0
	 */
	public function ssl_field() {

		$ssl = isset( $this->options['ssl'] ) ? esc_attr( $this->options['ssl']) : '';

		printf (
			'<input type="checkbox" id="wp-php-console-ssl" name="wp_php_console[ssl]" value="1" %s /> ',
			$ssl ? 'checked="checked"' : ''
		);
		echo '<label for="wp-php-console-ssl">' . esc_html__( 'Yes', 'wp-php-console' ) . '</label>';
		echo '<br />';
		echo '<small class="description">' . esc_html__( 'Tick this option if you want the eval terminal to work only on a SSL connection.', 'wp-php-console' ) . '</small>';

	}


	/**
	 * Settings page IP Range field.
	 *
	 * @since 1.0.0
	 */
	public function ip_field() {

		printf (
			'<input type="text" class="regular-text" id="wp-php-console-ip" name="wp_php_console[ip]" value="%s" /> ',
			isset( $this->options['ip'] ) ? esc_attr( $this->options['ip']) : ''
		);
		echo '<label for="wp-php-console-ip">' . esc_html__( 'IP addresses (optional)', 'wp-php-console' ) . '</label>';
		echo '<br />';
		/* translators: VVV Varying Vagrant Vagrants default IP address */
		printf ( __( '<small class="description">' . __( 'You may specify an IP address (e.g. <code>192.168.50.4</code>, %s default IP address), a range of addresses (<code>192.168.*.*</code>) or multiple addresses, comma separated (<code>192.168.10.25,192.168.10.28</code>) to grant access to the eval terminal.', 'wp-php-console' ) . '</small>' ), '<a href="https://github.com/Varying-Vagrant-Vagrants/VVV">Varying Vagrant Vagrants</a>' );

	}


	/**
	 * Settings page Register PC Class field.
	 *
	 * @since 1.2.4
	 */
	public function register_field() {

		$register = ! empty( $this->options['register'] );

		printf (
			'<input type="checkbox" id="wp-php-console-register" name="wp_php_console[register]" value="1" %s /> ',
			$register ? 'checked="checked"' : ''
		);
		echo '<label for="wp-php-console-register">' . esc_html__( 'Yes', 'wp-php-console' ) . '</label>';
		echo '<br />';
		echo '<small class="description">' . __( 'Choose to register PC in the global namespace. Allows to write <code>PC::debug(&#36;var, &#36;tag)</code> or <code>PC::magic_tag(&#36;var)</code> instructions in PHP to inspect <code>&#36;var</code> in the JavaScript console.', 'wp-php-console' ) . '</small>';

	}


	/**
	 * Settings page Show Call Stack field.
	 *
	 * @since 1.2.4
	 */
	public function stack_field() {

		$stack = ! empty( $this->options['stack'] );

		printf (
			'<input type="checkbox" id="wp-php-console-stack" name="wp_php_console[stack]" value="1" %s /> ',
			$stack ? 'checked="checked"' : ''
		);
		echo '<label for="wp-php-console-stack">' . esc_html__( 'Yes', 'wp-php-console' ) . '</label>';
		echo '<br />';
		echo '<small class="description">' . __( "Choose to also see the call stack when PHP Console writes to the browser's JavaScript-console.", 'wp-php-console' ) . '</small>';

	}


	/**
	 * Settings page Show Short Paths field.
	 *
	 * @since 1.2.4
	 */
	public function short_field() {

		$short = ! empty( $this->options['short'] );

		printf (
			'<input type="checkbox" id="wp-php-console-short" name="wp_php_console[short]" value="1" %s /> ',
			$short ? 'checked="checked"' : ''
		);
		echo '<label for="wp-php-console-short">' . esc_html__( 'Yes', 'wp-php-console' ) . '</label>';
		echo '<br />';
		echo '<small class="description">' . __( "Choose to shorten PHP Console error sources and traces paths in browser's JavaScript-console. Paths like <code>/server/path/to/document/root/WP/wp-admin/admin.php:31</code> will be displayed as <code>/W/wp-admin/admin.php:31</code>", 'wp-php-console' ) . '</small>';

	}


	/**
	 * Sanitize user input in settings page.
	 *
	 * @since  1.0.0
	 * @param  string $input user input
	 * @return array  sanitized inputs
	 */
	public function sanitize_field( $input ) {

			$sanitized_input = array();

			if ( isset( $input['password'] ) ) {
				$sanitized_input['password'] = sanitize_text_field( $input['password'] );
			}

			if ( isset( $input['ssl'] ) ) {
				$sanitized_input['ssl'] = ! empty( $input['ssl'] ) ? 1 : '';
			}

			if ( isset( $input['ip'] ) ) {
				$sanitized_input['ip']  = sanitize_text_field( $input['ip'] );
			}

			$sanitized_input['register'] = empty( $input['register'] ) ? '' : 1;
			$sanitized_input['stack']    = empty( $input['stack'] )    ? '' : 1;
			$sanitized_input['short']    = empty( $input['short'] )    ? '' : 1;

			return $sanitized_input;
	}


	/**
	 * Settings page.
	 *
	 * @since 1.0.0
	 */
	public function settings_page() {

		?>
		<div class="wrap">
			<h2><?php esc_html_e( 'WP PHP Console', 'wp-php-console' ); ?></h2>
			<hr />
			<form method="post" action="options.php">
				<?php
				settings_fields( 'wp_php_console' );
				do_settings_sections( $this->plugin_slug );
				submit_button();
				?>
			</form>
		</div>
		<?php

	}


	/**
	 * Settings page additional info.
	 * Prints more details on the plugin settings page.
	 *
	 * @since 1.3.0
	 */
	public function settings_info() {

		?>
		<p><?php /* translators: %s refers to 'PHP Console' Chrome extension, will print download link for the Chrome extension */
			printf( _x( 'This plugin allows you to use %s within your WordPress installation for testing, debugging and development purposes.<br/>Usage instructions:', 'PHP Console, the Chrome Extension', 'wp-php-console' ), '<a href="https://github.com/barbushin/php-console" target="_blank">PHP Console</a>' ); ?></p>
		<ol>
			<li><?php /* translators: Install PHP Console extension for Google Chrome download link */
				printf( _x( 'Make sure you have downloaded and installed %s.', 'PHP Console, the Chrome Extension', 'wp-php-console' ), '<a target="_blank" href="https://chrome.google.com/webstore/detail/php-console/nfhmhhlpfleoednkpnnnkolmclajemef">PHP Console extension for Google Chrome</a>' ); ?></li>
			<li><?php _e( 'Set a password for the eval terminal in the options below and hit <code>save changes</code>.', 'wp-php-console' ); ?></li>
			<li><?php _e( 'Reload any page of your installation and click on the key icon in your Chrome browser address bar, enter your password and access the terminal.', 'wp-php-console' ); ?></li>
			<li><?php _e( 'From the eval terminal you can execute any PHP or WordPress specific function, including functions from your plugins and active theme.', 'wp-php-console' ); ?></li>
			<li><?php _e( "In your PHP code, you can call PHP Console debug statements like <code>debug(&#36;var, &#36;tag)</code> to display PHP variables in the browser's JavaScript-console (<code>Ctrl Shift J </code>) and optionally filter selected tags through the browser's Remote PHP Eval Terminal screen's Ignore Debug options.", 'wp-php-console' ); ?></li>
		</ol>
		<?php

	}


}

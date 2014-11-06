<?php

/**
 * WP PHP Console Plugin Core Class
 *
 * @link       https://github.com/wp-php-console
 * @since      1.0.0
 *
 * @package    WP_PHP_Console
 * @subpackage WP_PHP_Console/lib
 */

/**
 * WP PHP Console.
 *
 * @since      1.0.0
 * @package    WP_PHP_Console
 * @subpackage WP_PHP_Console/lib
 */
class WP_PHP_Console {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;


	private $options;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the Dashboard and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'wp-php-console';
		$this->version = '1.0.0';

		register_activation_hook( __FILE__, 'activate' );

		add_action( 'plugins_loaded',   array( $this, 'set_locale' ) );
		add_action( 'admin_menu',       array( $this, 'register_settings_page' ) );
		add_action( 'admin_init',       array( $this, 'register_settings' ) );
		add_action( 'plugins_loaded',   array( $this, 'init' ) );
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Set plugin text domain.
	 *
	 * @since   1.0.0
	 */
	public function set_locale() {
		load_plugin_textdomain(
			$this->get_plugin_name(),
			false,
			dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Fired upon plugin activation.
	 *
	 * @since   1.0.0
	 */
	public function activate() {

	}

	/**
	 * Plugin Settings menu.
	 *
	 * @since   1.0.0
	 */
	public function register_settings_page() {

		add_options_page(
			__( 'WP PHP Console', $this->get_plugin_name() ),
			__( 'PHP Console', $this->get_plugin_name() ),
			'manage_options',
			$this->get_plugin_name(),
			array( $this, 'settings_page' )
		);

		add_action( 'admin_init', array( $this, 'register_settings'  ) );
	}

	/**
	 * Register plugin settings.
	 *
	 * @since   1.0.0
	 */
	function register_settings() {

		register_setting(
			'wp_php_console',
			'wp_php_console',
			array( $this, 'sanitize_field' )
		);

		add_settings_section(
			'wp_php_console',
			__( 'Settings', $this->get_plugin_name() ),
			array( $this, 'settings_info' ),
			$this->get_plugin_name()
		);

		add_settings_field(
			'password',
			__( 'Password', $this->get_plugin_name() ),
			array( $this, 'password_field' ),
			$this->get_plugin_name(),
			'wp_php_console'
		);

		add_settings_field(
			'ssl',
			__( 'Allow only on SSL', $this->get_plugin_name() ),
			array( $this, 'ssl_field' ),
			$this->get_plugin_name(),
			'wp_php_console'
		);

		add_settings_field(
			'ip',
			__( 'Allowed IP Masks', $this->get_plugin_name() ),
			array( $this, 'ip_field' ),
			$this->get_plugin_name(),
			'wp_php_console'
		);

	}

	/**
	 * Settings Page Password field.
	 *
	 * @since   1.0.0
	 */
	public function password_field() {

		printf (
			'<input type="password" id="wp-php-console-password" name="wp_php_console[password]" value="%s" />',
			isset( $this->options['password'] ) ? esc_attr( $this->options['password']) : ''
		);
		echo '<label for="wp-php-console-ip">' . __( 'Required', $this->get_plugin_name() ) . '</label>';
		echo '<br />';
		echo '<small class="description">' . __( 'The password for eval terminal. If empty, the plugin will not work.', $this->get_plugin_name() ) . '</small>';
	}

	/**
	 * Settings Page SSL option field.
	 *
	 * @since   1.0.0
	 */
	public function ssl_field() {

		$ssl = isset( $this->options['ssl'] ) ? esc_attr( $this->options['ssl']) : '';

		printf (
			'<input type="checkbox" id="wp-php-console-ssl" name="wp_php_console[ssl]" value="1" %s /> ',
			$ssl ? 'checked="checked"' : ''
		);
		echo '<label for="wp-php-console-ssl">' . __( 'Yes (optional)', $this->get_plugin_name() ) . '</label>';
		echo '<br />';
		echo '<small class="description">' . __( 'Choose if you want the eval terminal to work only on a SSL connection.', $this->get_plugin_name() ) . '</small>';
	}

	/**
	 * Settings page IP Range field.
	 *
	 * @since   1.0.0
	 */
	public function ip_field() {

		printf (
			'<input type="text" class="regular-text" id="wp-php-console-ip" name="wp_php_console[ip]" value="%s" /> ',
			isset( $this->options['ip'] ) ? esc_attr( $this->options['ip']) : ''
		);
		echo '<label for="wp-php-console-ip">' . __( 'IP addresses (optional)', $this->get_plugin_name() ) . '</label>';
		echo '<br />';
		echo '<small class="description">' . __( 'You may specify an IP address (e.g. 192.169.1.50), a range of addresses (192.168.*.*) or multiple addresses, comma separated (192.168.10.25,192.168.10.28) to grant access to eval terminal.', $this->get_plugin_name() ) . '</small>';
	}

	/**
	 * Sanitize user input in settings page.
	 *
	 * @since   1.0.0
	 *
	 * @param   string  $input  user input
	 *
	 * @return  array   sanitized inputs
	 */
	public function sanitize_field( $input ) {

			$sanitized_input = array();

			if ( isset( $input['password'] ) )
				$sanitized_input['password'] = sanitize_text_field( $input['password'] );

			if ( isset( $input['ssl'] ) )
				$sanitized_input['ssl'] = ! empty( $input['ssl'] ) ? 1 : '';

			if ( isset( $input['ip'] ) )
				$sanitized_input['ip'] = sanitize_text_field( $input['ip'] );

			return $sanitized_input;
	}

	/**
	 * Settings page.
	 *
	 * @since   1.0.0
	 */
	function settings_page() {

		$this->options = get_option( 'wp_php_console' );
		?>
		<div class="wrap">
			<h2><?php _e( 'WP PHP Console', $this->get_plugin_name() ); ?></h2>
			<hr />
			<form method="post" action="options.php">
				<?php
				settings_fields( 'wp_php_console' );
				do_settings_sections( $this->get_plugin_name() );
				submit_button();
				?>
			</form>
		</div>
		<?php

	}

	public function settings_info() {

	?>
	<p><?php _e( 'This plugin embeds PHP Console in your WordPress installation. Instructions:', $this->get_plugin_name() ); ?></p>
	<ol>
		<li><?php printf( __( 'Make sure you have downloaded and installed %s.', $this->get_plugin_name() ), '<a target="_blank" href="https://chrome.google.com/webstore/detail/php-console/nfhmhhlpfleoednkpnnnkolmclajemef">PHP Console extension for Google Chrome</a>' ); ?></li>
		<li><?php _e( 'Set a password for the eval terminal in the options below and hit `save changes`.', $this->get_plugin_name() ); ?></li>
		<li><?php _e( 'Reload any page of your installation and click on the key icon in your Chrome browser address bar, enter your password and access the terminal.', $this->get_plugin_name() ); ?></li>
		<li><?php _e( 'From the eval terminal you can execute any PHP or WordPress specific function, including functions from your plugins and active theme.', $this->get_plugin_name() ); ?></li>
	</ol>
	<?php

	}

	/**
	 * Initialize PHP Console.
	 *
	 * @since   1.0.0
	 */
	public function init() {

		$options = get_option( 'wp_php_console' );

		$password = isset( $options['password'] ) ? $options['password'] : '';
		if ( ! $password )
			return;

		$connector = PhpConsole\Connector::getInstance();
		$connector->setPassword( $password );

		$handler = PhpConsole\Handler::getInstance();
		if ( PhpConsole\Handler::getInstance()->isStarted() != true )
			$handler->start();

		$enableSslOnlyMode = isset( $options['ssl'] ) ? $options['ssl'] : '';
		if ( $enableSslOnlyMode == true )
			$connector->enableSslOnlyMode();

		$allowedIpMasks = isset( $options['ip'] ) ? explode( ',', $options['ip'] ) : '';
		if ( $allowedIpMasks )
			$connector->setAllowedIpMasks( (array) $allowedIpMasks );

		$evalProvider = $connector->getEvalDispatcher()->getEvalProvider();

		$evalProvider->addSharedVar( 'uri', $_SERVER['REQUEST_URI'] );
		$evalProvider->addSharedVarReference( 'post', $_POST );

		// $evalProvider->disableFileAccessByOpenBaseDir();
		$openBaseDirs = array( ABSPATH, get_template_directory() );
		$evalProvider->addSharedVarReference( 'dirs', $openBaseDirs );
   	    $evalProvider->setOpenBaseDirs( $openBaseDirs );

		$connector->startEvalRequestsListener();

	}

}
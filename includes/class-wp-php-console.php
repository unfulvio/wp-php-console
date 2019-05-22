<?php

namespace WP_PHP_Console;

use PhpConsole;

defined( 'ABSPATH' ) or exit;

/**
 * WP PHP Console main class.
 *
 * @since 1.0.0
 */
class Plugin {


	/** @var string plugin version */
	CONST VERSION = '1.5.3';

	/** @var string plugin name */
	CONST NAME = 'WP PHP Console';


	/** @var array settings options */
	protected $options = [];

	/** @var PhpConsole\Connector instance */
	public $connector;


	/**
	 * Loads plugin and connects to PHP Console.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// handle translations
		add_action( 'plugins_loaded', [ $this, 'set_locale' ] );

		// set options
		$this->options = $this->get_options();

		// load admin
		$this->set_admin();

		// bail out if PHP Console can't be found
		if ( ! class_exists( 'PhpConsole\Connector' ) ) {
			return;
		}

		// connect to PHP Console
		add_action( 'init',      [ $this, 'connect' ], -1000 );
		// delay further PHP Console initialisation to have more context during Remote PHP execution
		add_action( 'wp_loaded', [ $this, 'init' ], -1000 );
	}


	/**
	 * Sets plugin text domain.
	 *
	 * @since 1.0.0
	 */
	public function set_locale() {

		load_plugin_textdomain(
			'wp-php-console',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}


	/**
	 * Loads admin.
	 *
	 * @since 1.5.0
	 */
	private function set_admin() {

		if ( ! defined( 'DOING_AJAX' ) && is_admin() ) {

			// add a settings link to the plugins admin screen
			$plugin_name = str_replace( 'includes/class-', '', plugin_basename( __FILE__ ) );
			add_filter( "plugin_action_links_{$plugin_name}", static function( $actions ) {
				return array_merge( [
					'<a href="' . esc_url( admin_url( 'options-general.php?page=wp-php-console' ) ) . '">' . __( 'Settings', 'wp-php-console' ) . '</a>',
				], $actions );
			} );

			// init settings
			require_once __DIR__ . '/class-wp-php-console-settings.php';

			new Settings( $this->options );
		}
	}


	/**
	 * Connects to PHP Console.
	 *
	 * PHP Console needs to hook in session, in WordPress we need to be in 'init':
	 * @link http://silvermapleweb.com/using-the-php-session-in-wordpress/
	 * @internal action hook callback
	 *
	 * @since 1.4.0
	 */
	public function connect() {

		if ( ! @session_id() ) {
			@session_start();
		}

		if ( ! $this->connector instanceof PhpConsole\Connector ) {
			try {
				$this->connector = PhpConsole\Connector::getInstance();
			} catch ( \Exception $e ) {
				return;
			}
		}

		// apply PHP Console options
		$this->apply_options();
	}


	/**
	 * Get WP PHP Console settings options.
	 *
	 * @since  1.4.0
	 *
	 * @return array
	 */
	protected function get_options() {

		$options = get_option( 'wp_php_console', [] );

		return wp_parse_args( $options, [
			'ip'       => '',
			'password' => '',
			'register' => false,
			'short'    => false,
			'ssl'      => false,
			'stack'    => false,
		] );
	}


	/**
	 * Applies options.
	 *
	 * @since 1.4.0
	 */
	private function apply_options() {

		// bail out if not connected yet to PHP Console
		if ( ! $this->connector instanceof PhpConsole\Connector ) {
			return;
		}

		// apply 'register' option to PHP Console...
		if ( true === $this->options['register'] && ! class_exists( 'PC', false ) ) {
			// ...only if PC not registered yet
			try {
				PhpConsole\Helper::register();
			} catch( \Exception $e ) {
				$this->print_notice_exception( $e );
			}
		}

		// apply 'stack' option to PHP Console
		if ( true === $this->options['stack'] ) {
			$this->connector->getDebugDispatcher()->detectTraceAndSource = true;
		}

		// apply 'short' option to PHP Console
		if ( true === $this->options['short'] ) {
			try {
				$this->connector->setSourcesBasePath( $_SERVER['DOCUMENT_ROOT'] );
			} catch ( \Exception $e ) {
				$this->print_notice_exception( $e );
			}
		}
	}


	/**
	 * Initializes PHP Console.
	 *
	 * @internal action hook callback
	 *
	 * @since 1.0.0
	 */
	public function init() {

		// get PHP Console extension password
		$password = trim( $this->options['password'] );

		if ( empty( $password ) ) {

			// display admin notice and abort if no password has been set
			add_action( 'admin_notices', [ $this, 'password_notice' ] );
			return;
		}

		// selectively remove slashes added by WordPress as expected by PHP Console
		if ( array_key_exists( PhpConsole\Connector::POST_VAR_NAME, $_POST ) ) {
			$_POST[ PhpConsole\Connector::POST_VAR_NAME ] = stripslashes_deep( $_POST[ PhpConsole\Connector::POST_VAR_NAME ] );
		}

		// get PHP Console instance if wasn't set yet
		if ( ! $this->connector instanceof PhpConsole\Connector ) {

			try {
				$this->connector = PhpConsole\Connector::getInstance();
			} catch ( \Exception $e ) {
				return;
			}
		}

		// set PHP Console password
		try {
			$this->connector->setPassword( $password );
		} catch ( \Exception $e ) {
			$this->print_notice_exception( $e );
		}

		// get PHP Console handler instance
		$handler = PhpConsole\Handler::getInstance();

		if ( true !== PhpConsole\Handler::getInstance()->isStarted() ) {
			try {
				$handler->start();
			} catch( \Exception $e ) {
				$this->print_notice_exception( $e );
				return;
			}
		}

		// enable SSL-only mode
		if ( true === $this->options['ssl'] ) {
			$this->connector->enableSslOnlyMode();
		}

		// restrict IP addresses
		$allowedIpMasks = ! empty( $this->options['ip'] ) ? explode( ',', $this->options['ip'] ) : '';

		if ( is_array( $allowedIpMasks ) && count( $allowedIpMasks ) > 0 ) {
			$this->connector->setAllowedIpMasks( $allowedIpMasks );
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

		$openBaseDirs = [ ABSPATH, get_template_directory() ];

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
	 * Prints an exception message as WordPress admin notice.
	 *
	 * @since 1.4.0
	 *
	 * @param \Exception $e Exception object
	 */
	public function print_notice_exception( \Exception $e ) {

		add_action( 'admin_notices', static function() use ( $e ) {

			?>
			<div class="error">
				<p><?php printf( '%1$s: %2$s', self::NAME, $e->getMessage() ); ?></p>
			</div>
			<?php

		} );
	}



	/**
	 * Admin password notice.
	 *
	 * Prompts user to set a password for PHP Console upon plugin activation.
	 *
	 * @internal action hook callback
	 *
	 * @since 1.3.2
	 */
	public function password_notice() {

		?>
		<div class="update-nag">
			<p><?php printf(
				/* translators: Placeholders: %1$s - WP Php Console name, %2$s - opening HTML <a> link tag; %3$s closing HTML </a> link tag */
				__( '%1$s: Please remember to %2$sset a password%3$s if you want to enable the terminal.', 'wp-php-console' ),
				'<strong>' . self::NAME . '</strong>',
				'<a href="' . esc_url( admin_url( 'options-general.php?page=wp-php-console' ) ) .'">',
				'</a>'
			); ?></p>
		</div>
		<?php
	}


}

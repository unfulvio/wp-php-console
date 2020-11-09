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

use PhpConsole;

defined( 'ABSPATH' ) or exit;

/**
 * WP PHP Console main class.
 *
 * @since 1.0.0
 */
class Plugin {


	/** @var string plugin version */
	CONST VERSION = '1.6.0';

	/** @var string plugin ID */
	CONST ID = 'wp-php-console';

	/** @var string plugin name */
	CONST NAME = 'WP PHP Console';


	/** @var PhpConsole\Connector instance */
	public $connector;


	/**
	 * Loads plugin and connects to PHP Console.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		@error_reporting( E_ALL );

		foreach ( [ 'WP_DEBUG',	'WP_DEBUG_LOG', 'WP_DEBUG_DISPLAY', ] as $wp_debug_constant ) {
			if ( ! defined( $wp_debug_constant ) ) {
				define ( $wp_debug_constant, true );
			}
		}

		// handle translations
		add_action( 'plugins_loaded', static function() {
			load_plugin_textdomain(
				'wp-php-console',
				false,
				dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
			);
		} );

		if ( class_exists( 'PhpConsole\Connector' ) ) {
			// connect to PHP Console
			add_action( 'init',      [ $this, 'connect' ], -1000 );
			// delay further PHP Console initialisation to have more context during Remote PHP execution
			add_action( 'wp_loaded', [ $this, 'init' ], -1000 );
		}

		// load admin
		if ( is_admin() ) {
			new Admin();
		}
	}


	/**
	 * Connects to PHP Console.
	 *
	 * PHP Console needs to hook in session, in WordPress we need to be in 'init':
	 * @link http://silvermapleweb.com/using-the-php-session-in-wordpress/
	 *
	 * @internal action hook callback
	 *
	 * @since 1.4.0
	 */
	public function connect() {

		// workaround for avoiding headers already sent warnings
		@error_reporting( E_ALL & ~E_WARNING );

		if ( ! @session_id() ) {
			@session_start();
		}

		$connected = true;

		if ( ! $this->connector instanceof PhpConsole\Connector ) {
			try {
				$this->connector = PhpConsole\Connector::getInstance();
			} catch ( \Exception $e ) {
				$connected = false;
			}
		}

		// restore error reporting
		@error_reporting( E_ALL );

		// apply PHP Console options
		if ( $connected ) {
			$this->apply_options();
		}
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
		if ( Settings::should_register_pc_class() && ! class_exists( 'PC', false ) ) {
			// ...only if PC not registered yet
			try {
				PhpConsole\Helper::register();
			} catch( \Exception $e ) {
				$this->print_notice_exception( $e );
			}
		}

		// apply 'stack' option to PHP Console
		if ( Settings::should_show_call_stack() ) {
			$this->connector->getDebugDispatcher()->detectTraceAndSource = true;
		}

		// apply 'short' option to PHP Console
		if ( Settings::should_use_short_path_names() ) {
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

		// bail if no password is set to connect with PHP Console
		if ( ! Settings::has_eval_terminal_password() ) {
			return;
		}

		// selectively remove slashes added by WordPress as expected by PHP Console
		if ( array_key_exists( PhpConsole\Connector::POST_VAR_NAME, $_POST ) ) {
			$_POST[ PhpConsole\Connector::POST_VAR_NAME ] = stripslashes_deep( $_POST[ PhpConsole\Connector::POST_VAR_NAME ] );
		}

		// get PHP Console instance if wasn't set yet
		if ( ! $this->connector instanceof PhpConsole\Connector ) {

			// workaround for avoiding headers already sent warnings
			@error_reporting( E_ALL & ~E_WARNING );

			try {
				$this->connector = PhpConsole\Connector::getInstance();
				$connected       = true;
			} catch ( \Exception $e ) {
				$connected       = false;
			}

			// restore error reporting
			@error_reporting( E_ALL );

			if ( ! $connected ) {
				return;
			}
		}

		// set PHP Console password
		try {
			$this->connector->setPassword( Settings::get_eval_terminal_password() );
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
		if ( Settings::should_use_ssl_only() ) {
			$this->connector->enableSslOnlyMode();
		}

		// restrict IP addresses
		$allowedIpMasks = Settings::get_allowed_ip_masks();

		if ( count( $allowedIpMasks ) > 0 ) {
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
	private function print_notice_exception( \Exception $e ) {

		add_action( 'admin_notices', static function() use ( $e ) {
			?>
			<div class="error">
				<p><?php printf( '%1$s: %2$s', self::NAME, $e->getMessage() ); ?></p>
			</div>
			<?php
		} );
	}


	/**
	 * Gets the plugin path.
	 *
	 * @since 1.6.0
	 *
	 * @return string
	 */
	public static function get_plugin_path() {

		return untrailingslashit( dirname( __DIR__ ) );
	}


	/**
	 * Gets the plugin vendor path.
	 *
	 * @since 1.6.0
	 */
	public static function get_plugin_vendor_path() {

		return self::get_plugin_path() . '/vendor';
	}


	/**
	 * Gets the plugin page URL.
	 *
	 * @since 1.6.0
	 *
	 * @return string
	 */
	public static function get_plugin_page_url() {

		return 'https://wordpress.org/support/plugin/wp-php-console/';
	}


	/**
	 * Gets the plugin reviews page URL.
	 *
	 * @since 1.6.0
	 *
	 * @return string
	 */
	public static function get_reviews_page_url() {

		return 'https://wordpress.org/support/plugin/wp-php-console/reviews/';
	}


	/**
	 * Gets the plugin support page URL.
	 *
	 * @since 1.6.0
	 *
	 * @return string
	 */
	public static function get_support_page_url() {

		return 'https://wordpress.org/support/plugin/wp-php-console/';
	}


	/**
	 * Gets the GitHub repository page URL.
	 *
	 * @since 1.6.0
	 *
	 * @return string
	 */
	public static function get_wp_php_console_repository_url() {

		return 'https://github.com/unfulvio/wp-php-console';
	}


	/**
	 * Gets the PHP Console project page URL.
	 *
	 * @since 1.6.0
	 *
	 * @return string
	 */
	public static function get_php_console_repository_url() {

		return 'https://github.com/barbushin/php-console';
	}


	/**
	 * Gets the PHP Console Google Chrome extension URL.
	 *
	 * @since 1.6.0
	 *
	 * @return string
	 */
	public static function get_php_console_chrome_extension_web_store_url() {

		return 'https://chrome.google.com/webstore/detail/php-console/nfhmhhlpfleoednkpnnnkolmclajemef';
	}


	/**
	 * Gets the PHP Console Google Chrome extension repository URL.
	 *
	 * @since 1.6.0
	 *
	 * @return string
	 */
	public static function get_php_console_chrome_extension_repository_url() {

		return 'https://github.com/barbushin/php-console-extension';
	}


}

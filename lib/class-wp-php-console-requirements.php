<?php
/**
 * WP Requirements
 *
 * Utility to check current PHP version, WordPress version and PHP extensions.
 *
 * @package WP_Requirements
 * @version 1.0.0
 * @author  Fulvio Notarstefano <fulvio.notarstefano@gmail.com>
 * @link    https://github.com/nekojira/wp-requirements
 * @license GPL2+
 */

if ( ! class_exists( 'WP_PHP_Console_Requirements' ) ) {

	class WP_PHP_Console_Requirements {

		/**
		 * WordPress.
		 *
		 * @access private
		 * @var bool
		 */
		private $wp = true;

		/**
		 * PHP.
		 *
		 * @access private
		 * @var bool
		 */
		private $php = true;

		/**
		 * PHP Extensions.
		 *
		 * @access private
		 * @var bool
		 */
		private $extensions = true;

		/**
		 * Results failures.
		 *
		 * Associative array with requirements results.
		 *
		 * @access private
		 * @var array
		 */
		private $failures = array();

		/**
		 * Constructor.
		 *
		 * @param array $requirements Associative array with requirements.
		 */
		public function __construct( $requirements ) {

			if ( $requirements && is_array( $requirements ) ) {

				$errors       = array();
				$requirements = array_merge( array( 'wp' => '', 'php' => '', 'extensions' => '' ), (array) $requirements );

				// Check for WordPress version.
				if ( $requirements['wp'] && is_string( $requirements['wp'] ) ) {
					global $wp_version;
					// If $wp_version isn't found or valid probably you are not running WordPress (properly)?
					$wp_ver = $wp_version && is_string( $wp_version ) ? $wp_version : $requirements['wp'];
					$wp_ver = version_compare( $wp_ver, $requirements['wp'] );
					if ( $wp_ver === -1 ) {
						$errors['wp'] = $wp_version;
						$this->wp = false;
					}
				}

				// Check fo PHP version.
				if ( $requirements['php'] && is_string( $requirements['php'] ) ) {
					$php_ver = version_compare( PHP_VERSION, $requirements['php'] );
					if ( $php_ver === -1 ) {
						$errors['php'] = PHP_VERSION;
						$this->php = false;
					}
				}

				// Check fo PHP Extensions.
				if ( $requirements['extensions'] && is_array( $requirements['extensions'] ) ) {
					$extensions = array();
					foreach ( $requirements['extensions'] as $extension ) {
						if ( $extension && is_string( $extension ) ) {
							$extensions[ $extension ] = extension_loaded( $extension );
						}
					}
					if ( in_array( false, $extensions ) ) {
						foreach ( $extensions as $extension_name => $found  ) {
							if ( $found === false ) {
								$errors['extensions'][ $extension_name ] = $extension_name;
							}
						}
						$this->extensions = false;
					}
				}

				$this->failures = $errors;

			} else {

				trigger_error( 'WP Requirements: the requirements are invalid.', E_USER_ERROR );

			}

		}

		/**
		 * Get requirements results.
		 *
		 * @return array
		 */
		public function failures() {
			return $this->failures;
		}

		/**
		 * Check if versions check pass.
		 *
		 * @return bool
		 */
		public function pass() {
			if ( in_array( false, array( $this->wp, $this->php, $this->extensions ) ) ) {
				return false;
			}
			return true;
		}

	}

}

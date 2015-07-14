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

if ( ! class_exists( 'WP_Requirements' ) ) {

	class WP_Requirements {

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
		private $ext = true;

		/**
		 * Errors.
		 *
		 * An array of errors to display if any of the checks do not pass.
		 *
		 * @access private
		 * @var array
		 */
		private $errors = array();

		/**
		 * Constructor.
		 *
		 * @param array $requirements Associative array with required things.
		 * @param array $messages Associative array with messages to display (optional, to override default ones).
		 */
		public function __construct( $requirements, $messages = array() ) {

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
						if ( isset( $messages['wp'] ) ) {
							$errors[] = $messages['wp'];
						} else {
							$errors[] = sprintf( 'The minimum WordPress version required is %1$s, WordPress version found: %2$s', '`' . $requirements['wp'] . '`', '`' . $wp_version . '`' );
						}
						$this->wp = false;
					}

				}

				// Check fo PHP version.
				if ( $requirements['php'] && is_string( $requirements['php'] ) ) {

					$php_ver = version_compare( PHP_VERSION, $requirements['php'] );

					if ( $php_ver === -1 ) {
						if ( isset( $messages['wp'] ) ) {
							$errors[] = $messages['wp'];
						} else {
							$errors[] = sprintf( 'The minimum PHP version required is %1$s, PHP version found: %2$s', '`' . $requirements['php'], '`' . PHP_VERSION . '``' );
						}
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
						foreach ( $extensions as $extension ) {
							if ( $extension === false ) {
								if ( isset( $messages[ $extension ] ) ) {
									$errors[] = $messages[ $extension ];
								} else {
									$errors[] = sprintf( 'The PHP extension %s is required and was not found', '`' . $extension . '`' );
								}
							}
						}
						$this->ext = false;
					}
				}

				$this->errors = $errors;

			} else {
				trigger_error( 'WP Requirements: the requirements passed as argument are invalid.', E_USER_ERROR );
			}

		}

		/**
		 * Get errors.
		 *
		 * @return array
		 */
		public function errors() {
			return $this->errors;
		}

		/**
		 * Check if versions check pass.
		 *
		 * @return bool
		 */
		public function pass() {
			if ( in_array( false, array( $this->wp, $this->php, $this->ext ) ) ) {
				return false;
			}
			return true;
		}

	}

}
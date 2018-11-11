<?php
/**
 * Plugin base class.
 *
 * @package rest-xmlrpc-data-checker
 * @author Enrico Sorcinelli
 */

// Check running WordPress instance.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.1 404 Not Found' );
	exit();
}

/**
 * Plugin base class.
 */
class REST_XMLRPC_Data_Checker {

	/**
	 * Prefix used for options and postmeta fields, DOM IDs and DB tables.
	 *
	 * @var string
	 */
	private static $prefix = 'rest_xmlrpc_data_checker_';

	/**
	 * Instance of this class.
	 *
	 * @var object
	 */
	public static $instance;

	/**
	 * Instance settings.
	 *
	 * @var array
	 */
	private $settings = array();

	/**
	 * Plugin settings.
	 *
	 * @var array
	 */
	private $plugin_settings;

	/**
	 * Admin object.
	 *
	 * @var \REST_XMLRPC_Data_Checker\Admin
	 */
	private $_admin;

	/**
	 * REST object.
	 *
	 * @var \REST_XMLRPC_Data_Checker\Rest
	 */
	private $_rest;

	/**
	 * XML-RPC object.
	 *
	 * @var \REST_XMLRPC_Data_Checker\XMLRPC
	 */
	private $_xmlrpc;

	/**
	 * Plugin class constructor.
	 *
	 * @param array $args {
	 *     Arguments list.
	 *     @type boolean $debug Default value is `false`.
	 * }
	 *
	 *  @return REST_XMLRPC_Data_Checker
	 */
	public function __construct( $args = array() ) {

		$this->settings = wp_parse_args(
			array(
				'debug' => false,
			),
			$args
		);

		// Load plugin text domain.
		load_plugin_textdomain( 'rest-xmlrpc-data-checker', false, dirname( plugin_basename( __FILE__ ) ) . '/../languages/' );

		// Plugin Utils class.
		require_once REST_XMLRPC_DATA_CHECKER_BASEDIR . '/php/class-rest-xmlrpc-data-checker-utils.php';

		// Get plugin settings.
		$this->plugin_settings = $this->get_plugin_default_settings();

		// Create plugin Admin object.
		if ( is_admin() ) {
			require_once REST_XMLRPC_DATA_CHECKER_BASEDIR . '/php/class-rest-xmlrpc-data-checker-admin.php';
			$this->_admin = new \REST_XMLRPC_Data_Checker\Admin(
				array(
					'prefix'          => self::$prefix,
					'debug'           => $this->settings['debug'],
					'plugin_settings' => $this->plugin_settings,
				)
			);
		}

		// Add custom XML-RPC server.
		if ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) {

			require_once REST_XMLRPC_DATA_CHECKER_BASEDIR . '/php/class-rest-xmlrpc-data-checker-xmlrpc.php';
			$this->_xmlrpc = new \REST_XMLRPC_Data_Checker\XMLRPC(
				array(
					'prefix'          => self::$prefix,
					'debug'           => $this->settings['debug'],
					'plugin_settings' => $this->plugin_settings,
				)
			);
		}

		// Create plugin REST object.
		require_once REST_XMLRPC_DATA_CHECKER_BASEDIR . '/php/class-rest-xmlrpc-data-checker-rest.php';
		$this->_rest = new \REST_XMLRPC_Data_Checker\Rest(
			array(
				'prefix'          => self::$prefix,
				'debug'           => $this->settings['debug'],
				'plugin_settings' => $this->plugin_settings,
			)
		);
	}

	/**
	 * Get the singleton instance of this class.
	 *
	 * @param array $args Constructor arguments list.
	 *
	 * @return object
	 */
	public static function get_instance( $args = array() ) {
		if ( ! ( self::$instance instanceof self ) ) {
			self::$instance = new self( $args );
		}
		return self::$instance;
	}

	/**
	 * Get plugin options settings.
	 *
	 * @return array
	 */
	private function get_plugin_default_settings() {

		$settings = \REST_XMLRPC_Data_Checker\Utils::wp_parse_args_recursive(
			get_option( self::$prefix . 'settings', array() ),
			array(
				'rest'    => array(
					'url_prefix'             => rest_get_url_prefix(),
					'disable'                => false,
					'disable_jsonp'          => false,
					'auth_method'            => 'none',
					'apply_trusted_networks' => false,
					'trusted_networks'       => '',
					'apply_allowed_routes'   => false,
					'allowed_routes'         => array(),
				),
				'xmlrpc'  => array(
					'disable'                => false,
					'apply_trusted_users'    => false,
					'trusted_users'          => '',
					'apply_trusted_networks' => false,
					'trusted_networks'       => '',
					'apply_allowed_methods'  => false,
					'allowed_methods'        => array(),
				),
				'options' => array(
					'remove_plugin_settings' => false,
				),
			)
		);

		/**
		 * Filter plugin settings.
		 *
		 * @param array $post_types Post types.
		 *
		 * @return array
		 */
		$settings = apply_filters( self::$prefix . 'settings', $settings );

		return $settings;
	}

	/**
	 * Force refresh of plugin settings.
	 *
	 * @return array
	 */
	public static function refresh_plugin_settings() {

		$rest_xmlrpc_data_checker = self::get_instance();

		$rest_xmlrpc_data_checker->plugin_settings = $rest_xmlrpc_data_checker->get_plugin_default_settings();

		if ( $rest_xmlrpc_data_checker->_admin instanceof \REST_XMLRPC_Data_Checker\Admin ) {
			$rest_xmlrpc_data_checker->_admin->plugin_settings = $rest_xmlrpc_data_checker->plugin_settings;
		}

		if ( $rest_xmlrpc_data_checker->_xmlrpc instanceof \REST_XMLRPC_Data_Checker\XMLRPC ) {
			$rest_xmlrpc_data_checker->_xmlrpc->plugin_settings = $rest_xmlrpc_data_checker->plugin_settings;
		}

		if ( $rest_xmlrpc_data_checker->_rest instanceof \REST_XMLRPC_Data_Checker\Rest ) {
			$rest_xmlrpc_data_checker->_rest->plugin_settings = $rest_xmlrpc_data_checker->plugin_settings;
		}

		return $rest_xmlrpc_data_checker->plugin_settings;
	}

	/**
	 * Plugin uninstall hook.
	 *
	 * @return void
	 */
	public static function plugin_uninstall() {
		$options = get_option( self::$prefix . 'settings', true );
		if ( isset( $options['options'] ) && ! empty( $options['options']['remove_plugin_settings'] ) ) {
			delete_option( self::$prefix . 'settings' );
		}
	}
}

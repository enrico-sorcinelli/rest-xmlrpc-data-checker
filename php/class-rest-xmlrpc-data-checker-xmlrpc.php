<?php
/**
 * Plugin XML-RPC class.
 *
 * @package rest-xmlrpc-data-checker
 * @author Enrico Sorcinelli
 */

namespace REST_XMLRPC_Data_Checker;

// Check running WordPress instance.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.1 404 Not Found' );
	exit();
}

/**
 * XMLRPC class.
 */
class XMLRPC {

	/**
	 * Prefix.
	 *
	 * @var string  $prefix
	 */
	private $prefix;

	/**
	 * Plugin settings.
	 *
	 * @var array
	 */
	public $plugin_settings;

	/**
	 * Construct the plugin.
	 *
	 * @param array $args {
	 *     Arguments list.
	 *     @type string  $prefix
	 *     @type boolean $debug
	 * }
	 */
	public function __construct( $args = array() ) {

		// Set object property.
		$this->debug = isset( $args['debug'] ) ? $args['debug'] : false;
		foreach ( array( 'prefix', 'plugin_settings' ) as $property ) {
			$this->$property = $args[ $property ];
		}

		if ( ! class_exists( 'IXR_Client' ) ) {
			include_once ABSPATH . WPINC . '/class-IXR.php';
		}
		if ( ! class_exists( 'wp_xmlrpc_server' ) ) {
			include_once ABSPATH . WPINC . '/class-wp-xmlrpc-server.php';
		}
		require_once REST_XMLRPC_DATA_CHECKER_BASEDIR . '/php/class-rest-xmlrpc-data-checker-wp-xmlrpc-server.php';

		add_filter( 'wp_xmlrpc_server_class', array( $this, 'wp_xmlrpc_server_class_name' ), 10, 1 );

		// Check XML-RPC enabled users.
		add_filter( 'wp_authenticate_user', array( $this, 'check_xmlrpc_enabled_user' ), 10, 2 );

		// Enable/disable XML-RPC.
		add_filter( 'xmlrpc_enabled', array( $this, 'check_xmlrpc_enabled' ), 99 );

		// Check for unallowed methods.
		add_filter( 'xmlrpc_methods', array( $this, 'check_xmlrpc_methods' ), 99 );

		if ( $this->plugin_settings['rest']['remove_xmlrpc_rsd_apis'] ) {
			remove_action( 'xmlrpc_rsd_apis', 'rest_output_rsd' );
		}

		// Filter XML-RPC post result.
		add_filter( 'xmlrpc_prepare_post', array( $this, 'filter_xmlrpc_prepare_post' ), 10, 3 );
	}

	/**
	 * `wp_xmlrpc_server_class` filter hook to override XMLRPC server class name.
	 *
	 * @param string $class XMLRPC server class name.
	 *
	 * @return string
	 */
	public function wp_xmlrpc_server_class_name( $class ) {
		return 'REST_XMLRPC_Data_Checker\\XMLRPC_Server';
	}

	/**
	 * `wp_authenticate_user` filter hook to check XML-RPC enabled users.
	 *
	 * @param \WP_User $user     User data.
	 * @param string   $password User password.
	 *
	 * @return \WP_User|\WP_Error
	 */
	public function check_xmlrpc_enabled_user( $user, $password ) {

		// Apply check on setting basis.
		if ( $this->plugin_settings['xmlrpc']['apply_trusted_users'] ) {

			// Check cap.
			$enabled = user_can( $user, $this->prefix . 'xmlrpc_enable' );

			if ( empty( $enabled ) ) {
				return new \WP_Error( 'invalid_xmlrpc_user', 'invalid_xmlrpc_user' );
			}
		}

		return $user;
	}

	/**
	 * `xmlrpc_methods` filter hook that removes unallowed methods.
	 *
	 * @param array $methods XML-RPC available methods.
	 *
	 * @return array
	 */
	public function check_xmlrpc_methods( $methods ) {

		// Apply check on setting basis.
		if ( $this->plugin_settings['xmlrpc']['apply_allowed_methods'] ) {

			// Remove all unallowed methods.
			$methods = array_intersect_key( $methods, array_flip( $this->plugin_settings['xmlrpc']['allowed_methods'] ) );
		}

		return $methods;
	}

	/**
	 * 'xmlrpc_enabled' filter hook. Check remote IP over trusted IP list
	 * and turn on/off XML-RPC interface accordingly.
	 *
	 * @return boolean
	 */
	public function check_xmlrpc_enabled() {

		// Check XML-RPC enable.
		if ( $this->plugin_settings['xmlrpc']['disable'] ) {
			return false;
		}

		// Apply check access list on setting basis.
		if ( $this->plugin_settings['xmlrpc']['apply_trusted_networks'] && ! empty( $this->plugin_settings['xmlrpc']['trusted_networks'] ) ) {

			// Get IP/Networks.
			$trusted_networks = preg_split( '/(\s|,|;)+/', trim( \REST_XMLRPC_Data_Checker\Utils::strip_comments( array( 'string' => $this->plugin_settings['xmlrpc']['trusted_networks'] ) ) ) );

			if ( ! \REST_XMLRPC_Data_Checker\Utils::check_network( \REST_XMLRPC_Data_Checker\Utils::get_remote_ip( $this->plugin_settings['options']['check_forwarded_remote_ip'] ), $trusted_networks ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Filter XML-RPC post result according to plugin settings.
	 *
	 * @since 1.3.1
	 *
	 * @param array $_post  Array of modified post data.
	 * @param array $post   Array of original post data.
	 * @param array $fields Array of post fields.
	 *
	 * @return array
	 */
	public function filter_xmlrpc_prepare_post( $_post, $post, $fields ) {

		// Check for fields post data.
		if ( in_array( 'post', $fields, true ) ) {

			// Apply default WordPress rendering to post_content.
			if ( $this->plugin_settings['xmlrpc']['process_post_content'] ) {
				$_post['post_content'] = apply_filters( 'the_content', $_post['post_content'] );
			}

			// Restore original post_status value.
			if ( $this->plugin_settings['xmlrpc']['restore_original_post_status'] ) {
				$_post['post_status'] = $post['post_status'];
			}
		}

		return $_post;
	}

}

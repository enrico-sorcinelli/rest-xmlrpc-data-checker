<?php
/**
 * Plugin administration REST class.
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
 * Rest class.
 */
class Rest {

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
	 *
	 * @return Rest
	 */
	public function __construct( $args = array() ) {

		// Set object property.
		$this->debug = isset( $args['debug'] ) ? $args['debug'] : false;
		foreach ( array( 'prefix', 'plugin_settings' ) as $property ) {
			$this->$property = $args[ $property ];
		}

		// Add authentication filter.
		add_filter( 'rest_authentication_errors', array( $this, 'check_rest_api' ), 10 );

		// Filter rest URL prefix.
		if ( ! empty( $this->plugin_settings['rest']['url_prefix'] ) ) {
			add_filter( 'rest_url_prefix', array( $this, 'filter_rest_url_prefix' ) );
		}

		// Add JSONP support filter.
		add_filter( 'rest_jsonp_enabled', array( $this, 'filter_rest_jsonp_enabled' ) );

		// Remove REST API link tag.
		if ( ! empty( $this->plugin_settings['rest']['remove_link_tag'] ) ) {
			remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
		}

		// Remove REST API link in HTTP headers.
		if ( ! empty( $this->plugin_settings['rest']['remove_link_http_headers'] ) ) {
			remove_action( 'template_redirect', 'rest_output_link_header', 11, 0 );
		}

		// Remove oEmbed Discovery Links.
		if ( ! empty( $this->plugin_settings['rest']['remove_oembed_discovery_links'] ) ) {
			remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );
		}
	}

	/**
	 * `rest_authentication_errors` hook to authentication for current request.
	 *
	 * @param \WP_Error|null|boolean $result Current authentication error.
	 *
	 * @return \WP_Error|null|boolean
	 */
	public function check_rest_api( $result ) {

		global $wp;

		// REST request has been already authenticated.
		if ( ! empty( $result ) && true === $result ) {
			return $result;
		}

		// User is already logged or disable REST option is off.
		if ( empty( $this->plugin_settings['rest']['disable'] ) || is_user_logged_in() ) {
			return $result;
		}

		// Authentication plugin checks default to false.
		$result_check = false;

		// WP_Error arguments.
		$error = array(
			'code'    => 'rest_not_logged_in',
			'message' => __( 'Authenticated users only can access to the REST API.', 'rest-xmlrpc-data-checker' ),
			'data'    => array( 'status' => rest_authorization_required_code() ),
		);

		// Check Basic Auth login method.
		if ( 'basic_auth' === $this->plugin_settings['rest']['auth_method'] ) {

			// Check that we're trying to authenticate.
			if ( ! isset( $_SERVER['PHP_AUTH_USER'] ) || ! isset( $_SERVER['PHP_AUTH_PW'] ) ) {
				$error['message'] = __( 'Incorrect username or password.', 'rest-xmlrpc-data-checker' );
				$result_check     = false;
			}
			else {

				$user = wp_authenticate( $_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'] ); // WPCS: sanitization ok.

				// Login OK.
				if ( ! is_wp_error( $user ) && user_can( $user, $this->prefix . 'rest_enable' ) ) {
					wp_set_current_user( $user->ID );
					$result_check = true;
				}
				else {
					$error['message'] = __( 'Incorrect username or password.', 'rest-xmlrpc-data-checker' );
					$result_check     = false;
				}
			}
		}

		// Check access list.
		if ( ( $result_check || 'none' === $this->plugin_settings['rest']['auth_method'] )
			&& $this->plugin_settings['rest']['apply_trusted_networks']
			&& ! empty( $this->plugin_settings['rest']['trusted_networks'] )
		) {

			// Get IP/Networks.
			$trusted_networks = preg_split( '/(\s|,|;)+/', trim( \REST_XMLRPC_Data_Checker\Utils::strip_comments( array( 'string' => $this->plugin_settings['rest']['trusted_networks'] ) ) ) );

			// Remote IP unallowed.
			if ( false === \REST_XMLRPC_Data_Checker\Utils::check_network( \REST_XMLRPC_Data_Checker\Utils::get_remote_ip( $this->plugin_settings['options']['check_forwarded_remote_ip'] ), $trusted_networks ) ) {
				$result_check = false;
				$error        = array(
					'code'    => 'rest_not_allowed',
					'message' => __( 'Forbidden REST API request.', 'rest-xmlrpc-data-checker' ),
					'data'    => array( 'status' => 403 ),
				);
			}
			else {
				$result_check = true;
			}
		}

		// Try to check whitelisted routes.
		if ( ( $result_check || 'none' === $this->plugin_settings['rest']['auth_method'] )
			&& $this->plugin_settings['rest']['apply_allowed_routes']
			&& ! empty( $this->plugin_settings['rest']['allowed_routes'] )
		) {
			$current_rest_route = $wp->query_vars['rest_route'];

			$result_check = false;
			$error        = array(
				'code'    => 'rest_no_route',
				'message' => __( 'No route was found matching the URL and request method.', 'rest-xmlrpc-data-checker' ),
				'data'    => array( 'status' => 404 ),
			);
			foreach ( $this->plugin_settings['rest']['allowed_routes'] as $route ) {
				if ( (bool) preg_match( '@^' . $route . '$@i', $current_rest_route ) ) {
					$result_check = true;
					break;
				}
			}
		}

		// Authentication checks failed.
		if ( false === $result_check ) {

			if ( is_wp_error( $result ) ) {
				$result->add( $error['code'], $error['message'], $error['data'] );
			} else {
				$result = new \WP_Error( $error['code'], $error['message'], $error['data'] );
			}

			/**
			 * Filters the REST authentication error.
			 *
			 * @param \WP_Error $result Result.
			 *
			 * @return \WP_Error|boolean|null
			 */
			$result = apply_filters( $this->prefix . 'rest_error', $result );
		}

		return $result;
	}

	/**
	 * `rest_url_prefix` filter hook.
	 *
	 * @param string $prefix REST url prefix.
	 *
	 * @return string
	 */
	public function filter_rest_url_prefix( $prefix ) {
		return $this->plugin_settings['rest']['url_prefix'];
	}

	/**
	 * `rest_jsonp_enabled` filter hook.
	 *
	 * @param boolena $enabled JSONP support enabled.
	 *
	 * @return boolean
	 */
	public function filter_rest_jsonp_enabled( $enabled ) {
		return empty( $this->plugin_settings['rest']['disable_jsonp'] );
	}
}

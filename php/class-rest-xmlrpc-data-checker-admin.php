<?php
/**
 * Plugin administration class.
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
 * Admin interface class.
 */
class Admin {

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
	 * Admin pages screen names.
	 *
	 * @var array
	 */
	private $admin_pages = array();

	/**
	 * Construct the plugin.
	 *
	 * @param array $args {
	 *     Arguments list.
	 *     @type string  $prefix
	 *     @type boolean $debug
	 * }
	 *
	 * @return object
	 */
	public function __construct( $args = array() ) {

		// Set object property.
		$this->debug = isset( $args['debug'] ) ? $args['debug'] : false;
		foreach ( array( 'prefix', 'plugin_settings' ) as $property ) {
			$this->$property = $args[ $property ];
		}

		// This plugin only runs in the admin, but we need it initialized on init.
		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Initialize the plugin: setup menu, settings, add filters, actions,
	 * scripts, styles and so on.
	 *
	 * @return void
	 */
	public function init() {

		if ( ! is_admin() ) {
			return;
		}

		require_once REST_XMLRPC_DATA_CHECKER_BASEDIR . '/php/class-rest-xmlrpc-data-checker-users-wp-list-table.php';

		// Menu settings.
		add_action( 'admin_menu', array( $this, 'setup_menu' ), '10.1' );

		// Forms settings.
		add_action( 'admin_init', array( $this, 'setup_settings' ), '10.1' );
	}

	/**
	 * Setup admin menu.
	 *
	 * @return void
	 */
	public function setup_menu() {

		$admin_menu_capability = is_super_admin() ? 'manage_options' : $this->prefix . 'manage_admin_menu';

		/**
		 * Filter to allow display plugin settings page.
		 *
		 * @param boolean $display_setting_page Default `true`.
		 *
		 * @return boolean
		 */
		if ( apply_filters( $this->prefix . 'admin_settings', true ) ) {
			$this->admin_pages['settings'] = add_options_page( __( 'REST XML-RPC Data Checker', 'rest-xmlrpc-data-checker' ), __( 'REST XML-RPC Data Checker', 'rest-xmlrpc-data-checker' ), $admin_menu_capability, $this->prefix . 'settings', array( $this, 'page_settings' ) );
		}
	}

	/**
	 * Setup admin (register settings, enque JavaScript, CSS, add filters &
	 * actions, ...).
	 *
	 * @return void
	 */
	public function setup_settings() {
		global $wp_version;

		// Enqueue JS/CSS only for non AJAX requests.
		if ( ! \REST_XMLRPC_Data_Checker\Utils::is_ajax_request() ) {

			// Screens where to enqueue assets.
			$admin_pages = array_merge( array(), array_values( $this->admin_pages ) );

			// Add CSS to post pages.
			foreach ( $admin_pages as $page ) {
				add_action( 'admin_print_styles-' . $page, array( $this, 'load_css' ), 10, 0 );
				add_action( 'admin_print_scripts-' . $page, array( $this, 'load_javascript' ), 10, 0 );

				// Adds help_tab when settings page loads.
				add_action( 'load-' . $page, array( $this, 'add_help_tab' ), 100 );
			}
		}

		// General settings.
		register_setting( $this->prefix . 'settings', $this->prefix . 'settings', array( $this, 'check_plugin_settings' ) );

		// Add XML-RPC setting on user profile.
		if ( current_user_can( 'edit_users' ) ) {
			add_action( 'show_user_profile', array( $this, 'extra_user_profile_fields' ) );
			add_action( 'edit_user_profile', array( $this, 'extra_user_profile_fields' ) );

			add_action( 'personal_options_update', array( $this, 'save_extra_user_profile_fields' ) );
			add_action( 'edit_user_profile_update', array( $this, 'save_extra_user_profile_fields' ) );
		}

		/** This filter is documented in php/class-rest-xmlrpc-data-checker-admin.php */
		if ( apply_filters( $this->prefix . 'admin_settings', true ) ) {
			add_filter( 'plugin_action_links_rest-xmlrpc-data-checker/rest-xmlrpc-data-checker.php', array( $this, 'plugin_actions' ), 10, 4 );
		}

		add_action( 'update_option_' . $this->prefix . 'settings', array( $this, 'update_caps' ), 10, 3 );
		add_action( 'add_option_' . $this->prefix . 'settings', array( $this, 'update_caps' ), 10, 2 );

		add_filter( 'wp_redirect', array( $this, 'set_active_tab_wp_redirect' ), 10, 2 );
	}

	/**
	 * Add link to settng page in plugins list.
	 *
	 * @param array  $actions     Actions.
	 * @param string $plugin_file Plugin filename.
	 * @param array  $plugin_data Plugin data.
	 * @param string $context     Context.
	 *
	 * @return array
	 */
	public function plugin_actions( $actions, $plugin_file, $plugin_data, $context ) {
		array_unshift( $actions, '<a href="' . menu_page_url( $this->prefix . 'settings', false ) . '">' . esc_html__( 'Settings', 'rest-xmlrpc-data-checker' ) . '</a>' );
		return $actions;
	}

	/**
	 * Check general settings.
	 *
	 * @param mixed $settings Settings values.
	 *
	 * @return array
	 */
	public function check_plugin_settings( $settings ) {
		return $settings;
	}

	/**
	 * Load CSS files.
	 *
	 * @return void
	 */
	public function load_css() {
		global $post_type;

		wp_enqueue_style(
			$this->prefix . 'css',
			REST_XMLRPC_DATA_CHECKER_BASEURL . '/assets/css/admin.css',
			array(),
			REST_XMLRPC_DATA_CHECKER_VERSION,
			'screen'
		);
	}

	/**
	 * Load JavaScript files.
	 *
	 * @return void
	 */
	public function load_javascript() {
		global $post_type;

		wp_enqueue_script(
			$this->prefix . 'js',
			REST_XMLRPC_DATA_CHECKER_BASEURL . '/assets/js/admin.js',
			array(),
			REST_XMLRPC_DATA_CHECKER_VERSION,
			false
		);

		// Localization.
		wp_localize_script(
			$this->prefix . 'js',
			$this->prefix . 'i18n',
			array(
				'_nonces'     => $this->create_nonces(),
				'_plugin_url' => REST_XMLRPC_DATA_CHECKER_BASEURL,
				'msgs'        => array(),
				'prefix'      => $this->prefix,
			)
		);
	}

	/**
	 * Create all needed nonces.
	 *
	 * @param integer $post_id Current post ID (needed for custom lock dialog).
	 *
	 * @return array
	 */
	private function create_nonces( $post_id = '' ) {
		return array();
	}

	/**
	 * General settings admin page callback.
	 *
	 * @return void
	 */
	public function page_settings() {

		include_once ABSPATH . WPINC . '/class-IXR.php';
		include_once ABSPATH . WPINC . '/class-wp-xmlrpc-server.php';

		// Get XML-RPC known methods.
		$xmlrpc         = new \wp_xmlrpc_server();
		$xmlrpc_methods = array_keys( $xmlrpc->methods );
		sort( $xmlrpc_methods );
		unset( $xmlrpc );

		$xmlrpc_methods_items = array();
		foreach ( $xmlrpc_methods as $item ) {
			$ns                               = preg_split( '/\./', $item );
			$xmlrpc_methods_items[ $ns[0] ][] = $item;
		}

		// Get REST known routes.
		$rest_server     = rest_get_server();
		$rest_namespaces = $rest_server->get_namespaces();
		$rest_routes     = array_keys( $rest_server->get_routes() );
		sort( $rest_routes );
		$rest_routes_items = array();

		$ns = '/';
		foreach ( $rest_routes as $route ) {
			if ( '/' === $route ) {
				$rest_routes_items[ $route ][] = $route;
				continue;
			}
			if ( in_array( ltrim( $route, '/' ), $rest_namespaces, true ) ) {
				$ns = ltrim( $route, '/' );
			}
			$rest_routes_items[ $ns ][] = $route;
		}

		// Get users with caps.
		$users = $this->get_users(
			array(
				'cap' => array(
					$this->prefix . 'rest_enable',
					$this->prefix . 'xmlrpc_enable',
				),
			)
		);

		\REST_XMLRPC_Data_Checker\Utils::include_template(
			REST_XMLRPC_DATA_CHECKER_BASEDIR . '/php/adminpages/settings.php',
			array(
				'prefix'            => $this->prefix,
				'settings'          => $this->plugin_settings,
				'xmlrpc_methods'    => $xmlrpc_methods_items,
				'rest_routes'       => $rest_routes_items,
				'xmlrpc_users_list' => \REST_XMLRPC_Data_Checker\Utils::sinclude_template(
					REST_XMLRPC_DATA_CHECKER_BASEDIR . '/php/adminpages/users-list.php',
					array(
						'users'       => $users,
						'prefix'      => $this->prefix,
						'cb_name'     => $this->prefix . 'settings[xmlrpc][trusted_users][]',
						'cb_meta_key' => $this->prefix . 'xmlrpc_enable',
					)
				),
				'rest_users_list'   => \REST_XMLRPC_Data_Checker\Utils::sinclude_template(
					REST_XMLRPC_DATA_CHECKER_BASEDIR . '/php/adminpages/users-list.php',
					array(
						'users'       => $users,
						'prefix'      => $this->prefix,
						'cb_name'     => $this->prefix . 'settings[rest][trusted_users][]',
						'cb_meta_key' => $this->prefix . 'rest_enable',
					)
				),
			)
		);
	}

	/**
	 * Add section in user profile page.
	 *
	 * @param \WP_User $user The WP_User object of the user being edited.
	 */
	public function extra_user_profile_fields( $user ) {
		\REST_XMLRPC_Data_Checker\Utils::include_template(
			REST_XMLRPC_DATA_CHECKER_BASEDIR . '/php/adminpages/user-fields.php',
			array(
				'prefix'   => $this->prefix,
				'user'     => $user,
				'settings' => $this->plugin_settings,
			)
		);
	}

	/**
	 * Update user caps from profile.
	 *
	 * @param integer $user_id The user ID of the user being edited.
	 */
	public function save_extra_user_profile_fields( $user_id ) {

		// Only worry about if the user has access.
		if ( ! current_user_can( 'edit_users' ) ) {
			return;
		}

		// Get user.
		$editing_user = get_user_by( 'id', $user_id );

		// Update 'rest_enable' and 'xmlrpc_enable' cap.
		foreach ( array( 'rest_enable', 'xmlrpc_enable' ) as $cap ) {
			if ( ! empty( $_REQUEST[ $this->prefix . $cap ] ) ) { // WPCS: XSS ok, sanitization ok.
				$editing_user->add_cap( $this->prefix . $cap );
			} else {
				$editing_user->remove_cap( $this->prefix . $cap );
			}
		}
	}

	/**
	 * Add hash to redirect url after saving in order to set active tab.
	 *
	 * @param string  $location Redirect Location.
	 * @param integer $status   Redirect status.
	 *
	 * @return string
	 */
	public function set_active_tab_wp_redirect( $location, $status ) {
		global $pagenow;
		if ( 'options.php' === $pagenow && isset( $_REQUEST['option_page'] ) && $this->prefix . 'settings' === $_REQUEST['option_page'] ) {
			$location .= ( isset( $_REQUEST[ $this->prefix . 'active_tab' ] ) ? '#' . $_REQUEST[ $this->prefix . 'active_tab' ] : '' );
		}
		return $location;
	}

	/**
	 * Get user based on XML-RPC/REST caps.
	 *
	 * @param array $args {
	 *     Arguments list.
	 *     @type array  $query_args
	 *     @type array  $cap
	 * }
	 *
	 * @return array
	 */
	public function get_users( $args = array() ) {

		$args = \REST_XMLRPC_Data_Checker\Utils::wp_parse_args_recursive(
			$args,
			array(
				'cap'        => array(),
				'query_args' => array(
					'fields' => 'all',
					'number' => -1,
				),
			)
		);

		$wp_user_query = get_users( $args['query_args'] );

		$users = array();
		foreach ( $wp_user_query as $user ) {
			$role_list = array();
			foreach ( $user->roles as $role ) {
				if ( isset( wp_roles()->role_names[ $role ] ) ) {
					$role_list[ $role ] = translate_user_role( wp_roles()->role_names[ $role ] );
				}
			}
			if ( empty( $role_list ) ) {
				$role_list['none'] = _x( 'None', 'no user roles' );
			}
			$meta = array();
			foreach ( $args['cap'] as $cap ) {
				$meta[ $cap ] = user_can( $user, $cap );
			}
			$users[] = array(
				'data'  => $user->data,
				'meta'  => $meta,
				'roles' => implode( ', ', $role_list ),
			);
		}

		return $users;
	}

	/**
	 * Update REST/XML-RPC user capabilities.
	 *
	 * @param array  $old    Old values.
	 * @param array  $new    New values.
	 * @param string $option Option name.
	 */
	public function update_caps( $old, $new, $option = '' ) {

		// Only worry about if the user has access.
		if ( ! current_user_can( 'edit_users' ) ) {
			return;
		}

		// Get XML-RPC-enabled users.
		global $wpdb;

		foreach ( array( 'rest', 'xmlrpc' ) as $cap ) {
			$enabled_users = get_users(
				array(
					'fields'     => 'ID',
					'meta_query' => array(
						'relation' => 'AND',
						array(
							'key'     => $wpdb->prefix . 'capabilities',
							'value'   => '"' . $this->prefix . $cap . '_enable"',
							'compare' => 'LIKE',
						),
					),
					'number'     => -1,
				)
			);

			// No users selected.
			if ( empty( $_REQUEST[ $this->prefix . 'settings' ][ $cap ]['trusted_users'] ) ) { // WPCS: XSS ok, sanitization ok.
				$users_to_enable = array();
			} else {
				$users_to_enable = $_REQUEST[ $this->prefix . 'settings' ][ $cap ]['trusted_users']; // WPCS: XSS ok, sanitization ok.
			}

			// Remove caps.
			foreach ( array_diff( $enabled_users, $users_to_enable ) as $user_id ) {
				$editing_user = get_user_by( 'id', $user_id );
				$editing_user->remove_cap( $this->prefix . $cap . '_enable' );
			}
			// Add caps.
			foreach ( array_diff( $users_to_enable, $enabled_users ) as $user_id ) {
				$editing_user = get_user_by( 'id', $user_id );
				$editing_user->add_cap( $this->prefix . $cap . '_enable' );
			}
		}
	}

	/**
	 * Add help admin settings screen.
	 */
	public function add_help_tab() {

		global $pagenow;

		// Get screen.
		$screen = get_current_screen();

		$add_sidebar = false;

		$screen->add_help_tab(
			array(
				'id'      => 'overview',
				'title'   => __( 'Overview', 'rest-xmlrpc-data-checker' ),
				'content' => '<p>' . __( 'This screen is used for managing JSON REST and XML-RPC accesses and permissions to your WordPress installation.', 'rest-xmlrpc-data-checker' ) . '</p>'
					. '<p>' . __( 'You must click the <strong>Save Changes</strong> button at the bottom of the screen for new settings to take effect.', 'rest-xmlrpc-data-checker' ) . '</p>',
			)
		);

		$screen->add_help_tab(
			array(
				'id'      => 'rest',
				'title'   => __( 'REST', 'rest-xmlrpc-data-checker' ),
				'content' => '<p>' . __( 'The REST tab allows you to control JSON REST API requests to your WordPress installation.', 'rest-xmlrpc-data-checker' ) . '</p><ul>' .
					'<li><strong>' . __( 'REST API', 'rest-xmlrpc-data-checker' ) . '</strong> &mdash; ' . __( 'Allows you to completely disable JSON REST requests for unlogged users and/or disable JSONP support in the REST API (regardless of authentication and trust settings). If you don\'t have external applications that need to communicate with your WordPress instance using JSON REST you are strongly encouraged to disable JSON REST API for unlogged users.', 'rest-xmlrpc-data-checker' ) . '</li>' .
					'<li><strong>' . __( 'REST prefix', 'rest-xmlrpc-data-checker' ) . '</strong> &mdash; ' . __( 'Allows to change REST prefix route.', 'rest-xmlrpc-data-checker' ) . '</li>' .
					'<li><strong>' . __( 'REST Links', 'rest-xmlrpc-data-checker' ) . '</strong> &mdash; ' . /* translators: 1 tag, 2 HTTP header */ sprintf( __( 'Allows to remove REST API and oEmbed Discovery %1$s tags, REST API %2$s HTTP header and REST API Really Simple Discovery (RSD) endpoint informations added by WordPress to front-end pages.', 'rest-xmlrpc-data-checker' ), '<code>&lt;link&gt;</code>', '<code>Link</code>' ) . '</li>' .
					'</ul><p>' . __( 'If you need to leave REST JSON enabled, disable REST API interface for unlogged users and then grant accesses by using following settings:', 'rest-xmlrpc-data-checker' ) . '</p>' .
					'<ul><li><strong>' . __( 'Authentication', 'rest-xmlrpc-data-checker' ) . '</strong> &mdash; ' . /* translators: %s HTTP header */ sprintf( __( 'The <strong>Use Basic Authentication</strong> option enable Basic Authentication as login method. The users have to supply username/password in the %s HTTP header and the access to JSON REST API is restricted only to selected users. If you enable this option, be sure to use SSL-secured connections.', 'rest-xmlrpc-data-checker' ), '<code>Authorization</code>' ) . '</li>' .
					'<li><strong>' . __( 'Trusted netkwors', 'rest-xmlrpc-data-checker' ) . '</strong> &mdash; ' . __( 'Allows you to restrict JSON REST requests only if they come from selected IPs or netowrks.', 'rest-xmlrpc-data-checker' ) . '</li>' .
					'<li><strong>' . __( 'Allowed routes', 'rest-xmlrpc-data-checker' ) . '</strong> &mdash; ' . __( 'Allows you to restrict JSON REST requests only for selected REST routes.', 'rest-xmlrpc-data-checker' ) . '</li>' .
					'</ul>',
			)
		);

		$screen->add_help_tab(
			array(
				'id'      => 'xml-rpc',
				'title'   => __( 'XML-RPC', 'rest-xmlrpc-data-checker' ),
				'content' => '<p>' . __( 'The XML-RPC tab allows you to control XML-RPC API requests to your WordPress installation.', 'rest-xmlrpc-data-checker' ) . '</p><ul>' .
					'<li><strong>' . __( 'XML-RPC API', 'rest-xmlrpc-data-checker' ) . '</strong> &mdash; ' . __( 'Allows you to completely disable XML-RPC requests regardless of authentication and trust settings. If you don\'t have external applications that need to communicate with your WordPress instance using XML-RPC API you are strongly strongly encouraged to disable XML-RPC interface.', 'rest-xmlrpc-data-checker' ) . '</li>' .
					'<li><strong>' . __( 'XML-RPC Links', 'rest-xmlrpc-data-checker' ) . '</strong> &mdash; ' . /* translators: 1 tag, 2 HTTP header */ sprintf( __( 'Allows to remove %1$s tag for Really Simple Discovery (RSD) service endpoint informations and %2$s HTTP header added by WordPress to front-end pages.', 'rest-xmlrpc-data-checker' ), '<code>&lt;link&gt;</code>', '<code>X-Pingback</code>' ) . '</li></ul>' .
					'<p>' . __( 'If you need to leave XML-RPC enabled, first of all be sure to use SSL-secured connections. Then you can restrict accesses by using following settings:', 'rest-xmlrpc-data-checker' ) . '</p><ul>' .
					'<li><strong>' . __( 'Trusted users', 'rest-xmlrpc-data-checker' ) . '</strong> &mdash; ' . __( 'Allows you to restrict XML-RPC API access only to selected users.', 'rest-xmlrpc-data-checker' ) . '</li>' .
					'<li><strong>' . __( 'Trusted netkwors', 'rest-xmlrpc-data-checker' ) . '</strong> &mdash; ' . __( 'Allows you to restrict XML-RPC API requests only if they come from selected IPs or netowrks.', 'rest-xmlrpc-data-checker' ) . '</li>' .
					'<li><strong>' . __( 'Allowed methods', 'rest-xmlrpc-data-checker' ) . '</strong> &mdash; ' . __( 'Allows you to restrict XML-RPC API requests only for selected methods', 'rest-xmlrpc-data-checker' ) . '</li>' .
					'</ul>',
			)
		);

		$screen->add_help_tab(
			array(
				'id'      => 'options',
				'title'   => __( 'Options', 'rest-xmlrpc-data-checker' ),
				'content' => '<p>' . __( 'The Options tab allows you to perform following actions:', 'rest-xmlrpc-data-checker' ) . '</p><ul>' .
					'<li><strong>' . __( 'Plugin settings', 'rest-xmlrpc-data-checker' ) . '</strong> &mdash; ' . __( 'Allows you to completely remove options on plugin removal including additional REST/XML-RPC user\'s capabilities added by plugin.', 'rest-xmlrpc-data-checker' ) . '</li>' .
					'</ul>',
				false,
				1000,
			)
		);

		$screen->set_help_sidebar(
			'<p><strong>' . __( 'For more information:', 'rest-xmlrpc-data-checker' ) . '</strong></p>' .
			'<p><a href="https://github.com/enrico-sorcinelli/rest-xmlrpc-data-checker/" target="_blank">' . __( 'Plugin documentation', 'rest-xmlrpc-data-checker' ) . '</a></p>' .
			'<p><a href="https://github.com/enrico-sorcinelli/rest-xmlrpc-data-checker/issues" target="_blank">' . __( 'Report a bug', 'rest-xmlrpc-data-checker' ) . '</a></p>'
		);
	}

}

<?php
/**
 * REST XML-RPC data checker plugin for WordPress.
 *
 * @package rest-xmlrpc-data-checker
 *
 * Plugin Name: REST XMLRPC Data Checker
 * Plugin URI:  https://github.com/enrico-sorcinelli/rest-xmlrpc-data-checker
 * Description: A WordPress plugin that allow to check JSON REST and XML-RPC API requests and grant access permissions
 * Author:      Enrico Sorcinelli
 * Author URI:  https://github.com/enrico-sorcinelli/rest-xmlrpc-data-checker/graphs/contributors
 * Text Domain: rest-xmlrpc-data-checker
 * Domain Path: /languages/
 * Version:     1.2.1
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Check running WordPress instance.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.1 404 Not Found' );
	exit();
}

// Plugins constants.
define( 'REST_XMLRPC_DATA_CHECKER_VERSION', '1.2.1' );
define( 'REST_XMLRPC_DATA_CHECKER_BASEDIR', dirname( __FILE__ ) );
define( 'REST_XMLRPC_DATA_CHECKER_BASEURL', plugin_dir_url( __FILE__ ) );

// Enable debug prints on error_log (only when WP_DEBUG is true).
if ( ! defined( 'REST_XMLRPC_DATA_CHECKER_DEBUG' ) ) {
	define( 'REST_XMLRPC_DATA_CHECKER_DEBUG', false );
}

if ( ! class_exists( 'REST_XMLRPC_Data_Checker' ) ) {

	require_once REST_XMLRPC_DATA_CHECKER_BASEDIR . '/php/class-rest-xmlrpc-data-checker.php';

	/**
	 * Init the plugin.
	 *
	 * Define REST_XMLRPC_DATA_CHECKER_AUTOENABLE to `false` in your wp-config.php to disable.
	 */
	function rest_xmlrpc_data_checker_init() {

		if ( defined( 'REST_XMLRPC_DATA_CHECKER_AUTOENABLE' ) && REST_XMLRPC_DATA_CHECKER_AUTOENABLE === false ) {
			return;
		}

		// Instantiate our plugin class and add it to the set of globals.
		$GLOBALS['rest_xmlrpc_data_checker'] = REST_XMLRPC_Data_Checker::get_instance( array( 'debug' => REST_XMLRPC_DATA_CHECKER_DEBUG && WP_DEBUG ) );
	}

	// Activate the plugin once all plugin have been loaded.
	add_action( 'plugins_loaded', 'rest_xmlrpc_data_checker_init' );

	// Activation/Deactivation hooks.
	register_uninstall_hook( __FILE__, array( 'REST_XMLRPC_Data_Checker', 'plugin_uninstall' ) );
}

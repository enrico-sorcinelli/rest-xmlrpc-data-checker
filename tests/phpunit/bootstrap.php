<?php
/**
 * PHPUnit bootstrap file
 *
 * @package Rest_Xmlrpc_Data_Checker
 */

$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

// Allow to override PHP executable used by WordPress Test framework.
$_php_binary = getenv( 'WP_PHP_BINARY' );
if ( ! empty( $_php_binary ) ) {
	define( 'WP_PHP_BINARY', $_php_binary );
}

if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	echo "Could not find $_tests_dir/includes/functions.php, have you run bin/install-wp-tests.sh ?" . PHP_EOL;
	exit( 1 );
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

// Check for XML-RPC tests.
if ( in_array( '--group', $_SERVER['argv'], true ) && in_array( 'xmlrpc', $_SERVER['argv'], true ) ) {
	define( 'XMLRPC_REQUEST', true );
} else {
	echo PHP_EOL;
	echo 'Not running plugin XML-RPC tests. To execute these, use --group xmlrpc.';
	echo PHP_EOL;
}

/**
 * Manually load the plugin being tested.
 */
function _manually_load_plugin() {
	require dirname( dirname( __FILE__ ) ) . '../../rest-xmlrpc-data-checker.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';

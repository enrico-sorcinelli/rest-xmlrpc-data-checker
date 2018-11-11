<?php
/**
 * WordPress XML-RPC server custom implementation.
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
 * WordPress XMLRPC server custom implementation.
 */
class XMLRPC_Server extends \wp_xmlrpc_server {

	/**
	 * Overrided method.
	 *
	 * @param \WP_User         $user           Current logged user.
	 * @param array|\IXR_Error $content_struct Arguments call.
	 *
	 * @return array|\IXR_Error|mixed|string|void
	 */
	protected function _insert_post( $user, $content_struct ) {

		/**
		 * Filter XML-RPC data to be inserted via XML-RPC before any action.
		 *
		 * @since 1.0.0
		 *
		 * @param array    $content_struct  Post data array.
		 * @param \WP_User $user            WP_User object
		 *
		 * @return array|\IXR_Error $content_struct
		 */
		$content_struct = apply_filters( 'xmlrpc_before_insert_post', $content_struct, $user );
		if ( $content_struct instanceof \IXR_Error ) {
			return $content_struct;
		}
		return parent::_insert_post( $user, $content_struct );
	}

}

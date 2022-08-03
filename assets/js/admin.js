/**
 * @file This file contains XML-RPC REST Data Checker JavaScript
 * @author Enrico Sorcinelli <enrico.sorcinelli.com>
 * @version 1.3.2
 * @title XML-RPC REST Data Checker
 */

// Make all in a closure.
;( function( $ ) {
	$( document ).ready( function() {

		// Tab managements.
		$( '.nav-tab-wrapper.rest-xmlrpc-data-checker a' ).on( 'click', function() {
			$( '.nav-tab.rest-xmlrpc-data-checker' ).removeClass( 'nav-tab-active' );
			$( this ).addClass( 'nav-tab-active' );
			$( 'section.rest-xmlrpc-data-checker' )
				.hide()
				.eq( $( this ).index() ).show();
			$( 'input[name="' + rest_xmlrpc_data_checker_i18n.prefix + 'active_tab"]' ).val( $( this ).data( 'section' ) );

			document.location.hash = $( this ).data( 'section' );
			return false;
		});

		// Active tab by hash.
		$( '.nav-tab-wrapper.rest-xmlrpc-data-checker a[data-section="' + document.location.hash.replace( /^#+/, '' ) + '"]' ).trigger( 'click' );

		// Add handler on click over user row.
		$( 'table.rest-xmlrpc-data-checker-users-list.cb tbody tr td' ).on( 'click', function( e ) {

			//$( e.currentTarget ).parent().toggleClass( 'selected' );
			$( e.currentTarget ).parent().find( 'th input' ).first().trigger( 'click' );
		} );

		// Add warning under insecure protocol.
		if ( ! /^https/.test( document.location.protocol ) ) {

			// REST API warning.
			if ( 'basic_auth' === $( 'input[name="' + rest_xmlrpc_data_checker_i18n.prefix + 'settings\\[rest\\]\\[auth_method\\]"]:checked' ).val() ) {
				$( 'div.update-nag.rest_basic_auth_alert' ).show();
			}
			else {
				$( '.' + rest_xmlrpc_data_checker_i18n.prefix + 'rest_enable' ).hide();
			}

			$( 'input[name="' + rest_xmlrpc_data_checker_i18n.prefix + 'settings\\[rest\\]\\[auth_method\\]"]' ).on( 'click', function() {
				if ( 'basic_auth' === $( this ).val() ) {
					$( 'div.update-nag.rest_basic_auth_alert' ).show( 200 );
					$( '.' + rest_xmlrpc_data_checker_i18n.prefix + 'rest_enable' ).show( 200 );
				}
				else {
					$( 'div.update-nag.rest_basic_auth_alert' ).hide( 200 );
					$( '.' + rest_xmlrpc_data_checker_i18n.prefix + 'rest_enable' ).hide( 200 );
				}
			} );

			// XML-RPC API warning.
			if ( false === $( '#' + rest_xmlrpc_data_checker_i18n.prefix + 'settings_xmlrpc_disable' ).prop( 'checked' ) ) {
				$( 'div.update-nag.xmlrpc_enable_alert' ).show();
			}
			$( '#' + rest_xmlrpc_data_checker_i18n.prefix + 'settings_xmlrpc_disable' ).on( 'click', function() {
				false === $( this ).prop( 'checked' ) ? $( 'div.update-nag.xmlrpc_enable_alert' ).show( 200 ) : $( 'div.update-nag.xmlrpc_enable_alert' ).hide( 200 );
			} );
		}

		// Setup jQuery Flex Tree for REST routes.
		$( '#rest-xmlrpc-data-checker-rest-routes' ).flexTree(
			{
				type: 'checkbox',
				name: 'rest_xmlrpc_data_checker_settings[rest][allowed_routes][]',
				collapsed: true,
				items: rest_xmlrpc_data_checker_jft_items.rest
			}
		);

		// Setup jQuery Flex Tree for XML-RPC methods.
		$( '#rest-xmlrpc-data-checker-xmlrpc-methods' ).flexTree(
			{
				type: 'checkbox',
				name: 'rest_xmlrpc_data_checker_settings[xmlrpc][allowed_methods][]',
				collapsed: true,
				items: rest_xmlrpc_data_checker_jft_items.xmlrpc
			}
		);

	});
})( jQuery );

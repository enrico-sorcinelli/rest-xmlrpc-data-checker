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

		// Handle toggle over methods/end-point.
		$( '.rest-xmlrpc-data-checker-cb-list h2 span' ).on( 'click', function() {
			$( this )
				.toggleClass( 'closed' )
				.parent().next().toggle( 200 );

		} );

		// Check all/none over methods/end-point.
		$( '.rest-xmlrpc-data-checker-cb-list h2' ).find( 'input[type="checkbox"]:first' ).on( 'click', function() {
			$( this ).removeClass( 'indeterminate' );
			$( this ).parent().next().find( 'input[type="checkbox"]' ).prop( 'checked', $( this ).prop( 'checked' ) );
		} );

		// Handle toggle on 'all' checkboxes.
		var check_all_checkbox = function() {
			var $all_cb = $( this ).parents( 'ul' ).prev().find( 'input[type="checkbox"]:first' );

			// Remove checkall.
			if ( ! $( this ).prop( 'checked' ) ) {
				$all_cb.prop( 'checked', false );
			}
			var all_is_checked = true,
				some_is_checked = false;

			// Check all childrens.
			$( this ).parents( 'ul' ).find( 'input[type="checkbox"]' ).each( function() {
				if ( ! $( this ).prop( 'checked' ) ) {
					all_is_checked = false;
				}
				else {
					some_is_checked = true;
				}
			} );

			if ( some_is_checked ) {
				$all_cb.addClass( 'indeterminate' )
					.prop( 'indeterminate', true );
			}
			else {
				$all_cb.removeClass( 'indeterminate' )
					.prop( 'indeterminate', false );
			}

			if ( all_is_checked ) {
				$all_cb.prop( 'checked', true )
					.prop( 'indeterminate', false )
					.removeClass( 'indeterminate' );
			}

		};

		// Check parent at run time.
		$( '.rest-xmlrpc-data-checker-cb-list ul input[type="checkbox"]' ).on( 'click', check_all_checkbox );

		// Check parent checkbox on load.
		$( '.rest-xmlrpc-data-checker-cb-list ul' ).each( function() {
			( $( 'input[type="checkbox"]:first', this ) ).each( check_all_checkbox );
		} );

	});
})( jQuery );

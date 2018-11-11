<?php
/**
 * Plugin credit template part.
 *
 * @package rest-xmlrpc-data-checker
 */

?>
			<div id="postbox-container-1" class="postbox-container">
				<div class="postbox" id="rest-xmlrpc-data-checker-credits">
					<h3 class="hndle">REST XML-RPC Data Checker <?php echo esc_html( REST_XMLRPC_DATA_CHECKER_VERSION ); ?></h3>
					<div class="inside">
						<h4><?php esc_html_e( 'Changelog', 'rest-xmlrpc-data-checker' ); ?></h4>
						<p><?php esc_html_e( 'What\'s new in', 'rest-xmlrpc-data-checker' ); ?>
							<a href="https://github.com/enrico-sorcinelli/rest-xmlrpc-data-checker/releases"><?php /* translators: */ echo esc_html( sprintf( __( 'version %s', 'rest-xmlrpc-data-checker' ), REST_XMLRPC_DATA_CHECKER_VERSION ) ); ?></a>.</p>
						<h4><?php esc_html_e( 'Support', 'rest-xmlrpc-data-checker' ); ?></h4>
						<p><span class="dashicons dashicons-email-alt"></span>
							<a href="https://github.com/enrico-sorcinelli/rest-xmlrpc-data-checker/issues"><?php esc_html_e( 'Feel free to ask for help', 'rest-xmlrpc-data-checker' ); ?></a>.</p>
						<div class="author">
							<i><span><?php esc_html_e( 'REST XML-RPC Data Checker', 'rest-xmlrpc-data-checker' ); ?> <?php esc_html_e( 'by', 'rest-xmlrpc-data-checker' ); ?> Enrico Sorcinelli</span><br>
							&copy; <?php echo date( 'Y' ); /* WPCS: XSS okay. */ ?></i>
						</div>
					</div>
				</div>
			</div>

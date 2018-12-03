<?php
/**
 * User column table status template.
 *
 * @package rest-xmlrpc-data-checker
 */

?>
<div class="table">
	<div class="row">
		<div class="cell">
			<span><?php esc_html_e( 'REST JSON', 'rest-xmlrpc-data-checker' ); ?>: </span>
		</div>
		<div class="cell">
<?php
if ( empty( $params['settings']['rest']['disable'] ) || 'none' === $params['settings']['rest']['auth_method'] ) {
?>
			<span title="<?php esc_html_e( 'Enabled', 'rest-xmlrpc-data-checker' ); ?>" class="dashicons dashicons-unlock rest-xmlrpc-data-checker-ok"></span>
<?php
}
elseif ( 'basic_auth' === $params['settings']['rest']['auth_method']
			&& user_can( $params['user_id'], $params['prefix'] . 'rest_enable' ) ) {
?>
			<span title="<?php esc_html_e( 'Access with credentials', 'rest-xmlrpc-data-checker' ); ?>" class="dashicons dashicons-lock rest-xmlrpc-data-checker-ko"></span>
<?php
}
else {
?>
			<span title="<?php esc_html_e( 'Disabled', 'rest-xmlrpc-data-checker' ); ?>" class="dashicons dashicons-no rest-xmlrpc-data-checker-ko"></span>
<?php
}
?>
		</div>
	</div>
	<div class="row">
		<div class="cell">
			<span><?php esc_html_e( 'XML-RPC', 'rest-xmlrpc-data-checker' ); ?>: </span>
		</div>
		<div class="cell">
<?php
if ( $params['settings']['xmlrpc']['disable']
	|| ( $params['settings']['xmlrpc']['apply_trusted_users'] && ! user_can( $params['user_id'], $params['prefix'] . 'xmlrpc_enable' ) )
) {
?>
			<span title="<?php esc_html_e( 'Disabled', 'rest-xmlrpc-data-checker' ); ?>" class="dashicons dashicons-no rest-xmlrpc-data-checker-ko"></span>
<?php
}
else {
?>
			<span title="<?php esc_html_e( 'Access with credentials', 'rest-xmlrpc-data-checker' ); ?>" class="dashicons dashicons-lock rest-xmlrpc-data-checker-ko"></span>
<?php
}
?>
		</div>
	</div>
</div>

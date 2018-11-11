<?php
/**
 * User fields.
 *
 * @package rest-xmlrpc-data-checker
 */

?>
<h3><?php esc_html_e( 'REST XML-RPC Data Checker', 'rest-xmlrpc-data-checker' ); ?></h3>
<table class="form-table">
	<tr>
		<th><label for="address"><?php esc_html_e( 'REST API', 'rest-xmlrpc-data-checker' ); ?></label></th>
		<td>
			<input name="<?php echo esc_attr( $params['prefix'] . 'rest_enable' ); ?>" type="checkbox" id="<?php echo esc_attr( $params['prefix'] . 'rest_enable' ); ?>" value="1" <?php checked( 1, user_can( $params['user'], $params['prefix'] . 'rest_enable' ) ); ?>">
			<label for="<?php echo esc_attr( $params['prefix'] . 'rest_enable' ); ?>">
				<?php esc_html_e( 'Enable REST interface', 'rest-xmlrpc-data-checker' ); ?>
			</label>
			<p class="description">
				<?php esc_html_e( 'This setting will take effect only if the REST interface has been disabled for unlogged users.', 'rest-xmlrpc-data-checker' ); ?>
			</p>
		</td>
	</tr>
	<tr>
		<th><label for="address"><?php esc_html_e( 'XML-RPC API', 'rest-xmlrpc-data-checker' ); ?></label></th>
		<td>
			<input name="<?php echo esc_attr( $params['prefix'] . 'xmlrpc_enable' ); ?>" type="checkbox" id="<?php echo esc_attr( $params['prefix'] . 'xmlrpc_enable' ); ?>" value="1" <?php checked( 1, user_can( $params['user'], $params['prefix'] . 'xmlrpc_enable' ) ); ?>">
			<label for="<?php echo esc_attr( $params['prefix'] . 'xmlrpc_enable' ); ?>">
				<?php esc_html_e( 'Enable XML-RPC interface', 'rest-xmlrpc-data-checker' ); ?>
			</label>
			<p class="description">
				<?php esc_html_e( 'This setting will take effect only if the XML-RPC interface hasn\'t been disabled.', 'rest-xmlrpc-data-checker' ); ?>
			</p>
		</td>
	</tr>
</table>

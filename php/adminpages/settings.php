<?php
/**
 * Plugin settings.
 *
 * @package rest-xmlrpc-data-checker
 */

// Global.
global $wp_query, $wp;

/**
 * Build checkbox list.
 *
 * @param array $args {
 *     Arguments list.
 *     @type array  $items
 *     @type string $name
 *     @type array  $checked_items
 * }
 */
function cb_list( $args = array() ) {
	$args = array_merge(
		array(
			'items'         => array(),
			'name'          => '',
			'checked_items' => array(),
		),
		$args
	);
?>
		<div class="rest-xmlrpc-data-checker-cb-list">
<?php
	foreach ( $args['items']  as $key => $value ) {
?>
			<h2><input type="checkbox"> <span class="closed"><?php echo esc_html( $key ); ?></span></h2>
			<ul>
<?php
		foreach ( $value as $item ) {
?>
				<li>
					<label>
						<input name="<?php echo esc_attr( $args['name'] ); ?>" type="checkbox" value="<?php echo esc_attr( $item ); ?>" <?php checked( 1, in_array( $item, $args['checked_items'], true ) ); ?>">
						<?php echo esc_html( $item ); ?>
					</label>
				</li>
<?php
		}
?>
			</ul>
<?php
	}
?>
		<div>
<?php
}
?>
<div class="wrap">
	<h1><?php esc_html_e( 'REST XML-RPC Data Checker', 'rest-xmlrpc-data-checker' ); ?></h1>
	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="postbox-container-2" class="postbox-container">

<form method="post" action="options.php">
	<input type="hidden" name="<?php echo esc_attr( $params['prefix'] . 'active_tab' ); ?>" value="rest">
	<?php settings_fields( $params['prefix'] . 'settings' ); ?>
	<?php do_settings_sections( $params['prefix'] . 'settings' ); ?>

	<h2 class="nav-tab-wrapper wp-clearfix rest-xmlrpc-data-checker">
		<a class="nav-tab rest-xmlrpc-data-checker nav-tab-active" data-section="rest"><?php esc_html_e( 'REST', 'rest-xmlrpc-data-checker' ); ?></a>
		<a class="nav-tab rest-xmlrpc-data-checker" data-section="xml-rpc"><?php esc_html_e( 'XML-RPC', 'rest-xmlrpc-data-checker' ); ?></a>
		<a class="nav-tab rest-xmlrpc-data-checker" data-section="options"><?php esc_html_e( 'Options', 'rest-xmlrpc-data-checker' ); ?></a>
	</h2>

	<!-- REST section -->
	<section class="rest-xmlrpc-data-checker">
		<table class="form-table">
			<tr>
				<th scope="row">
					<?php esc_html_e( 'REST API', 'rest-xmlrpc-data-checker' ); ?>
				</th>
				<td>
					<fieldset>
						<label>
							<input name="<?php echo esc_attr( $params['prefix'] . 'settings[rest][disable]' ); ?>" type="checkbox" id="<?php echo esc_attr( $params['prefix'] . 'settings_rest_disable' ); ?>" value="1" <?php checked( 1, $params['settings']['rest']['disable'] ); ?>">
							<?php esc_html_e( 'Disable REST API interface for unlogged users', 'rest-xmlrpc-data-checker' ); ?>
						</label>
						<br>
						<label>
							<input name="<?php echo esc_attr( $params['prefix'] . 'settings[rest][disable_jsonp]' ); ?>" type="checkbox" id="<?php echo esc_attr( $params['prefix'] . 'settings_rest_disable_jsonp' ); ?>" value="1" <?php checked( 1, $params['settings']['rest']['disable_jsonp'] ); ?>">
							<?php esc_html_e( 'Disable JSONP support in REST API interface', 'rest-xmlrpc-data-checker' ); ?>
						</label>
						<p class="description rest-xmlrpc-data-checker-indent"><?php esc_html_e( 'Note that this will deny all JSONP requests regardless of authentication and trust checks below.', 'rest-xmlrpc-data-checker' ); ?></p>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?php echo esc_attr( $params['prefix'] . 'settings_rest_url_prefix' ); ?>">
						<?php esc_html_e( 'REST prefix', 'rest-xmlrpc-data-checker' ); ?>
					</label>
				</th>
				<td>
					<?php echo esc_html( get_site_url() ); ?>/<input name="<?php echo esc_attr( $params['prefix'] . 'settings[rest][url_prefix]' ); ?>" type="text" id="<?php echo esc_attr( $params['prefix'] . 'settings_rest_url_prefix' ); ?>" value="<?php echo esc_attr( $params['settings']['rest']['url_prefix'] ); ?>">
					<p class="description">
						<?php esc_html_e( 'Allows to change REST prefix route.', 'rest-xmlrpc-data-checker' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php esc_html_e( 'REST Links', 'rest-xmlrpc-data-checker' ); ?>
				</th>
				<td>
					<fieldset>
						<label>
							<input name="<?php echo esc_attr( $params['prefix'] . 'settings[rest][remove_link_tag]' ); ?>" type="checkbox" id="<?php echo esc_attr( $params['prefix'] . 'settings_rest_remove_link_tag' ); ?>" value="1" <?php checked( 1, $params['settings']['rest']['remove_link_tag'] ); ?>">
							<?php /* translators: %s tag */ echo sprintf( __( 'Remove REST API %s tag', 'rest-xmlrpc-data-checker' ), '<code>&lt;link&gt;</code>' ); ?>
						</label>
						<br>
						<label>
							<input name="<?php echo esc_attr( $params['prefix'] . 'settings[rest][remove_link_http_headers]' ); ?>" type="checkbox" id="<?php echo esc_attr( $params['prefix'] . 'settings_rest_remove_link_http_headers' ); ?>" value="1" <?php checked( 1, $params['settings']['rest']['remove_link_http_headers'] ); ?>">
							<?php /* translators: %s HTTP header */ echo sprintf( __( 'Remove REST API %s HTTP header', 'rest-xmlrpc-data-checker' ), '<code>Link</code>' ); ?>
						</label>
						<br>
						<label>
							<input name="<?php echo esc_attr( $params['prefix'] . 'settings[rest][remove_xmlrpc_rsd_apis]' ); ?>" type="checkbox" id="<?php echo esc_attr( $params['prefix'] . 'settings_rest_remove_xmlrpc_rsd_apis' ); ?>" value="1" <?php checked( 1, $params['settings']['rest']['remove_xmlrpc_rsd_apis'] ); ?>">
							<?php esc_html_e( 'Remove REST API Really Simple Discovery (RSD) endpoint informations', 'rest-xmlrpc-data-checker' ); ?>
						</label>
						<br>
						<label>
							<input name="<?php echo esc_attr( $params['prefix'] . 'settings[rest][remove_oembed_discovery_links]' ); ?>" type="checkbox" id="<?php echo esc_attr( $params['prefix'] . 'settings_rest_remove_oembed_discovery_links' ); ?>" value="1" <?php checked( 1, $params['settings']['rest']['remove_oembed_discovery_links'] ); ?>">
							<?php /* translators: %s tag */ echo sprintf( __( 'Remove oEmbed discovery %s tags', 'rest-xmlrpc-data-checker' ), '<code>&lt;link&gt;</code>' ); ?>
						</label>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php esc_html_e( 'Authentication', 'rest-xmlrpc-data-checker' ); ?>
				</th>
				<td>
					<fieldset>
						<label>
							<input name="<?php echo esc_attr( $params['prefix'] . 'settings[rest][auth_method]' ); ?>" type="radio" id="<?php echo esc_attr( $params['prefix'] . 'settings_rest_auth_method_none' ); ?>" value="none" <?php checked( 'none', $params['settings']['rest']['auth_method'] ); ?>">
							<?php esc_html_e( 'None', 'rest-xmlrpc-data-checker' ); ?>
						</label>
						<br>
						<label>
							<input name="<?php echo esc_attr( $params['prefix'] . 'settings[rest][auth_method]' ); ?>" type="radio" id="<?php echo esc_attr( $params['prefix'] . 'settings_rest_auth_method_basic_auth' ); ?>" value="basic_auth" <?php checked( 'basic_auth', $params['settings']['rest']['auth_method'] ); ?>">
							<?php esc_html_e( 'Use Basic Authentication', 'rest-xmlrpc-data-checker' ); ?>
						</label>
						<p class="description rest-xmlrpc-data-checker-indent">
							<?php /* translators: %s tag */ echo sprintf( __( 'This allows you to restrict REST requests only for selected users. They have to supply username/password in the %s HTTP header.', 'rest-xmlrpc-data-checker' ), '<code>Authorization</code>' ); ?>
							<?php esc_html_e( 'This will applied only if REST API interface has been disabled for unlogged users.', 'rest-xmlrpc-data-checker' ); ?>
						</p>
						<div class="update-nag rest_basic_auth_alert">
							<b><?php esc_html_e( 'Your WordPress installation don\'t appear to run under a secure connection.', 'rest-xmlrpc-data-checker' ); ?></b>
							<br>
							<?php esc_html_e( 'The Basic Authentication requires sending your username and password with every request, and should only be used over SSL-secured connections or for local development and testing.', 'rest-xmlrpc-data-checker' ); ?>
							<br>
							<?php esc_html_e( 'Without SSL you are strongly encouraged to to turn off authentication in production environments.', 'rest-xmlrpc-data-checker' ); ?>
						</div>
					</fieldset>
					<?php echo $params['rest_users_list']; // WPCS: XSS ok, sanitization ok. ?>

				</td>
			</tr>
			<tr>
				<th scope="row"><label for="<?php echo esc_attr( $params['prefix'] . 'settings_rest_trusted_networks' ); ?>"><?php esc_html_e( 'Trusted netkwors', 'rest-xmlrpc-data-checker' ); ?></label></th>
				<td>
					<label>
						<input name="<?php echo esc_attr( $params['prefix'] . 'settings[rest][apply_trusted_networks]' ); ?>" type="checkbox" id="<?php echo esc_attr( $params['prefix'] . 'settings_rest_apply_trusted_networks' ); ?>" value="1" <?php checked( 1, $params['settings']['rest']['apply_trusted_networks'] ); ?>">
						<?php esc_html_e( 'Apply trusted networks criteria', 'rest-xmlrpc-data-checker' ); ?>
					</label>
					<p>
						<?php esc_html_e( 'With this option active, only requests coming form a specific IPs or newtorks are allowed.', 'rest-xmlrpc-data-checker' ); ?>
						<?php esc_html_e( 'This will applied only if REST API interface has been disabled for unlogged users.', 'rest-xmlrpc-data-checker' ); ?>
						<br>
						<?php esc_html_e( 'Add one trusted IP or network per line', 'rest-xmlrpc-data-checker' ); ?>
					</p>
					<textarea name="<?php echo esc_attr( $params['prefix'] . 'settings[rest][trusted_networks]' ); ?>" rows="5" cols="50" id="<?php echo esc_attr( $params['prefix'] . 'settings_rest_trusted_networks' ); ?>" class="large-text code"><?php echo esc_html( $params['settings']['rest']['trusted_networks'] ); ?></textarea>
					<p class="description"><?php esc_html_e( 'For example: 10.10.0.1/32 , 10.10.10.0/25 .', 'rest-xmlrpc-data-checker' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Allowed routes', 'rest-xmlrpc-data-checker' ); ?></th>
				<td>
					<label>
						<input name="<?php echo esc_attr( $params['prefix'] . 'settings[rest][apply_allowed_routes]' ); ?>" type="checkbox" id="<?php echo esc_attr( $params['prefix'] . 'settings_rest_apply_allowed_routes' ); ?>" value="1" <?php checked( 1, $params['settings']['rest']['apply_allowed_routes'] ); ?>">
						<?php esc_html_e( 'Apply allowed routes criteria', 'rest-xmlrpc-data-checker' ); ?>
					</label>
					<p>
						<?php esc_html_e( 'With this option active, you can selectively allow REST routes.', 'rest-xmlrpc-data-checker' ); ?><br>
						<?php esc_html_e( 'This will applied only if REST API interface has been disabled for unlogged users.', 'rest-xmlrpc-data-checker' ); ?>
					</p>
					<?php
						cb_list(
							array(
								'items'         => $params['rest_routes'],
								'name'          => $params['prefix'] . 'settings[rest][allowed_routes][]',
								'checked_items' => $params['settings']['rest']['allowed_routes'],
							)
						);
					?>
				</td>
			</tr>
		</table>
	</section>

	<!-- XML-RPC section -->
	<section class="rest-xmlrpc-data-checker">
		<table class="form-table">
			<tr>
				<th scope="row">
					<?php esc_html_e( 'XML-RPC API', 'rest-xmlrpc-data-checker' ); ?>
				</th>
				<td>
					<input name="<?php echo esc_attr( $params['prefix'] . 'settings[xmlrpc][disable]' ); ?>" type="checkbox" id="<?php echo esc_attr( $params['prefix'] . 'settings_xmlrpc_disable' ); ?>" value="1" <?php checked( 1, $params['settings']['xmlrpc']['disable'] ); ?>">
					<label for="<?php echo esc_attr( $params['prefix'] . 'settings_xmlrpc_disable' ); ?>">
						<?php esc_html_e( 'Disable XML-RPC API interface', 'rest-xmlrpc-data-checker' ); ?>
					</label>
					<p class="description rest-xmlrpc-data-checker-indent"><?php esc_html_e( 'This will deny all XML-RPC requests regardless of authentication and trust checks below.', 'rest-xmlrpc-data-checker' ); ?></p>
					<div class="update-nag xmlrpc_enable_alert">
						<b><?php esc_html_e( 'Your WordPress installation don\'t appear to run under a secure connection.', 'rest-xmlrpc-data-checker' ); ?></b>
						<br>
						<?php esc_html_e( 'The XML-RPC interface requires sending your username and password with every request, and should only be used over SSL-secured connections or for local development and testing.', 'rest-xmlrpc-data-checker' ); ?>
						<br>
						<?php esc_html_e( 'Without SSL you are strongly encouraged to disable XML-RPC interface in production environments.', 'rest-xmlrpc-data-checker' ); ?>
					</div>

				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php esc_html_e( 'XML-RPC Links', 'rest-xmlrpc-data-checker' ); ?>
				</th>
				<td>
					<fieldset>
						<label>
							<input name="<?php echo esc_attr( $params['prefix'] . 'settings[xmlrpc][remove_rsd_link]' ); ?>" type="checkbox" id="<?php echo esc_attr( $params['prefix'] . 'settings_xmlrpc_remove_rsd_link' ); ?>" value="1" <?php checked( 1, $params['settings']['xmlrpc']['remove_rsd_link'] ); ?>">
							<?php /* translators: %s tag */ echo sprintf( __( 'Remove %s to the Really Simple Discovery (RSD) service endpoint informations', 'rest-xmlrpc-data-checker' ), '<code>&lt;link&gt;</code>' ); ?>
						</label>
						<br>
						<label>
							<input name="<?php echo esc_attr( $params['prefix'] . 'settings[xmlrpc][remove_pingback_http_header]' ); ?>" type="checkbox" id="<?php echo esc_attr( $params['prefix'] . 'settings_xmlrpc_remove_pingback_http_header' ); ?>" value="1" <?php checked( 1, $params['settings']['xmlrpc']['remove_pingback_http_header'] ); ?>">
							<?php /* translators: %s HTTP header */ echo sprintf( __( 'Remove %s HTTP header', 'rest-xmlrpc-data-checker' ), '<code>X-Pingback</code>' ); ?>
						</label>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Trusted users', 'rest-xmlrpc-data-checker' ); ?></th>
				<td>
					<label>
						<input name="<?php echo esc_attr( $params['prefix'] . 'settings[xmlrpc][apply_trusted_users]' ); ?>" type="checkbox" id="<?php echo esc_attr( $params['prefix'] . 'settings_xmlrpc_apply_trusted_users' ); ?>" value="1" <?php checked( 1, $params['settings']['xmlrpc']['apply_trusted_users'] ); ?>">
						<?php esc_html_e( 'Apply trusted users criteria', 'rest-xmlrpc-data-checker' ); ?>
					</label>
					<p>
						<?php esc_html_e( 'With this option active, only selected users can access to XML-RPC API.', 'rest-xmlrpc-data-checker' ); ?>
						<?php esc_html_e( 'Select users for which enable XML-RPC', 'rest-xmlrpc-data-checker' ); ?>
					</p>
					<?php echo $params['xmlrpc_users_list']; // WPCS: XSS ok, sanitization ok. ?>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="<?php echo esc_attr( $params['prefix'] . 'settings_xmlrpc_trusted_networks' ); ?>"><?php esc_html_e( 'Trusted netkwors', 'rest-xmlrpc-data-checker' ); ?></label></th>
				<td>
					<label>
						<input name="<?php echo esc_attr( $params['prefix'] . 'settings[xmlrpc][apply_trusted_networks]' ); ?>" type="checkbox" id="<?php echo esc_attr( $params['prefix'] . 'settings_xmlrpc_apply_trusted_networks' ); ?>" value="1" <?php checked( 1, $params['settings']['xmlrpc']['apply_trusted_networks'] ); ?>">
						<?php esc_html_e( 'Apply trusted networks criteria', 'rest-xmlrpc-data-checker' ); ?>
					</label>
					<p>
						<?php esc_html_e( 'With this option active, only requests coming form a specific IPs or newtorks are allowed.', 'rest-xmlrpc-data-checker' ); ?>
						<?php esc_html_e( 'Add one trusted IP or network per line', 'rest-xmlrpc-data-checker' ); ?>
					</p>
					<textarea name="<?php echo esc_attr( $params['prefix'] . 'settings[xmlrpc][trusted_networks]' ); ?>" rows="5" cols="50" id="<?php echo esc_attr( $params['prefix'] . 'settings_xmlrpc_trusted_networks' ); ?>" class="large-text code"><?php echo esc_html( $params['settings']['xmlrpc']['trusted_networks'] ); ?></textarea>
					<p class="description"><?php esc_html_e( 'For example: 10.10.0.1/32 or 10.10.10.0/25 .', 'rest-xmlrpc-data-checker' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Allowed methods', 'rest-xmlrpc-data-checker' ); ?></th>
				<td>
					<label>
						<input name="<?php echo esc_attr( $params['prefix'] . 'settings[xmlrpc][apply_allowed_methods]' ); ?>" type="checkbox" id="<?php echo esc_attr( $params['prefix'] . 'settings_xmlrpc_apply_allowed_methods' ); ?>" value="1" <?php checked( 1, $params['settings']['xmlrpc']['apply_allowed_methods'] ); ?>">
						<?php esc_html_e( 'Apply trusted methods criteria', 'rest-xmlrpc-data-checker' ); ?>
					</label>
					<p>
						<?php esc_html_e( 'With this option active, you can selectively allow XML-RPC methods.', 'rest-xmlrpc-data-checker' ); ?>
					</p>
					<?php
						cb_list(
							array(
								'items'         => $params['xmlrpc_methods'],
								'name'          => $params['prefix'] . 'settings[xmlrpc][allowed_methods][]',
								'checked_items' => $params['settings']['xmlrpc']['allowed_methods'],
							)
						);
					?>
				</td>
			</tr>
		</table>
	</section>

	<!-- Options section -->
	<section class="rest-xmlrpc-data-checker">
		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'User\'s  grants', 'rest-xmlrpc-data-checker' ); ?></th>
				<td>
					<input name="<?php echo esc_attr( $params['prefix'] . 'settings[options][show_user_status_column]' ); ?>" type="checkbox" id="<?php echo esc_attr( $params['prefix'] . 'settings_options_show_user_status_column' ); ?>" value="1" <?php checked( 1, empty( $params['settings']['options']['show_user_status_column'] ) ? 0 : 1, true ); ?>>
					<label for="<?php echo esc_attr( $params['prefix'] . 'settings_options_show_user_status_column' ); ?>">
						<?php esc_html_e( 'Add column with REST and XML-RPC API access informations on users list administration screen.', 'rest-xmlrpc-data-checker' ); ?>
					</label>
				</td>
			<tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Trusted networks check', 'rest-xmlrpc-data-checker' ); ?></th>
				<td>
					<input name="<?php echo esc_attr( $params['prefix'] . 'settings[options][check_forwarded_remote_ip]' ); ?>" type="checkbox" id="<?php echo esc_attr( $params['prefix'] . 'settings_options_check_forwarded_remote_ip' ); ?>" value="1" <?php checked( 1, empty( $params['settings']['options']['check_forwarded_remote_ip'] ) ? 0 : 1, true ); ?>>
					<label for="<?php echo esc_attr( $params['prefix'] . 'settings_options_check_forwarded_remote_ip' ); ?>">
						<?php esc_html_e( 'Uses first the originating IP address if it\'s found in HTTP headers added by proxy or load balancer.', 'rest-xmlrpc-data-checker' ); ?>
					</label>
					<p class="description"><?php esc_html_e( 'Only the right-most IP address that connects to the last proxy or load balancer will be used for trusted networks checks.', 'rest-xmlrpc-data-checker' ); ?>
						<?php /* translators: %s HTTP header */ echo sprintf( __( 'Since it is easy to forge an %s field, enable this option with care.', 'rest-xmlrpc-data-checker' ), '<code>X-Forwarded-For</code>' ); ?></p>
				</td>
			<tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Plugin settings', 'rest-xmlrpc-data-checker' ); ?></th>
				<td>
					<input name="<?php echo esc_attr( $params['prefix'] . 'settings[options][remove_plugin_settings]' ); ?>" type="checkbox" id="<?php echo esc_attr( $params['prefix'] . 'settings_options_remove_plugin_settings' ); ?>" value="1" <?php checked( 1, empty( $params['settings']['options']['remove_plugin_settings'] ) ? 0 : 1, true ); ?>>
					<label for="<?php echo esc_attr( $params['prefix'] . 'settings_options_remove_plugin_settings' ); ?>">
						<?php esc_html_e( 'Completely remove options on plugin removal.', 'rest-xmlrpc-data-checker' ); ?>
					</label>
				</td>
			<tr>
		</table>
	</section>

<?php submit_button(); ?>
</form>

			</div>
			<?php \REST_XMLRPC_Data_Checker\Utils::include_template( REST_XMLRPC_DATA_CHECKER_BASEDIR . '/php/adminpages/credits.php', array( 'prefix' => $params['prefix'] ) ); ?>
		</div><!-- /#post-body -->
	</div><!-- /#poststuff -->
</div><!--/.wrap-->

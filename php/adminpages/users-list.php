<?php
/**
 * User table fields.
 *
 * @package rest-xmlrpc-data-checker
 */

// Only worry about if the user has access.
if ( ! current_user_can( 'edit_users' ) ) {
	?>
<div class="update-nag xmlrpc_enable_alert" style="display: block;">
	<?php esc_html_e( 'You don\'t have enough privileges to manage XML-RPC permissions for users.', 'rest-xmlrpc-data-checker' ); ?>
</div>
	<?php
	return;
}

$users_table = new \REST_XMLRPC_Data_Checker\Users_WP_List_Table(
	array(
		'cb_name'     => $params['cb_name'],
		'cb_meta_key' => $params['cb_meta_key'],
		'table_class' => 'rest-xmlrpc-data-checker-users-list cb',
	)
);
$users_table->prepare_items( array( 'items' => $params['users'] ) );

?>
<div class="<?php echo esc_attr( $params['cb_meta_key'] ); ?>">
<?php $users_table->display(); ?>
</div>

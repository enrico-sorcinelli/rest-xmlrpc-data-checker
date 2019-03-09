<?php
/**
 * Unit tests covering plugin XML-RPC functionality.
 *
 * @package rest-xmlrpc-data-checker
 */

/**
 * Class Tests_XMLRPC.
 * @group xmlrpc
 */
class Tests_XMLRPC extends WP_XMLRPC_UnitTestCase {

	protected $post_data;
	protected $post_id;
	protected $post_date_ts;
	protected $post_custom_field;
	protected $rest_xmlrpc_data_checker_settings = array();

	public function setUp() {

		parent::setUp();

		$this->post_date_ts            = strtotime( '+1 day' );
		$this->post_data               = array(
			'post_title'   => rand_str(),
			'post_content' => rand_str( 2000 ),
			'post_excerpt' => rand_str( 100 ),
			'post_author'  => $this->make_user_by_role( 'author' ),
			'post_date'    => strftime( '%Y-%m-%d %H:%M:%S', $this->post_date_ts ),
		);
		$this->post_id                 = wp_insert_post( $this->post_data );
		$this->post_custom_field       = array(
			'key'   => 'test_custom_field',
			'value' => 12345678,
		);
		$this->post_custom_field['id'] = add_post_meta( $this->post_id, $this->post_custom_field['key'], $this->post_custom_field['value'] );

		// Gives XML-RPC caps to 'author'.
		$current_user = get_user_by( 'login', 'author' );
		$current_user->add_cap( 'rest_xmlrpc_data_checker_xmlrpc_enable' );
	}

	public function tearDown() {
		parent::tearDown();

		# Removes XML-RPC cap to 'author'.
		$current_user = get_user_by( 'login', 'author' );
		$current_user->remove_cap( 'rest_xmlrpc_data_checker_xmlrpc_enable' );
	}

	public function test_enabled_xmlrpc() {

		// Refresh plugin settings.
		$this->refresh_plugin_settings(
			array(
				'xmlrpc' => array(
					'disable' => false,
					'apply_trusted_users'    => false,
					'apply_trusted_networks' => false,
					'apply_allowed_methods'  => false,
				),
			)
		);

		$result = $this->myxmlrpcserver->wp_getPost( array( 1, 'author', 'author', $this->post_id ) );

		\REST_XMLRPC_Data_Checker\Utils::is_wp_version( '4.8' ) ?
			$this->assertNotIXRError( $result )
			: $this->assertEquals( false, $result instanceof IXR_Error );

		$this->assertEquals( $this->post_data['post_title'], $result['post_title'] );
		$this->assertEquals( 'draft', $result['post_status'] );
		$this->assertEquals( 'post', $result['post_type'] );
		$this->assertStringMatchesFormat( '%d', $result['post_author'] );
	}

	public function test_disabled_xmlrpc() {

		// Refresh plugin settings.
		$this->refresh_plugin_settings(
			array(
				'xmlrpc' => array(
					'disable' => true,
				),
			)
		);

		$result = $this->myxmlrpcserver->wp_getPost( array( 1, 'author', 'author', 1 ) );

		\REST_XMLRPC_Data_Checker\Utils::is_wp_version( '4.8' ) ?
			$this->assertIXRError( $result )
			: $this->assertEquals( true, $result instanceof IXR_Error );

		$this->assertEquals( 405, $result->code );
	}

	public function test_unallowed_user() {

		// Refresh plugin settings.
		$this->refresh_plugin_settings(
			array(
				'xmlrpc' => array(
					'disable'                => false,
					'apply_trusted_users'    => true,
					'apply_trusted_networks' => false,
					'apply_allowed_methods'  => false,
				),
			)
		);

		$result = $this->myxmlrpcserver->wp_getPost( array( 1, 'admin', 'admin', $this->post_id ) );
		\REST_XMLRPC_Data_Checker\Utils::is_wp_version( '4.8' ) ?
			$this->assertIXRError( $result )
			: $this->assertEquals( true, $result instanceof IXR_Error );

		$this->assertEquals( 403, $result->code );
	}

	public function test_allowed_user() {

		// Refresh plugin settings.
		$this->refresh_plugin_settings(
			array(
				'xmlrpc' => array(
					'disable'                => false,
					'apply_trusted_users'    => true,
					'apply_trusted_networks' => false,
					'apply_allowed_methods'  => false,
				),
			)
		);

		$result = $this->myxmlrpcserver->wp_getPost( array( 1, 'author', 'author', $this->post_id ) );

		\REST_XMLRPC_Data_Checker\Utils::is_wp_version( '4.8' ) ?
			$this->assertNotIXRError( $result )
			: $this->assertEquals( false, $result instanceof IXR_Error );

		$this->assertEquals( $this->post_data['post_title'], $result['post_title'] );
		$this->assertEquals( 'draft', $result['post_status'] );
		$this->assertEquals( 'post', $result['post_type'] );
		$this->assertStringMatchesFormat( '%d', $result['post_author'] );
	}

	public function test_untrusted_network() {

		// Refresh plugin settings.
		$this->refresh_plugin_settings(
			array(
				'xmlrpc' => array(
					'disable'                => false,
					'apply_trusted_networks' => true,
					'apply_trusted_users'    => false,
					'trusted_networks'       => '10.0.0.1/32',
					'apply_allowed_methods'  => false,
				),
			)
		);

		$result = $this->myxmlrpcserver->wp_getPost( array( 1, 'author', 'author', $this->post_id ) );

		\REST_XMLRPC_Data_Checker\Utils::is_wp_version( '4.8' ) ?
			$this->assertIXRError( $result )
			: $this->assertEquals( true, $result instanceof IXR_Error );

		$this->assertEquals( 405, $result->code );
	}

	public function test_trusted_network() {

		// Refresh plugin settings.
		$this->refresh_plugin_settings(
			array(
				'xmlrpc' => array(
					'disable'                => false,
					'apply_trusted_networks' => true,
					'apply_trusted_users'    => false,
					'trusted_networks'       => '127.0.0.1/32',
					'apply_allowed_methods'  => false,
				),
			)
		);

		$result = $this->myxmlrpcserver->wp_getPost( array( 1, 'author', 'author', $this->post_id ) );

		\REST_XMLRPC_Data_Checker\Utils::is_wp_version( '4.8' ) ?
			$this->assertNotIXRError( $result )
			: $this->assertEquals( false, $result instanceof IXR_Error );

		$this->assertEquals( $this->post_data['post_title'], $result['post_title'] );
		$this->assertEquals( 'draft', $result['post_status'] );
		$this->assertEquals( 'post', $result['post_type'] );
		$this->assertStringMatchesFormat( '%d', $result['post_author'] );
	}

	public function test_untrusted_network_proxied() {

		// Refresh plugin settings.
		$this->refresh_plugin_settings(
			array(
				'xmlrpc' => array(
					'disable'                => false,
					'apply_trusted_networks' => true,
					'apply_trusted_users'    => false,
					'trusted_networks'       => '127.0.0.1/32',
					'apply_allowed_methods'  => false,
				),
				'options' => array(
					'check_forwarded_remote_ip' => true,	
				),
			)
		);

		$_SERVER['HTTP_X_FORWARDED_FOR'] = '102.102.102.102, 10.0.0.10';
		$result = $this->myxmlrpcserver->wp_getPost( array( 1, 'author', 'author', $this->post_id ) );

		\REST_XMLRPC_Data_Checker\Utils::is_wp_version( '4.8' ) ?
			$this->assertIXRError( $result )
			: $this->assertEquals( true, $result instanceof IXR_Error );

		$this->assertEquals( 405, $result->code );
	}

	public function test_unallowed_method() {

		// Refresh plugin settings.
		$this->refresh_plugin_settings(
			array(
				'xmlrpc' => array(
					'disable'                => false,
					'apply_trusted_networks' => false,
					'apply_trusted_users'    => false,
					'apply_allowed_methods'  => true,
					'allowed_methods' => array(
						'wp.getPage'
					),
				),
			)
		);

		$result = $this->myxmlrpcserver->call( 'wp.getPost', '' );

		\REST_XMLRPC_Data_Checker\Utils::is_wp_version( '4.8' ) ?
			$this->assertIXRError( $result )
			: $this->assertEquals( true, $result instanceof IXR_Error );
	}

	public function test_allowed_method() {

		// Refresh plugin settings.
		$this->refresh_plugin_settings(
			array(
				'xmlrpc' => array(
					'disable'                => false,
					'apply_trusted_networks' => false,
					'apply_trusted_users'    => false,
					'apply_allowed_methods'  => true,
					'allowed_methods' => array(
						'wp.getPost'
					),
				),
			)
		);

		$result = $this->myxmlrpcserver->call( 'wp.getPost', '' );

		\REST_XMLRPC_Data_Checker\Utils::is_wp_version( '4.8' ) ?
			$this->assertIXRError( $result )
			: $this->assertEquals( true, $result instanceof IXR_Error );
	}

	/**
	 * Filter hook to change plugin setting at runtime.
	 *
	 * @param $settings
	 * @return array
	 */
	public function filter_rest_xmlrpc_data_checker_settings ( $settings ) {
		$settings = \REST_XMLRPC_Data_Checker\Utils::wp_parse_args_recursive(
			$this->rest_xmlrpc_data_checker_settings,
			$settings
		);
		return $settings;
	}

	/**
	 * Refresh plugin settings.
	 */
	public function refresh_plugin_settings( $new_settings = array() ) {

		$this->rest_xmlrpc_data_checker_settings = $new_settings;

		add_filter( 'rest_xmlrpc_data_checker_settings', array( $this, 'filter_rest_xmlrpc_data_checker_settings' ) );

		\REST_XMLRPC_Data_Checker::refresh_plugin_settings();

		remove_filter( 'rest_xmlrpc_data_checker_settings', array( $this, 'filter_rest_xmlrpc_data_checker_settings' ) );
	}

}

<?php
/**
 * Unit tests covering plugin REST functionality.
 *
 * @package rest-xmlrpc-data-checker
 * @subpackage REST API
 */

// Only run test if supported class.
if ( class_exists( 'WP_Test_REST_Controller_Testcase' ) ) {

	/**
	 * Class WP_Test_REST_Controller.
	 *
	 * @group restapi
	 */
	class WP_Test_REST_Controller extends WP_Test_REST_Controller_Testcase {

		protected static $superadmin_id;
		protected static $admin_id;
		protected static $editor_id;
		protected static $subscriber_id;
		protected static $author_id;

		protected static $post_id;
		protected static $password_id;
		protected static $private_id;
		protected static $draft_id;
		protected static $trash_id;
		protected static $approved_id;
		protected static $hold_id;

		/**
		 * Plugin settings.
		 *
		 * @var array
		 */
		protected $rest_xmlrpc_data_checker_settings = array();

		/**
		 * Class seteup hook.
		 *
		 * @param \WP_UnitTest_Factory $factory Factory instance.
		 */
		public static function wpSetUpBeforeClass( $factory ) {
			self::$superadmin_id = $factory->user->create(
				array(
					'role'       => 'administrator',
					'user_login' => 'superadmin',
				)
			);
			self::$admin_id      = $factory->user->create(
				array(
					'role' => 'administrator',
				)
			);
			self::$editor_id     = $factory->user->create(
				array(
					'user_login' => 'editor',
					'role'       => 'editor',
				)
			);
			self::$subscriber_id = $factory->user->create(
				array(
					'role' => 'subscriber',
				)
			);
			self::$author_id     = $factory->user->create(
				array(
					'role'         => 'author',
					'display_name' => 'Sea Captain',
					'first_name'   => 'Horatio',
					'last_name'    => 'McCallister',
					'user_email'   => 'captain@thefryingdutchman.com',
					'user_url'     => 'http://thefryingdutchman.com',
				)
			);

			self::$post_id     = $factory->post->create(
				array(
					'post_title' => 'Test rest post',
				)
			);
			self::$private_id  = $factory->post->create(
				array(
					'post_status' => 'private',
				)
			);
			self::$password_id = $factory->post->create(
				array(
					'post_password' => 'toomanysecrets',
				)
			);
			self::$draft_id    = $factory->post->create(
				array(
					'post_status' => 'draft',
				)
			);
			self::$trash_id    = $factory->post->create(
				array(
					'post_status' => 'trash',
				)
			);

			self::$approved_id = $factory->comment->create(
				array(
					'comment_approved' => 1,
					'comment_post_ID'  => self::$post_id,
					'user_id'          => 0,
				)
			);
			self::$hold_id     = $factory->comment->create(
				array(
					'comment_approved' => 0,
					'comment_post_ID'  => self::$post_id,
					'user_id'          => self::$subscriber_id,
				)
			);
		}

		/**
		 * Class tear down hook.
		 */
		public static function wpTearDownAfterClass() {
			self::delete_user( self::$superadmin_id );
			self::delete_user( self::$admin_id );
			self::delete_user( self::$editor_id );
			self::delete_user( self::$subscriber_id );
			self::delete_user( self::$author_id );

			wp_delete_post( self::$post_id, true );
			wp_delete_post( self::$private_id, true );
			wp_delete_post( self::$password_id, true );
			wp_delete_post( self::$draft_id, true );
			wp_delete_post( self::$trash_id, true );
			wp_delete_post( self::$approved_id, true );
			wp_delete_post( self::$hold_id, true );
		}

		/**
		 * Setup hook.
		 */
		public function setUp() {
			parent::setUp();
			if ( is_multisite() ) {
				update_site_option( 'site_admins', array( 'superadmin' ) );
			}

			// Gives REST cap to 'editor'.
			$current_user = get_user_by( 'id', self::$editor_id );
			$current_user->add_cap( 'rest_xmlrpc_data_checker_rest_enable' );
		}

		/**
		 * Tear down hook.
		 */
		public function tearDown() {
			parent::tearDown();

			// Removes XML-RPC cap to 'author'.
			$current_user = get_user_by( 'id', self::$editor_id );
			$current_user->remove_cap( 'rest_xmlrpc_data_checker_rest_enable' );
		}

		/**
		 * Test routes.
		 */
		public function test_register_routes() {
			$routes = rest_get_server()->get_routes();

			$this->assertArrayHasKey( '/wp/v2/posts', $routes );
			$this->assertArrayHasKey( '/wp/v2/posts/(?P<id>[\d]+)', $routes );
		}

		/**
		 * Rest test placeholder.
		 */
		public function test_context_param() {
			$this->assertEquals( true, true );
		}

		/**
		 * Rest test placeholder.
		 */
		public function test_registered_query_params() {
			$this->assertEquals( true, true );
		}

		/**
		 * Rest test placeholder.
		 */
		public function test_get_items() {
			$this->assertEquals( true, true );
		}
		/**
		 * Test get.
		 */
		public function test_get_item() {
			$response = rest_get_server()->serve_request( sprintf( '/wp/v2/posts/%d', self::$post_id ) );
			$result   = json_decode( rest_get_server()->sent_body, true );
			$this->assertEquals( self::$post_id, $result['id'] );
		}

		/**
		 * Rest test placeholder.
		 */
		public function test_create_item() {
			$this->assertEquals( true, true );
		}

		/**
		 * Rest test placeholder.
		 */
		public function test_update_item() {
			$this->assertEquals( true, true );
		}

		/**
		 * Rest test placeholder.
		 */
		public function test_delete_item() {
			$this->assertEquals( true, true );
		}

		/**
		 * Rest test placeholder.
		 */
		public function test_prepare_item() {
			$this->assertEquals( true, true );
		}

		/**
		 * Rest test placeholder.
		 */
		public function test_get_item_schema() {
			$this->assertEquals( true, true );
		}

		/**
		 * Check disabled REST with no checks.
		 */
		public function test_disabled_rest_for_unlogged_users_without_check() {

			// Refresh plugin settings.
			$this->refresh_plugin_settings(
				array(
					'rest' => array(
						'disable' => true,
					),
				)
			);

			$response = rest_get_server()->serve_request( sprintf( '/wp/v2/posts/%d', self::$post_id ) );
			$result   = json_decode( rest_get_server()->sent_body, true );
			$this->assertEquals( 401, $result['data']['status'] );
		}

		/**
		 * Check requests coming from trusted networks.
		 */
		public function test_disabled_rest_trusted_network() {

			// Refresh plugin settings.
			$this->refresh_plugin_settings(
				array(
					'rest' => array(
						'disable'                => true,
						'trusted_networks'       => '127.0.0.1/32',
						'apply_trusted_networks' => true,
					),
				)
			);

			$response = rest_get_server()->serve_request( sprintf( '/wp/v2/posts/%d', self::$post_id ) );
			$result   = json_decode( rest_get_server()->sent_body, true );
			$this->assertEquals( self::$post_id, $result['id'] );
		}

		/**
		 * Check requests coming from trusted networks.
		 */
		public function test_disabled_rest_trusted_network_with_comments() {

			// Refresh plugin settings.
			$this->refresh_plugin_settings(
				array(
					'rest' => array(
						'disable'                => true,
						'trusted_networks'       => '
						// Localhost
						127.0.0.1/32  // Really, localhost!
						# Other network
						10.0.0.0/24   # Really other networks
						',
						'apply_trusted_networks' => true,
					),
				)
			);

			$response = rest_get_server()->serve_request( sprintf( '/wp/v2/posts/%d', self::$post_id ) );
			$result   = json_decode( rest_get_server()->sent_body, true );
			$this->assertEquals( self::$post_id, $result['id'] );
		}

		/**
		 * Check requests coming from trusted networks using proxied IP.
		 */
		public function test_disabled_rest_trusted_network_proxied() {

			// Refresh plugin settings.
			$this->refresh_plugin_settings(
				array(
					'rest'    => array(
						'disable'                => true,
						'trusted_networks'       => '10.0.0.0/24',
						'apply_trusted_networks' => true,
					),
					'options' => array(
						'check_forwarded_remote_ip' => true,
					),
				)
			);

			$_SERVER['HTTP_X_FORWARDED_FOR'] = '102.102.102.102, 10.0.0.10';

			$response = rest_get_server()->serve_request( sprintf( '/wp/v2/posts/%d', self::$post_id ) );
			$result   = json_decode( rest_get_server()->sent_body, true );
			$this->assertEquals( self::$post_id, $result['id'] );
		}

		/**
		 * Check fail of untrusted requests.
		 */
		public function test_disabled_rest_untrusted_network() {

			// Refresh plugin settings.
			$this->refresh_plugin_settings(
				array(
					'rest' => array(
						'disable'                => true,
						'trusted_networks'       => '127.0.0.2/32',
						'apply_trusted_networks' => true,
					),
				)
			);

			$response = rest_get_server()->serve_request( sprintf( '/wp/v2/posts/%d', self::$post_id ) );
			$result   = json_decode( rest_get_server()->sent_body, true );
			$this->assertEquals( 403, $result['data']['status'] );
		}

		/**
		 * Test allowed routes.
		 */
		public function test_disabled_rest_allowed_routes() {

			global $wp;

			// Refresh plugin settings.
			$this->refresh_plugin_settings(
				array(
					'rest' => array(
						'disable'              => true,
						'apply_allowed_routes' => true,
						'allowed_routes'       => array(
							'/wp/v2/posts/(?P<id>[\d]+)',
						),
					),
				)
			);

			$_SERVER['PATH_INFO']         = sprintf( '/wp/v2/posts/%d', self::$post_id );
			$wp->query_vars['rest_route'] = $_SERVER['PATH_INFO'];

			$response = rest_get_server()->serve_request();
			$result   = json_decode( rest_get_server()->sent_body, true );
			$this->assertEquals( self::$post_id, $result['id'] );
		}

		/**
		 * Test unallowed routes.
		 */
		public function test_disabled_rest_unallowed_routes() {

			global $wp;

			// Refresh plugin settings.
			$this->refresh_plugin_settings(
				array(
					'rest' => array(
						'disable'              => true,
						'apply_allowed_routes' => true,
						'allowed_routes'       => array(
							'/wp/v2/posts/(?P<id>[\d]+)',
						),
					),
				)
			);

			$_SERVER['PATH_INFO']         = sprintf( '/wp/v2/pages/%d', self::$post_id );
			$wp->query_vars['rest_route'] = $_SERVER['PATH_INFO'];

			$response = rest_get_server()->serve_request();
			$result   = json_decode( rest_get_server()->sent_body, true );
			$this->assertEquals( 404, $result['data']['status'] );
		}

		/**
		 * Test trusted networks.
		 */
		public function test_disabled_rest_for_logged_users() {

			global $wp;
			// Refresh plugin settings.
			$this->refresh_plugin_settings(
				array(
					'rest' => array(
						'disable'                => true,
						'trusted_networks'       => '127.0.0.2/32',
						'apply_trusted_networks' => true,
					),
				)
			);

			// Log in.
			wp_set_current_user( self::$editor_id );

			$response = rest_get_server()->serve_request( sprintf( '/wp/v2/posts/%d', self::$post_id ) );
			$result   = json_decode( rest_get_server()->sent_body, true );
			$this->assertEquals( self::$post_id, $result['id'] );
		}

		/**
		 * Test basic authentication.
		 */
		public function test_disabled_rest_basic_authentication() {

			// Refresh plugin settings.
			$this->refresh_plugin_settings(
				array(
					'rest' => array(
						'disable'     => true,
						'auth_method' => 'basic_auth',
					),
				)
			);

			$_SERVER['PHP_AUTH_USER'] = 'editor';
			$_SERVER['PHP_AUTH_PW']   = 'password';

			$response = rest_get_server()->serve_request( sprintf( '/wp/v2/posts/%d', self::$post_id ) );
			$result   = json_decode( rest_get_server()->sent_body, true );
			$this->assertEquals( self::$post_id, $result['id'] );
		}

		/**
		 * Check wrong basic auth credentials.
		 */
		public function test_disabled_rest_basic_authentication_fails() {

			// Refresh plugin settings.
			$this->refresh_plugin_settings(
				array(
					'rest' => array(
						'disable'     => true,
						'auth_method' => 'basic_auth',
					),
				)
			);

			$_SERVER['PHP_AUTH_USER'] = 'editor';
			$_SERVER['PHP_AUTH_PW']   = 'idunno';

			$response = rest_get_server()->serve_request( sprintf( '/wp/v2/posts/%d', self::$post_id ) );
			$result   = json_decode( rest_get_server()->sent_body, true );
			$this->assertEquals( 401, $result['data']['status'] );
		}

		/**
		 * Test JSONP.
		 */
		public function test_disabled_jsonp() {

			// Refresh plugin settings.
			$this->refresh_plugin_settings(
				array(
					'rest' => array(
						'disable_jsonp' => true,
					),
				)
			);

			// Make a JSONP request.
			$_GET['_jsonp'] = 'my_func';

			$response = rest_get_server()->serve_request( sprintf( '/wp/v2/posts/%d', self::$post_id ) );
			$result   = json_decode( rest_get_server()->sent_body, true );
			$this->assertEquals( 'rest_callback_disabled', $result['code'] );
		}

		/**
		 * Filter hook to change plugin setting at runtime.
		 *
		 * @param array $settings Plugin settings.
		 *
		 * @return array
		 */
		public function filter_rest_xmlrpc_data_checker_settings( $settings ) {
			$settings = \REST_XMLRPC_Data_Checker\Utils::wp_parse_args_recursive(
				$this->rest_xmlrpc_data_checker_settings,
				$settings
			);
			return $settings;
		}

		/**
		 * Refresh plugin settings.
		 *
		 * @param array $new_settings Plugin new settings.
		 */
		public function refresh_plugin_settings( $new_settings = array() ) {

			$this->rest_xmlrpc_data_checker_settings = $new_settings;

			add_filter( 'rest_xmlrpc_data_checker_settings', array( $this, 'filter_rest_xmlrpc_data_checker_settings' ) );

			\REST_XMLRPC_Data_Checker::refresh_plugin_settings();

			remove_filter( 'rest_xmlrpc_data_checker_settings', array( $this, 'filter_rest_xmlrpc_data_checker_settings' ) );
		}
	}

}


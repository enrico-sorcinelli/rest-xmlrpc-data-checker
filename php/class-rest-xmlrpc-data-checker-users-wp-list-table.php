<?php
/**
 * Plugin WP_List_Table classes.
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

// Since the WP_List_Table isn't loaded automatically.
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Create users list that extends the core WP_List_Table class.
 */
class Users_WP_List_Table extends \WP_List_Table {

	/**
	 * Users_WP_List_Table constructor.
	 *
	 * @param array $args {
	 *     In addition to parent class there are following arguments.
	 *     @type string $cb_name
	 *     @type string $table_class
	 * }
	 */
	public function __construct( $args = array() ) {
		global $status, $page;

		// Defaults.
		$args = wp_parse_args(
			$args,
			array(
				'cb_name'     => 'id',
				'table_class' => '',
				'cb_meta_key' => '',
			)
		);
		// Set parent defaults.
		parent::__construct( $args );
	}

	/**
	 * Return columns definitions.
	 *
	 * @return array
	 */
	public function get_columns() {
		$columns = array(
			'cb'           => '<input type="checkbox" />',
			'user_login'   => __( 'Username', 'rest-xmlrpc-data-checker' ),
			'display_name' => __( 'Name', 'rest-xmlrpc-data-checker' ),
			'roles'        => __( 'Role', 'rest-xmlrpc-data-checker' ),
		);
		return $columns;
	}

	/**
	 * Prepares the list of items for displaying.
	 *
	 * @param array $args Arguments.
	 */
	public function prepare_items( $args = array() ) {

		// Pagination.
		$total_items = count( $args['items'] );
		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $total_items,
			)
		);

		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->items           = $args['items'];
	}

	/**
	 * This method is called when the parent class can't find a method
	 * specifically build for a given column.
	 *
	 * @param array $item        A singular item (one full row's worth of data).
	 * @param array $column_name The name/slug of the column to be processed.
	 *
	 * @return string Text or HTML to be placed inside the column <td>.
	 */
	protected function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'roles':
				return $item[ $column_name ];
			default:
				return $item['data']->$column_name;
		}
	}

	/**
	 * Checkbox code.
	 *
	 * @param array $item Current item data.
	 *
	 * @return string
	 */
	protected function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%s" value="%s" %s/>',
			$this->_args['cb_name'],
			$item['data']->ID,
			empty( $item['meta'][ $this->_args['cb_meta_key'] ] ) ? '' : 'checked'
		);
	}

	/**
	 * Add class to table.
	 *
	 * @return array
	 */
	public function get_table_classes() {
		$classes = parent::get_table_classes();
		if ( ! empty( $this->_args['table_class'] ) ) {
			$classes[] = $this->_args['table_class'];
		}
		return $classes;
	}

	/**
	 * Set no items message.
	 */
	public function no_items() {
		esc_html_e( 'No items found.' );
	}

	/**
	 * Raises table output nonce codes.
	 *
	 * @param string $which Display on top or bottom.
	 */
	protected function display_tablenav( $which ) {
		parent::display_tablenav( 'bottom' );
	}
}


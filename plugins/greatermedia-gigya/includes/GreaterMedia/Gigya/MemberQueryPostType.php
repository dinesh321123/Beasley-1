<?php

namespace GreaterMedia\Gigya;

/**
 * MemberQueryPostType manages registration of the `member_query` post
 * type with WordPress.
 *
 * @package GreaterMedia\Gigya
 */
class MemberQueryPostType {

	/**
	 * Metaboxes for this post type. These are created on demand.
	 *
	 * @access public
	 * @var array
	 */
	public $meta_boxes = array();

	/**
	 * Registers the `member_query` custom post type.
	 *
	 * @access public
	 * @return void
	 */
	public function register() {
		$name = $this->get_post_type_name();

		register_post_type(
			$name, $this->get_options()
		);

		register_post_type(
			"{$name}_preview", $this->get_preview_options()
		);
	}

	/**
	 * Lazy initializes the meta boxes for this post_type. This keeps the
	 * footprint down on the POST request, since we don't need to
	 * register the meta boxes there.
	 *
	 * For the POST request, we'll use null data. For those
	 * requests the meta boxes only do nonce verification.
	 *
	 * @access public
	 * @param mixed $data
	 * @return array Associative array of meta box objects
	 */
	public function get_meta_boxes( $data = null ) {
		if ( count( $this->meta_boxes ) === 0 ) {
			$this->meta_boxes = array();

			$this->meta_boxes['preview'] = $this->meta_box_for(
				array(
					'id'       => 'preview',
					'title'    => __( 'Preview Results', 'gmr_gigya' ),
					'context'  => 'side',
					'priority' => 'default',
					'template' => 'preview',
				),
				$data
			);

			$this->meta_boxes['query_builder'] = $this->meta_box_for(
				array(
					'id'       => 'query_builder',
					'title'    => __( 'Gigya Social', 'gmr_gigya' ),
					'context'  => 'normal',
					'priority' => 'default',
					'template' => 'query_builder',
				),
				$data
			);

		}

		return $this->meta_boxes;
	}

	/**
	 * Registers the meta boxes with the associated data object.
	 *
	 * @access public
	 * @param mixed $data The data to associate with a metabox.
	 * @return void
	 */
	public function register_meta_boxes( $data ) {
		$meta_boxes = $this->get_meta_boxes( $data );

		foreach ( $meta_boxes as $meta_box ) {
			$meta_box->register();
		}
	}

	/**
	 * Verifies than correct nonces were passed for each MetaBox.
	 *
	 * Exits script execution with a warning if invalid.
	 *
	 * @access public
	 * @return void
	 */
	public function verify_meta_box_nonces() {
		$meta_boxes = $this->get_meta_boxes( null );

		foreach ( $meta_boxes as $meta_box ) {
			$result = $meta_box->verify_nonce();
			// only runs in PHPUnit, since the script has already
			// ended execution before, if the nonce was invalid
			if ( ! $result ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Returns the labels for the `member_query` post type.
	 *
	 * @access public
	 * @return void
	 */
	function get_labels() {
		return array(
			'name'               => _x( 'Member Query', 'post type general name', 'gmr_gigya' ),
			'singular_name'      => _x( 'Member Query', 'post type singular name', 'gmr_gigya' ),
			'menu_name'          => _x( 'Member Queries', 'admin menu', 'gmr_gigya' ),
			'name_admin_bar'     => _x( 'Member Query', 'add new on admin bar', 'gmr_gigya' ),
			'add_new'            => _x( 'Add New', 'member query', 'gmr_gigya' ),
			'add_new_item'       => __( 'Add New Member Query', 'gmr_gigya' ),
			'new_item'           => __( 'New Member Query', 'gmr_gigya' ),
			'edit_item'          => __( 'Edit Member Query', 'gmr_gigya' ),
			'view_item'          => __( 'View Member Query', 'gmr_gigya' ),
			'all_items'          => __( 'All Member Queries', 'gmr_gigya' ),
			'search_items'       => __( 'Search Member Queries', 'gmr_gigya' ),
			'parent_item_colon'  => __( 'Parent Member Queries:', 'gmr_gigya' ),
			'not_found'          => __( 'No member queries found.', 'gmr_gigya' ),
			'not_found_in_trash' => __( 'No member queries found in Trash.', 'gmr_gigya' )
		);
	}

	/**
	 * Returns the configuration options for the `member_query` post
	 * type.
	 *
	 * @access public
	 * @return array
	 */
	public function get_options() {
		return array(
			'labels'             => $this->get_labels(),
			'supports'           => $this->get_supports(),
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => false,
			'can_export'         => true,
			'rewrite'            => false,
			'capability_type'    => 'page',
			'has_archive'        => false,
			'hierarchical'       => false,
		);
	}

	public function get_preview_options() {
		return array(
			'public'              => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'show_ui'             => false,
			'can_export'          => false,
		);
	}

	/**
	 * The `member_query` CPT only uses the title support. Rest of the
	 * meta boxes are custom.
	 *
	 * @access public
	 * @return void
	 */
	public function get_supports() {
		return array( 'title' );
	}

	/**
	 * The post type name for the MemberQuery CPT.
	 *
	 * @access public
	 * @return string
	 */
	public function get_post_type_name() {
		return 'member_query';
	}

	/* Helpers */
	/**
	 * Builds a new meta box for the specified params.
	 *
	 * @access public
	 * @param array $params The params to pass to the meta box object
	 * @param mixed $data The data associated with the meta box.
	 * @return MetaBox
	 */
	public function meta_box_for( $params, $data ) {
		$meta_box = new MetaBox( $data );
		$meta_box->params = $params;

		return $meta_box;
	}
}

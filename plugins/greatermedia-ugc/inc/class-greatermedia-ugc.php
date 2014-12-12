<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

/**
 * Class GreaterMediaUserGeneratedContent
 * Implements functionality for Listener Submissions / User-Generated Content and can be instantiated to represent a
 * particular post.
 *
 * To change how UGC is rendered in different contexts, subclass this class and name your new class
 * 'GreaterMediaUserGenerated{$post_format}' (i.e. 'GreaterMediaUserGeneratedGallery') then override the
 * render_* methods.
 */
class GreaterMediaUserGeneratedContent {

	protected $post_id;
	protected $post;

	/**
	 * Constructor is protected so it's only called from the factory method for_post_id() or a child class
	 *
	 * @param int $post_id
	 */
	protected function __construct( $post_id ) {

		$this->post_id = $post_id;
		$this->post    = get_post( $post_id );

	}

	/**
	 * Set up hooks to register the custom post type and add its screens to the admin menus
	 */
	public static function register_cpt() {

		add_action( 'init', array( __CLASS__, 'user_generated_content' ), 0 );
		add_action( 'init', array( __CLASS__, 'admin_endpoints' ) );
		add_action( 'wp', array( __CLASS__, 'wp' ), 100 );
		add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ), 0 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue_scripts' ) );

		add_filter( 'gmr_live_link_add_copy_action', array( __CLASS__, 'remove_copy_to_live_link_action' ), 10, 2 );

	}

	/**
	 * Register the custom post type
	 */
	public static function user_generated_content() {

		$labels = array(
			'name'               => _x( 'Listener Submissions', 'Post Type General Name', 'greatermedia_ugc' ),
			'singular_name'      => _x( 'Listener Submission', 'Post Type Singular Name', 'greatermedia_ugc' ),
			'menu_name'          => __( 'Listener Submissions', 'greatermedia_ugc' ),
			'parent_item_colon'  => __( 'Parent Submission:', 'greatermedia_ugc' ),
			'all_items'          => __( 'All Submissions', 'greatermedia_ugc' ),
			'view_item'          => __( 'View Submission', 'greatermedia_ugc' ),
			'add_new_item'       => __( 'Add New Submission', 'greatermedia_ugc' ),
			'add_new'            => __( 'Add New', 'greatermedia_ugc' ),
			'edit_item'          => __( 'Edit Submission', 'greatermedia_ugc' ),
			'update_item'        => __( 'Update Submission', 'greatermedia_ugc' ),
			'search_items'       => __( 'Search Submission', 'greatermedia_ugc' ),
			'not_found'          => __( 'Not found', 'greatermedia_ugc' ),
			'not_found_in_trash' => __( 'Not found in Trash', 'greatermedia_ugc' ),
		);
		$args   = array(
			'label'               => __( 'user_generated_content', 'greatermedia_ugc' ),
			'description'         => __( 'Listener Submissions', 'greatermedia_ugc' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'custom-fields', 'post-formats', ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => false,
			'menu_position'       => 5,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'capability_type'     => 'page',
		);
		register_post_type( 'listener_submissions', $args );

	}

	/**
	 * Checks whether or not to add "Copy Live Link" action to the listener submission posts.
	 *
	 * @static
	 * @access public
	 * @filter gmr_live_link_add_copy_action
	 * @param boolean $add_copy_action Determines whether or not to add the action.
	 * @param WP_Post $post The current post object.
	 * @return boolean Initial flag if a post type is not a listener submission pt, otherwise FALSE.
	 */
	public static function remove_copy_to_live_link_action( $add_copy_action, WP_Post $post ) {
		return 'listener_submissions' != $post->post_type ? $add_copy_action : false;
	}
	
	/**
	 * Add custom admin pages to the admin menu
	 */
	public static function admin_menu() {
		add_submenu_page( 'edit.php?post_type=listener_submissions', 'Listener Submission Moderation', 'Listener Submission Moderation', 'delete_posts', GreaterMediaUserGeneratedContentModerationTable::PAGE_NAME, array(
				__CLASS__,
				'moderation_ui'
			) );
	}

	public static function admin_enqueue_scripts() {

		wp_enqueue_style( 'greatermedia-ugc', GREATER_MEDIA_UGC_URL . 'css/greatermedia-ugc-moderation.css' );
		wp_enqueue_script( 'greatermedia-ugc', GREATER_MEDIA_UGC_URL . 'js/greatermedia-ugc-moderation.js', array( 'jquery' ) );

		ob_start();
		include trailingslashit( GREATER_MEDIA_UGC_PATH ) . 'tpl/moderation-approved-row.tpl.php';
		$approved_row = ob_get_clean();

		// AJAX Templates
		$greatermedia_ugc = array(
			'templates' => array(
				'approved' => $approved_row
			),
		);

		wp_localize_script( 'greatermedia-ugc', 'GreaterMediaUGC', $greatermedia_ugc );

	}

	/**
	 * Render the UI for the Moderation page
	 */
	public static function moderation_ui() {

		$wp_list_table = new GreaterMediaUserGeneratedContentModerationTable();
		$wp_list_table->prepare_items();

		$pagenum     = $wp_list_table->get_pagenum();
		$total_pages = $wp_list_table->get_pagination_arg( 'total_pages' );

		if ( $pagenum > $total_pages && $total_pages > 0 ) {
			wp_redirect( add_query_arg( 'paged', $total_pages ) );
			exit;
		}

		include trailingslashit( GREATER_MEDIA_UGC_PATH ) . 'tpl/moderation.tpl.php';

	}

	public static function admin_endpoints() {

		global $wp, $wp_rewrite;
		$wp->add_query_var( 'ugc' );
		$wp->add_query_var( 'ugc_action' );
		$wp->add_query_var( 'ugc_attachment' );
		$wp->add_query_var( 'output' );

		$rewrite_rules = self::rewrite_rules();

		foreach ( $rewrite_rules as $rewrite_regex => $rewrite_target ) {
			add_rewrite_rule( $rewrite_regex, $rewrite_target, 'top' );
		}

		// flush rewrite rules only if our rules is not registered
		$all_registered_rules = $wp_rewrite->wp_rewrite_rules();
		$registered_rules     = array_intersect( $rewrite_rules, $all_registered_rules );
		if ( count( $registered_rules ) !== count( $rewrite_rules ) ) {
			flush_rewrite_rules( true );
		}

	}

	public static function wp() {

		global $wp;

		$rewrite_rules = self::rewrite_rules();
		if ( ! isset( $rewrite_rules[ $wp->matched_rule ] ) ) {
			return;
		}

		$ugc_action = get_query_var( 'ugc_action' );
		if ( empty( $ugc_action ) ) {
			return;
		}

		$output = get_query_var( 'output' );

		if ( 'approve' === $ugc_action ) {

			$ugc_id = intval( get_query_var( 'ugc' ) );
			if ( empty( $ugc_id ) ) {
				return;
			}

			$nonce = isset( $_REQUEST['_wpnonce'] ) ? $_REQUEST['_wpnonce'] : '';
			if ( false === wp_verify_nonce( $nonce, 'approve-ugc_' . $ugc_id ) ) {
				wp_nonce_ays( 'approve-ugc_' . $ugc_id );
			}

			$ugc = self::for_post_id( $ugc_id );
			$ugc->approve();

			if ( '.json' === $output ) {
				wp_send_json_success( array( 'ids' => $ugc_id ) );
			} else {
				// Default to interactive moderation. Redirect back to the Moderation screen.
				wp_redirect(
					add_query_arg(
						'page',
						'moderate-ugc',
						add_query_arg(
							'post_type',
							'listener_submissions',
							admin_url( 'edit.php' )
						)
					)
				);
			}
		} elseif ( 'gallery-delete' === $ugc_action ) {

			$ugc_id = intval( get_query_var( 'ugc' ) );
			if ( empty( $ugc_id ) ) {
				return;
			}

			$ugc_attachment_id = intval( get_query_var( 'ugc_attachment' ) );
			if ( empty( $ugc_attachment_id ) ) {
				return;
			}

			$nonce = isset( $_REQUEST['_wpnonce'] ) ? $_REQUEST['_wpnonce'] : '';
			if ( false === wp_verify_nonce( $nonce, 'trash-ugc-gallery_' . $ugc_attachment_id ) ) {
				wp_nonce_ays( 'trash-ugc-gallery_' . $ugc_attachment_id );
			}

			$post = get_post( $ugc_id );

			// Trash (don't delete) the attachment
			wp_trash_post( $ugc_attachment_id );

			// Remove this post from the gallery tag
			$attachment_data = get_post_gallery( $ugc_id, false );
			$attachment_ids  = explode( ',', $attachment_data['ids'] );
			$attachment_ids  = array_diff( $attachment_ids, array( $ugc_attachment_id ) );

			$post->post_content = sprintf( '[gallery ids="%s"]', implode( ',', $attachment_ids ) );
			wp_update_post( $post );

			if ( class_exists( 'GreaterMediaAdminNotifier' ) ) {
				GreaterMediaAdminNotifier::message( __( 'Removed image', 'greatermedia_ugc' ) );
			}

			if ( '.json' === $output ) {
				wp_send_json_success( array( 'ids' => $ugc_id ) );
			} else {
				wp_redirect(
					add_query_arg(
						'page',
						'moderate-ugc',
						add_query_arg(
							'post_type',
							'listener_submissions',
							admin_url( 'edit.php' )
						)
					) . '#ugc-' . $ugc_id
				);
			}

		} elseif ( 'bulk' === $ugc_action ) {

			// Nonce check
			if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulk-' . 'submissions' ) ) {
				wp_nonce_ays( '_wpnonce' );
			}

			$ugc_ids = get_query_var( 'ugc' );
			if ( ! is_array( $ugc_ids ) ) {
				$ugc_ids = array( $ugc_ids );
			}

			if ( empty( $ugc_ids ) ) {
				return;
			}

			$action = ( $_REQUEST['action'] != - 1 ) ? $_REQUEST['action'] : $_REQUEST['action2'];

			if ( 'approve' === $action ) {

				// Approve each post
				foreach ( $ugc_ids as $ugc_id ) {
					$ugc = self::for_post_id( $ugc_id );
					$ugc->approve();
				}

				if ( '.json' === $output ) {
					wp_send_json_success( array( $$ugc_ids ) );
				} else {
					// Default to interactive moderation. Redirect back to the Moderation screen.
					wp_redirect(
						add_query_arg(
							'page',
							'moderate-ugc',
							add_query_arg(
								'post_type',
								'listener_submissions',
								admin_url( 'edit.php' )
							)
						)

					);

				}

			} elseif ( 'trash' === $action ) {

				// Trash each post
				array_map( 'wp_trash_post', $ugc_ids );

				// Redirect back to the Moderation screen
				wp_redirect(
					add_query_arg(
						'page',
						'moderate-ugc',
						add_query_arg(
							'post_type',
							'listener_submissions',
							admin_url( 'edit.php' )
						)
					)

				);

			}

		}

	}

	/**
	 * Return an instance of this class or an appropriate subclass based on Post Format
	 *
	 * @param int $post_id Post ID
	 *
	 * @return GreaterMediaUserGeneratedContent
	 */
	public static function for_post_id( $post_id ) {

		$post_formats = wp_get_post_terms( $post_id, 'post_format' );
		$post_format  = array_pop( $post_formats );
		if ( $post_format ) {
			$potential_subclass_name = 'GreaterMediaUserGenerated' . ucfirst( $post_format->name );
		}

		if ( isset( $potential_subclass_name ) && is_subclass_of( $potential_subclass_name, __CLASS__ ) ) {
			$ugc = new $potential_subclass_name( $post_id );
		} else {
			$ugc = new self( $post_id );
		}

		return $ugc;

	}

	/**
	 * Approve this User Generated Content
	 */
	public function approve() {

		$this->post->post_status = 'publish';
		wp_update_post( $this->post );

	}

	/**
	 * Retrieve a list of rewrite rules this class implements
	 * @return array
	 */
	public static function rewrite_rules() {

		static $rewrite_rules;
		if ( ! isset( $rewrite_rules ) ) {

			$rewrite_rules = array(
				'^ugc/bulk'                          => 'index.php?ugc_action=bulk&ugc=$matches[1]',
				'^ugc/(.*)/approve(.*)?'             => 'index.php?ugc_action=approve&ugc=$matches[1]&output=$matches[2]',
				'^ugc/(.*)/gallery/(.*)/delete(.*)?' => 'index.php?ugc_action=gallery-delete&ugc=$matches[1]&ugc_attachment=$matches[2]&output=$matches[3]',
			);

		}

		return $rewrite_rules;

	}

	/**
	 * Retrieve the contest associated with this User Generated Content
	 *
	 * @return null|WP_Post
	 */
	public function contest() {

		$contest_id = get_post_meta( $this->post_id, '_ugc_contest', true );

		if ( ! empty( $contest_id ) ) {
			$contest = get_post( $contest_id );

			return $contest;
		}

		return null;

	}

	public function listener_gigya_id() {

		$listener_gigya_id = get_post_meta( $this->post_id, '_ugc_listener_gigya_id', true );

		return $listener_gigya_id;

	}

	public function listener_name() {

		$listener_name = get_post_meta( $this->post_id, '_ugc_listener_name', true );

		return $listener_name;

	}

	/**
	 * Render a representation of this post appropriate for displaying in the moderation queue
	 *
	 * @return string html
	 */
	public function render_moderation_row() {
		return 'Generic result';
	}

	/**
	 * Render a preview of this UGC suitable for use in the admin
	 *
	 * @return string html
	 */
	public function render_preview() {
		return 'Generic result';
	}

}

GreaterMediaUserGeneratedContent::register_cpt();

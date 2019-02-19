<?php

namespace Bbgi\Integration;

class Dfp extends \Bbgi\Module {

	private static $_sensitive_types = array( 'post', 'gmr_gallery' );

	/**
	 * Registers this module.
	 *
	 * @access public
	 */
	public function register() {
		add_action( 'wp_loaded', $this( 'register_metabox' ), 20 );
		add_filter( 'dfp_single_targeting', $this( 'update_single_targeting' ) );
	}

	/**
	 * Registers meta box.
	 *
	 * @access public
	 */
	public function register_metabox() {
		$fields = $location = array();

		$fields[] = array(
			'key'               => 'field_sensitive_content',
			'label'             => 'Sensitive Content',
			'name'              => 'sensitive_content',
			'type'              => 'true_false',
			'instructions'      => '',
			'required'          => 0,
			'conditional_logic' => 0,
			'message'           => '',
			'default_value'     => 0,
			'ui'                => 1,
			'ui_on_text'        => '',
			'ui_off_text'       => '',
		);

		foreach ( self::$_sensitive_types as $type ) {
			$location[] = array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => $type,
				),
			);
		}

		acf_add_local_field_group( array(
			'key'                   => 'group_dfp_settings',
			'title'                 => 'DFP Settings',
			'fields'                => $fields,
			'location'              => $location,
			'menu_order'            => 0,
			'position'              => 'side',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => 1,
			'description'           => '',
		) );
	}

	/**
	 * Updates single slot targeting.
	 *
	 * @access public
	 * @param array $targeting
	 * @return array
	 */
	public function update_single_targeting( $targeting ) {
		foreach ( self::$_sensitive_types as $type ) {
			if ( is_singular( $type ) ) {
				$field = get_field( 'sensitive_content', get_queried_object_id() );
				if ( filter_var( $field, FILTER_VALIDATE_BOOLEAN ) ) {
					$targeting[] = array( 'sensitive', 'yes' );
				}
			}
		}

		return $targeting;
	}

	public static function get_global_targeting() {
		static $targeting = null;
		if ( ! is_null( $targeting ) ) {
			return $targeting;
		}
	
		$cpage = ! is_home() && ! is_front_page()
			? untrailingslashit( current( explode( '?', $_SERVER['REQUEST_URI'], 2 ) ) ) // strip query part and trailing slash of the current uri
			: 'home';

		$targeting = array(
			array( 'cdomain', parse_url( home_url( '/' ), PHP_URL_HOST ) ),
			array( 'cpage', $cpage ),
			array( 'ctest', trim( get_option( 'dfp_targeting_ctest' ) ) ),
			array( 'genre', trim( get_option( 'dfp_targeting_genre' ) ) ),
			array( 'market', trim( get_option( 'dfp_targeting_market' ) ) ),
		);

		if ( is_singular() ) {
			$post_id = get_queried_object_id();
			$targeting[] = array( 'cpostid', "{$post_id}" );

			$terms = get_the_terms( $post_id, '_shows' );
			if ( is_array( $terms ) && ! empty( $terms ) ) {
				$targeting[] = array( 'shows', implode( ",", wp_list_pluck( $terms, 'slug' ) ) );
			}

			$post = get_post( $post_id );
			$post_type = get_post_type( $post );

			$podcast = false;
			if ( 'podcast' == $post_type ) {
				$podcast = $post->post_name;
			} elseif ( 'episode' == $post_type ) {
				$parent_podcast_id = wp_get_post_parent_id( $post );
				if ( $parent_podcast_id && ! is_wp_error( $parent_podcast_id ) ) {
					$parent_podcast = get_post( $parent_podcast_id );
					$podcast = $parent_podcast->post_name;
				}
			}

			if ( $podcast ) {
				$targeting[] = array( 'podcasts', $podcast );
			}

			$categories = wp_get_post_categories( get_queried_object_id() );
			if ( ! empty( $categories ) ) {
				$categories = array_filter( array_map( 'get_category', $categories ) );
				$categories = wp_list_pluck( $categories, 'slug' );
				$targeting[] = array( 'categories', implode( ',', $categories ) );
			}
		} elseif ( is_category() ) {
			$category = get_queried_object();
			$targeting[] = array( 'categories', $category->slug );
		}

		$targeting = apply_filters( 'dfp_global_targeting', $targeting );

		return $targeting;	
	}

}

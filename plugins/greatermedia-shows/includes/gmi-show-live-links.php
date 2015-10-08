<?php

// action hooks
add_action( 'gmr_live_link_copy_post', 'gmrs_copy_personalities_to_live_link', 10, 2 );
add_action( 'gmr_quicklink_submitbox_misc_actions', 'gmi_shows_render_quicklink_meta_box' );
add_action( 'gmr_quicklink_post_created', 'gmi_shows_set_quicklink_show' );

// filter hooks
add_filter( 'gmr_live_link_taxonomies', 'gmrs_add_live_links_taxonomy_support' );
add_filter( 'gmr_live_link_add_copy_action', 'gmrs_check_live_links_copy_action', 10, 2 );

/**
 * Adds support of shows taxonomy to live links post type.
 *
 * @filter gmr_live_link_taxonomies
 * @param array $taxonomies The array of already supported taxonomies.
 * @return array The extended array of supported taxonomies.
 */
function gmrs_add_live_links_taxonomy_support( $taxonomies ) {
	$taxonomies[] = ShowsCPT::SHOW_TAXONOMY;
	return $taxonomies;
}

/**
 * Copies show terms when a post is copied to live links.
 *
 * @action gmr_live_link_copy_post
 * @param int $ll_id The live link post id.
 * @param int $post_id The copied post id.
 */
function gmrs_copy_personalities_to_live_link( $ll_id, $post_id ) {
	$terms = wp_get_post_terms( $post_id, ShowsCPT::SHOW_TAXONOMY );
	if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
		$terms = array_filter( (array) wp_list_pluck( $terms, 'term_id' ) );
		wp_set_post_terms( $ll_id, $terms, ShowsCPT::SHOW_TAXONOMY );
	}
}

/**
 * Checks whether or not to add copy live link action.
 *
 * @filter gmr_live_link_add_copy_action
 * @param boolean $add_copy_link Initial value.
 * @param WP_Post $post The post object.
 * @return boolean TRUE if we need to add a copy link, otherwise FALSE.
 */
function gmrs_check_live_links_copy_action( $add_copy_link, WP_Post $post ) {
	return ! $add_copy_link ? $add_copy_link : ShowsCPT::SHOW_CPT != $post->post_type;
}

/**
 * Renders shows meta box for quicklink popup.
 */
function gmi_shows_render_quicklink_meta_box() {
	$query = new WP_Query( array(
		'post_type'           => ShowsCPT::SHOW_CPT,
		'posts_per_page'      => 200,
		'no_found_rows'       => true,
		'ignore_sticky_posts' => true,
		'orderby'             => 'title',
		'order'               => 'ASC',
	) );

	$user_show = 0;
	$user_show_tt_id = get_user_option( 'show_tt_id' );
	if ( $user_show_tt_id ) {
		$term = get_term_by( 'term_taxonomy_id', $user_show_tt_id, ShowsCPT::SHOW_TAXONOMY );
		if ( $term ) {
			$user_show = TDS\get_related_post( $term );
			if ( $user_show ) {
				$user_show = $user_show->ID;
			}
		}
	}

	?><p>
		<label for="show">Show:</label>
		<select id="show" class="widefat" name="show">
			<option></option>
			<?php while ( $query->have_posts() ) : ?>
				<?php $show = $query->next_post(); ?>
				<option value="<?php echo esc_attr( $show->ID ); ?>"<?php selected( $show->ID, $user_show ); ?>>
					<?php echo esc_html( $show->post_title ); ?>
				</option>
			<?php endwhile; ?>
		</select>
	</p><?php
}

/**
 * Sets show to the created live link.
 *
 * @filter gmr_quicklink_post_created
 * @param int $post_id The post id.
 */
function gmi_shows_set_quicklink_show( $post_id ) {
	$show = filter_input( INPUT_POST, 'show', FILTER_VALIDATE_INT );
	if ( $show && ( $show = get_post( $show ) ) && ShowsCPT::SHOW_CPT == $show->post_type ) {
		$term = TDS\get_related_term( $show );
		if ( $term ) {
			wp_set_object_terms( $post_id, $term->term_id, ShowsCPT::SHOW_TAXONOMY );
		}
	}
}
<?php

namespace GreaterMedia\AdCodeManager;

add_action( 'wp_head', __NAMESPACE__ . '\wp_head', 10000 );
add_action( 'wp_footer', __NAMESPACE__ . '\load_js' );
add_filter( 'acm_output_html', __NAMESPACE__ . '\render_tag', 15, 2 );
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\load_scripts' );

function load_scripts() {
	wp_enqueue_script( 'jquery' );
}

function wp_head() {
	// Creates global ad object for use later in the rendering process

?>
	<script type="text/javascript">

		function fill_ad( $slot ) {
			var minWidthOk = true,
				maxWidthOk = true;

			if ( $slot.data( 'min-width' ) ) {
				minWidthOk = ( parseInt( $slot.data( 'min-width' ), 10 ) <= parseInt( window.GMRAds.width, 10 ) ) ? true : false;
			}
			if ( $slot.data( 'max-width' ) ) {
				maxWidthOk = ( parseInt( $slot.data( 'max-width' ), 10 ) >= parseInt( window.GMRAds.width, 10 ) ) ? true : false;
			}

			if ( maxWidthOk && minWidthOk ) {
				var OX_12345 = new OX(),
					category = jQuery.trim( $slot.data( 'category' ) );

				OX_12345.addAdUnit( $slot.data( 'openx-id' ) );
				jQuery( $slot ).empty();
				jQuery( $slot ).append( '<div id="' + $slot.attr( 'id' ) + '-placeholder"></div>' )
				OX_12345.setAdUnitSlotId( $slot.data( 'openx-id' ), $slot.attr( 'id' ) + '-placeholder' );
				if ( $slot.data( 'slug' ) )  {
					OX_12345.addVariable( 'slug', $slot.data( 'slug' ) );
				}
				if ( $slot.data( 'shows' ) )  {
					OX_12345.addVariable( 'shows', $slot.data( 'shows' ) );
				}
				if ( $slot.data( 'podcast' ) )  {
					OX_12345.addVariable( 'podcast', $slot.data( 'podcast' ) );
				}
				if ( category ) {
					OX_12345.addVariable( 'category', category );
				}

				OX_12345.load();

				$slot.addClass( 'gmr-ad-filled' );
			}
		}

		function fill_ads() {
			jQuery( '.gmr-ad' ).each( function () {
				fill_ad( jQuery( this ) );
			} );
		}

		jQuery( window ).load( function( $ ) {

			(function() {
				var GMRAds = GMRAds || {};

				if ( typeof window.innerWidth !== "undefined" ) {
					// Normal Browsers (Including IE9+)
					GMRAds.width = window.innerWidth;
					GMRAds.height = window.innerHeight;
				} else if ( typeof document.documentElement !== "undefined" && typeof document.documentElement.clientWidth !== 0 ) {
					// Old IE Versions
					GMRAds.width = document.documentElement.clientWidth;
					GMRAds.height = document.documentElement.clientHeight;
				} else {
					// Ancient IE Versions
					GMRAds.width = document.getElementsByTagName('body')[0].clientWidth;
					GMRAds.height = document.getElementsByTagName('body')[0].clientHeight;
				}

				window.GMRAds = GMRAds;
			})();

			fill_ads();
			jQuery( document ).on( 'pjax:end gmr_lazy_load_end', fill_ads );
		} );
	</script>
	<?php
}

function load_js() {
	?><script type="text/javascript" src="//ox-d.greatermedia.com/w/1.0/jstag"></script><?php
}

function render_tag( $output_html, $tag_id ) {
	static $random_number;

	// Check if the post is singluar
	if ( is_singular() ) {
		global $post; // Just to be safe
		// We want a slug for all singular post types
		$slug = $post->post_name;

		// Check if the shows class exists so we can use its properties
		if ( class_exists( 'ShowsCPT' ) && defined( "ShowsCPT::SHOW_TAXONOMY" ) ) {
			// Get the show terms
			$terms = get_the_terms( $post->ID, constant( 'ShowsCPT::SHOW_TAXONOMY' ) );
			// Check for errors
			if ( $terms && ! is_wp_error( $terms ) ) {
				// Pluck those slugs out of the terms object
				$shows = wp_list_pluck( $terms, 'slug' );
				// Finally join those for openx in a csv format
				$shows = join( ",", $shows );
			}
		}

		// Check if the podcast class exists so we can use its properties
		if ( class_exists( 'GMP_CPT' ) ) {

			$post_type = get_post_type( $post );

			if ( constant( 'GMP_CPT::PODCAST_POST_TYPE' ) == $post_type ) {
				$podcast = $post->post_name;
			}

			if ( constant( 'GMP_CPT::EPISODE_POST_TYPE' ) == $post_type ) {
				// Find the podcast that this episode is a part of.
				$parent_podcast_id = wp_get_post_parent_id( $post );

				if ( $parent_podcast_id && ! is_wp_error( $parent_podcast_id ) ) {
					// Assign parent's slug to this post
					$parent_podcast = get_post( $parent_podcast_id );
					$podcast = $parent_podcast->post_name;
				}
			}
		}
	}

	$tag_meta = get_ad_tag_meta( $tag_id );

	if ( false === $tag_meta ) {
		return '';
	}

	$variant = trim( ad_variant() );

	$min_width = $max_width = false;

	if ( ! empty( $variant ) ) {
		// We know this exists, because otherwise render_variant would not have set this value
		$variant_meta = $tag_meta['variants'][ $variant ];
		$variant_overrides = ad_variant_overrides();

		// Merge the overrides and meta together, giving overrides priority to the global variant settings
		$variant_meta = wp_parse_args( $variant_overrides, $variant_meta );

		$variant_id = '_' . $variant;

		$min_width = isset( $variant_meta['min_width'] ) ? $variant_meta['min_width'] : false;
		$max_width = isset( $variant_meta['max_width'] ) ? $variant_meta['max_width'] : false;
	} else {
		$variant_id = '';

		$min_width = isset( $tag_meta['min_width'] ) ? $tag_meta['min_width'] : false;
		$max_width = isset( $tag_meta['max_width'] ) ? $tag_meta['max_width'] : false;
	}

	if ( is_null( $random_number ) ) {
		$random_number =  str_pad( rand( 0, PHP_INT_MAX ), 15, rand( 0, 9 ), STR_PAD_LEFT );
	}

	$uniqid = uniqid();

	$category = false;
	if ( is_singular() ) {
		$categories = wp_get_post_categories( get_queried_object_id() );
		if ( ! empty( $categories ) ) {
			$categories = array_filter( array_map( 'get_category', $categories ) );
			$categories = wp_list_pluck( $categories, 'slug' );
			$category = implode( ',', $categories );
		}
	} elseif ( is_category() ) {
		$category = get_queried_object()->slug;
	}

	ob_start();

	?>
	<div
		id="%openx_id%_%tag%<?php echo esc_attr( $variant_id ); ?>_<?php echo esc_attr( $uniqid ); ?>"
		class="gmr-ad"
		data-min-width="<?php echo esc_attr( $min_width ); ?>"
		data-max-width="<?php echo esc_attr( $max_width ); ?>"
		<?php if ( ! empty( $slug ) ) : ?>
			data-slug="<?php echo esc_attr( $slug ); ?>"
		<?php endif; ?>
		<?php if ( ! empty( $shows ) ) : ?>
			data-shows="<?php echo esc_attr( $shows ); ?>"
		<?php endif; ?>
		<?php if ( ! empty( $podcast ) ) : ?>
			data-podcast="<?php echo esc_attr( $podcast ); ?>"
		<?php endif; ?>
		<?php if ( ! empty( $category ) ) : ?>
		data-category="<?php echo esc_attr( $category ); ?>"
		<?php endif; ?>
	    data-openx-id="%openx_id%"
	>
	</div>

	<?php

	$output = ob_get_clean();

	return $output;
}

function render_variant( $tag_id, $variant, $overrides = array() ) {
	$tag_meta = get_ad_tag_meta( $tag_id );

	if ( false === $tag_meta ) {
		return;
	}

	if ( ! isset( $tag_meta['variants'] ) || ! isset( $tag_meta['variants'][ $variant ] ) ) {
		error_log( "Ad Render Error: Trying to render {$tag_id} with undefined variant {$variant}" );
		return;
	}

	// Set our current variant + any overrides we may need to use
	ad_variant( $variant );
	ad_variant_overrides( $overrides );

	// Do the ad slot
	do_action( 'acm_tag', $tag_id );

	// Clear the current variant + overrides
	ad_variant( '' );
	ad_variant_overrides( array() );
}
add_action( 'acm_tag_gmr_variant', __NAMESPACE__ . '\render_variant', 10, 3 );

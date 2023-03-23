<?php

add_filter( 'next_posts_link_attributes', 'ee_load_more_attributes' );
add_filter( 'get_the_archive_title', 'ee_update_archive_title' );

if ( ! function_exists( 'ee_get_date' ) ) :
	function ee_get_date( $timestamp, $gmt = 0 ) {

		$created_offset = $gmt
			? $timestamp + get_option( 'gmt_offset' ) * HOUR_IN_SECONDS
			: $timestamp;

		$month = is_single() || is_page() ? 'F' : 'M';
		$format = $month . ( date( 'Y' ) == date( 'Y', $created_offset ) ? ' jS' : ' jS, Y' );

		return date( $format, $created_offset );
	}
endif;

if ( ! function_exists( 'ee_the_date' ) ) :
	function ee_the_date( $post = null ) {
		$post = get_post( $post );
		if ( is_a( $post, '\WP_Post' ) ) {
			$created = mysql2date( 'G', $post->post_date_gmt );
			echo ee_get_date( $created, 1 );
		}
	}
endif;

if (! function_exists( 'ee_older_than_2019' ) ) :
	function ee_older_than_2019( $post = null ) {
		$post = get_post( $post );
		return strtotime($post->post_date_gmt) < strtotime('1/1/2019');
	}
endif;

if (! function_exists( 'ee_category_exists' ) ) :
	function ee_category_exists( $post = null ) {
		$allowedCategorySetting =  get_option( 'stn_categories', '' );

		if ( ! $allowedCategorySetting ) {
			return true;
		}

		$allowedCategories = explode( ',', $allowedCategorySetting );

		$post = get_post( $post );
		$category_match = false;

		$categories = get_the_category( $post );

		foreach ( $categories as $category ) {
			if  ( in_array( $category->slug, $allowedCategories ) ) {
				$category_match = true;
				break;
			}
		}

		return $category_match;
	}
endif;

if ( ! function_exists( 'ee_the_query_tiles' ) ) :
	function ee_the_query_tiles( $query, $carousel = false ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			if ( $carousel ) {
				echo '<div class="swiper-slide">';
					get_template_part( 'partials/tile', get_post_type() );
				echo '</div>';
			} else {
				get_template_part( 'partials/tile', get_post_type() );
			}
		}

		wp_reset_postdata();
	}
endif;

if ( ! function_exists( 'ee_load_more' ) ) :
	function ee_load_more( $query = null ) {
		if ( $query ) {
			$GLOBALS['wp_query'] = $query;
		}

		next_posts_link( 'Load More' );

		if ( $query ) {
			wp_reset_query();
		}
	}
endif;

if ( ! function_exists( 'ee_load_more_attributes' ) ) :
	function ee_load_more_attributes() {
		return 'class="load-more"';
	}
endif;

if ( ! function_exists( 'ee_is_first_page' ) ) :
	function ee_is_first_page() {
		return get_query_var( 'paged', 1 ) < 2;
	}
endif;

if ( ! function_exists( 'ee_update_archive_title' ) ) :
	function ee_update_archive_title( $title ) {
		if ( is_post_type_archive( 'tribe_events' ) ) {
			$title = 'Events';
		}

		$parts = explode( ':', $title, 2 );
		return array_pop( $parts );
	}
endif;

if ( ! function_exists( 'ee_the_subtitle' ) ) :
	function ee_the_subtitle( $subtitle ) {
		echo '<h2 class="section-head"><span>', esc_html( $subtitle ), '</span></h2>';
	}
endif;

if ( ! function_exists( 'ee_the_have_no_posts' ) ) :
	function ee_the_have_no_posts( $message = 'No items found' ) {
		echo '<h4>', esc_html( $message ), '</h4>';
	}
endif;

if ( ! function_exists( 'ee_the_share_buttons' ) ) :
	function ee_the_share_buttons( $url = null, $title = null ) {
		$url = filter_var( $url, FILTER_VALIDATE_URL )
			? ' data-url="' . esc_attr( $url ) . '"'
			: '';

		$title = ! empty( $title )
			? ' data-title="' . esc_attr( trim( $title ) ) . '"'
			: '';

		echo '<div class="share-buttons"', $url, $title, '></div>';
	}
endif;

if ( ! function_exists( 'ee_the_sponsored_by_div' ) ) :
	function ee_the_sponsored_by_div( $the_id = null, $add_padding = false ) {
		$sponsored_by = ee_get_sponsored_by($the_id);
	    if ( $sponsored_by !== '' ) {
			$sponsor_url = ee_get_sponsor_url($the_id);

			$div_start = $add_padding
				? '<div class="sponsor-meta pad-top-75rem">'
				: '<div class="sponsor-meta">';

			$sponsored_by = $sponsor_url === ''
				? $div_start . $sponsored_by . '</div>'
				: $div_start . '<a class="sponsor-meta" href="' . $sponsor_url . '" rel="sponsored" target="_blank" >' . $sponsored_by . '</a></div>';
		}

		echo $sponsored_by;
	}
endif;

if ( ! function_exists( 'ee_the_sponsored_by_thumbnail_overlay' ) ) :
	function ee_the_sponsored_by_thumbnail_overlay( $the_id = null) {
		$sponsored_by = ee_get_sponsored_by($the_id);
		if ( $sponsored_by !== '' ) {
			$sponsored_by = '<div class="post-sponsor-overlay">Sponsored</div>';
		}
		echo $sponsored_by;
	}
endif;

if ( ! function_exists( 'ee_filter_primary_category' ) ) :
	function ee_filter_primary_category( $categories, $post_id ) {
		$post = get_post( $post_id );
		$cat_id = get_post_meta( $post->ID, '_yoast_wpseo_primary_category', true );
		if ( $cat_id > 0 ) {
			foreach ( $categories as $category ) {
				if ( $category->term_id == $cat_id ) {
					return array( $category );
				}
			}
		}

		return array( current( $categories ) );
	}
endif;

if ( ! function_exists( 'append_current_device_to_cache_tag' ) ) :
	function append_current_device_to_cache_tag(&$headerCacheTag)
	{
		if (ee_is_common_mobile()) {
			$headerCacheTag[] = 'mobile';
		} else {
			$headerCacheTag[] = 'desktop';
		}
		$headerCacheTag[] = 'content';
	}
endif;


if ( ! function_exists( 'ee_the_permalink' ) ) :
	function ee_the_permalink() {
		$post = get_post();

		if ( ! empty( get_post_meta( $post->ID, 'fp_syndicated_post', true ) ) ) {
			the_permalink( $post );
			return;
		}

		if ( ! empty( $post->link ) ) {
			$url = $post->link;
			$parts = parse_url( $url );
			if ( parse_url( home_url(), PHP_URL_HOST ) != $parts['host'] && ee_is_network_domain( $url ) ) {
				$url = home_url( '/' . $parts['host'] . $parts['path'] );
			}

			echo filter_var( $url, FILTER_VALIDATE_URL );
		} else {
			the_permalink( $post );
		}
	}
endif;

if ( ! function_exists( 'ee_is_current_domain' ) ) :
	function ee_is_current_domain( $url ) {
		return parse_url( $url, PHP_URL_HOST ) == parse_url( home_url(), PHP_URL_HOST );
	}
endif;

if ( ! function_exists( 'ee_is_network_domain' ) ) :
	function ee_is_network_domain( $url ) {
		static $domains = null;
		if ( is_null( $domains ) ) {
			$domains = wp_list_pluck( get_sites(), 'domain' );
		}

		return in_array( parse_url( $url, PHP_URL_HOST ), $domains );
	}
endif;

if ( ! function_exists( 'ee_get_related_articles' ) ) :
	function ee_get_related_articles() {
		if ( ! function_exists( 'ep_find_related' ) ) {
			return array();
		}

		$post_id = get_queried_object_id();
		$key = 'ee-related-' . $post_id;
		$related_articles = wp_cache_get( $key );

		if ( $related_articles === false ) {
			$remove_filter = false;

			if ( function_exists( 'ep_related_posts_formatted_args' ) ) {
				if ( ! has_filter( 'ep_formatted_args', 'ep_related_posts_formatted_args' ) ) {
					$remove_filter = true;
					add_filter( 'ep_formatted_args', 'ep_related_posts_formatted_args', 10, 2 );
				}
			}

			$related_articles = ep_find_related( $post_id, 5 );

			if ( function_exists( 'ep_related_posts_formatted_args' ) ) {
				if ( $remove_filter ) {
					remove_filter( 'ep_formatted_args', 'ep_related_posts_formatted_args', 10, 2 );
				}
			}

			if ( ! empty( $related_articles ) ) {
				wp_cache_set( $key, $related_articles, '', 15 * MINUTE_IN_SECONDS );
			}
		}

		return $related_articles;
	}
endif;

if ( ! function_exists( 'ee_add_to_favorites' ) ) :
	function ee_add_to_favorites( $keyword ) {
		echo '<div class="add-to-favorites" data-keyword="', esc_attr( $keyword ), '"></div>';
	}
endif;

if ( ! function_exists( 'ee_get_sponsored_by' ) ) :
	function ee_get_sponsored_by( $post_id ) {
		$post = get_post( $post_id );
		$sponsored_by_label = get_post_meta( $post->ID, 'sponsored_by_label', true );
		if (   strlen($sponsored_by_label) > 0
			&& substr($sponsored_by_label, strlen($sponsored_by_label) - 1) != ' ' ) {
			$sponsored_by_label = $sponsored_by_label.' ';
		}
		$sponsor_name = get_post_meta( $post->ID, 'sponsor_name', true );
		if ($sponsor_name !== '') {
			$sponsor_name = $sponsored_by_label . $sponsor_name;
		}
		return esc_attr( trim($sponsor_name));
	}
endif;

if ( ! function_exists( 'ee_get_sponsor_url' ) ) :
	function ee_get_sponsor_url( $post_id ) {
		$post = get_post( $post_id );
		$sponsored_url = get_post_meta( $post->ID, 'sponsor_url', true );
		return esc_attr( trim($sponsored_url));
	}
endif;

if ( ! function_exists( 'ee_get_primary_terms' ) ) :
	function ee_get_primary_terms($post_id, $term='category', $return_all_categories=false){
		$return = array();
		$categories_list = get_the_terms($post_id, $term);

		if (class_exists('WPSEO_Primary_Term')){
			// Show Primary category by Yoast if it is enabled & set
			$wpseo_primary_term = new WPSEO_Primary_Term( $term, $post_id );
			$primary_term = get_term($wpseo_primary_term->get_primary_term());

			if (!is_wp_error($primary_term)){
				$return['primary'] = $primary_term;
			}
		}

		if (empty($return['primary']) && !empty($categories_list)){
			$return['primary'] = $categories_list[0];  //get the first category
		}

		if ($return_all_categories){
			$return['all'] = array();

			if (!empty($categories_list)){
				foreach($categories_list as &$category){
					$return['all'][] = $category->name;
				}
			}
		}

		return $return;
	}	
endif;

if ( ! function_exists( 'ee_mparticle_get_author_data' ) ) :
	function ee_mparticle_get_author_data( $post ){
		$response = array(
			'author' => '',
			'primary_author' => '',
			'secondary_author' => ''
		);
		
		$response['author'] = $post->post_author ? get_the_author_meta( 'login', $post->post_author ) : '';
		$primary_author = get_field( 'primary_author_cpt', $post );
		$response['primary_author'] = $primary_author ? get_the_author_meta( 'login', $primary_author ) : '';
		$secondary_author = get_field( 'secondary_author_cpt', $post );
		$response['secondary_author'] = $secondary_author ? get_the_author_meta( 'login', $secondary_author ) : '';

		return (object) $response;
	}
endif;

if ( ! function_exists( 'ee_mparticle_prepare_pageview_data' ) ) :
	function ee_mparticle_prepare_pageview_data( $post ){
		$post_categories = ee_get_primary_terms($post->ID, 'category', true);
		$post_shows = ee_get_primary_terms($post->ID, '_shows', true);
		$post_tags = ee_get_primary_terms($post->ID, 'post_tag', true);
		
		$mParticle_category = $post_categories['primary'];
		$mParticle_categories = !empty($post_categories['all']) ? wp_json_encode($post_categories['all']) : '';
		$mParticle_show = $post_shows['primary'];
		$mParticle_tags = !empty($post_tags['all']) ? wp_json_encode($post_tags['all']) : '';
		
		$mParticle_post_id = $post->ID ? $post->ID : '';
		
		$author_details = ee_mparticle_get_author_data( $post );
		$mParticle_author = $author_details->author ? $author_details->author : '';
		$mParticle_primary_author = $author_details->primary_author ? $author_details->primary_author : '';
		$mParticle_secondary_author = $author_details->secondary_author ? $author_details->secondary_author : '';
		
		$mParticle_word_count = $post->post_content ? str_word_count( strip_tags( $post->post_content ) ) : null;

		$mParticle_select_embed_post_id = get_post_meta( $post->ID, 'select_embed_post_id', true );
		if( !empty($mParticle_select_embed_post_id) ) {
			$mParticle_select_embed_post = get_post($mParticle_select_embed_post_id);
			$mParticle_select_embed_title = $mParticle_select_embed_post->post_title;
			$mParticle_select_embed_type = $mParticle_select_embed_post->post_type;
			$mParticle_select_embed_path = trailingslashit( get_permalink( $mParticle_select_embed_post_id ) );
			
			$select_embed_author_details = ee_mparticle_get_author_data( $mParticle_select_embed_post );
			$mParticle_select_embed_author = $select_embed_author_details->author ? $select_embed_author_details->author : '';
			$mParticle_select_embed_primary_author = $select_embed_author_details->primary_author ? $select_embed_author_details->primary_author : '';
			$mParticle_select_embed_secondary_author = $select_embed_author_details->secondary_author ? $select_embed_author_details->secondary_author : '';
		}

		$mParticleContentType = get_post_type( $post );
		if (empty($mParticleContentType)) {
			$mParticleContentType = 'null';
		} else {
			if (strpos($mParticleContentType, 'listicle') !== false) {
				$mParticleContentType = 'listicle';
			} else if (strpos($mParticleContentType, 'gallery') !== false) {
				$mParticleContentType = 'gallery';
			} else if (strpos($mParticleContentType, 'affiliate_marketing') !== false) {
				$mParticleContentType = 'must_have';
			} else if (strpos($mParticleContentType, 'homepage') !== false) {
				$mParticleContentType = 'home';
			} else if (strpos($mParticleContentType, 'event') !== false) {
				$mParticleContentType = 'event';
			} else if (strpos($mParticleContentType, 'episode') !== false) {
				$mParticleContentType = 'episode';
			} else if (strpos($mParticleContentType, 'contest') !== false) {
				$mParticleContentType = 'contest';
			} else if (strpos($mParticleContentType, 'podcast') !== false) {
				$mParticleContentType = 'podcast';
			} else if (strpos($mParticleContentType, 'show') !== false) {
				$mParticleContentType = 'show';
			} else {
				$mParticleContentType = 'article';
			}
		}

		return [
			'mParticleContentType'						=> $mParticleContentType,
			'mParticle_category'						=> $mParticle_category ?: '',
			'mParticle_categories'						=> $mParticle_categories ?: '',
			'mParticle_show'							=> $mParticle_show ?: '',
			'mParticle_tags'							=> $mParticle_tags ?: '',
			'mParticle_select_embed_title'				=> $mParticle_select_embed_title ?: '',
			'mParticle_select_embed_type'				=> $mParticle_select_embed_type ?: '',
			'mParticle_select_embed_path' 				=> $mParticle_select_embed_path ?: '',
			'mParticle_select_embed_post_id' 			=> $mParticle_select_embed_post_id ?: '',
			'mParticle_select_embed_author' 			=> $mParticle_select_embed_author ?: '',
			'mParticle_select_embed_primary_author' 	=> $mParticle_select_embed_primary_author ?: '',
			'mParticle_select_embed_secondary_author' 	=> $mParticle_select_embed_secondary_author ?: '',
			'mParticle_post_id' 						=> $mParticle_post_id ?: '',
			'mParticle_author' 							=> $mParticle_author ?: '',
			'mParticle_primary_author' 					=> $mParticle_primary_author ?: '',
			'mParticle_secondary_author' 				=> $mParticle_secondary_author ?: '',
			'mParticle_word_count' 						=> $mParticle_word_count ?:  null
		];
	}	
endif;
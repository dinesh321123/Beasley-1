<?php

get_header();

if ( ee_is_first_page() ):
	get_template_part( 'partials/archive/title' );
	get_template_part( 'partials/archive/meta' );
endif;

if ( have_posts() ) :

	echo '<div class="archive-tiles content-wrap ', ! is_post_type_archive( 'contest' ) && ! is_post_type_archive( 'tribe_events' ) ? '-grid -large' : '-list', '">';
		while ( have_posts() ) :
			the_post();
			get_template_part( 'partials/tile', get_post_type() );
		endwhile;
	echo '</div>';
	echo '<div class="content-wrap">';
		// ee_load_more();

		$count_posts = wp_count_posts( 'tribe_events' );
		$posts_per_page = tribe_get_option( 'postsPerPage');
		
		$total_posts = $count_posts->publish;
		$max_pages = ceil( $total_posts / $posts_per_page );
        
		$page_number = (get_query_var('paged')) ? max(get_query_var('paged'), 1) : 1;
		$max_page_number = $page_number+1;
		$dynamic_url = get_site_url() . '/events/page/' . $max_page_number . '/';
        
		if( $max_pages >= $max_page_number ){
        	?><a href="<?php echo $dynamic_url;?>" class="load-more">Load More</a><?php
		}

	echo '</div>';
else :
	echo '<div class="content-wrap">';
		ee_the_have_no_posts();
	echo '</div>';
endif;

get_footer();

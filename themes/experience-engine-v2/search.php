<?php

get_header();

global $wp_query;
$search_query = get_search_query();

if ( ee_is_first_page() ) :
	get_template_part( 'partials/search/header' );

	$random = substr(md5(mt_rand()), 0, 10);
	$result_count = $wp_query->found_posts;
	if (! $result_count) {
		$result_count = 0;
	}

	$mparticle_implementation = sprintf(
		'<script class="mparticle_implementation">
                    console.log(\'Firing MParticle Search Event - \', \'%s\');
					window.beasleyanalytics.setAnalyticsForMParticle(\'search_term\', \'%s\');
					window.beasleyanalytics.setAnalyticsForMParticle(\'search_num_results\', %d);

					window.beasleyanalytics.sendMParticleEvent(
						BeasleyAnalyticsMParticleProvider.mparticleEventNames.searchedFor,
						\'%s\',
					);
				</script>',
		$random,
		$search_query,
		$result_count,
		$random,
	);
	echo $mparticle_implementation;

endif;

if ( have_posts() ) :
	$search_results = $wp_query->posts;
	$page_num       = intval( get_query_var( 'paged' ) );

	if ( empty( $page_num ) ) {
		$page_num = 1;
	}

	echo '<div class="content-wrap">';
		if ( $page_num === 1 ) {
			$keyword = intval( get_post_with_keyword( $search_query ) );

			if ( ! empty( $keyword ) ) {
				echo "<h4>Here's what we found for '" . esc_html( $search_query ) . "'</h4>";
				echo '<div class="archive-tiles archive-keyword-tiles -grid -small">';

				global $post;
				$post = get_post( $keyword );
				setup_postdata( $post );
				get_template_part( 'partials/tile', get_post_type() );
				wp_reset_postdata();

				echo '</div>';
			}
		}


		echo '<div class="archive-tiles -grid -small">';
			while ( have_posts() ) :
				the_post();
				get_template_part( 'partials/tile', get_post_type() );
			endwhile;
		echo '</div>';

		ee_load_more();
	echo '</div>';

else :
	ee_the_have_no_posts();
endif;

get_footer();

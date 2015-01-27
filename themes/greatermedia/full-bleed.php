<?php
/**
 * Template Name: Full Bleed
 */
get_header(); ?>

	<main class="main" role="main">

		<?php the_post(); ?>

		<div class="container">

			<?php

			$landing_page_content = locate_template(array(
				'landing-page-content/' . get_post_field( 'post_name', null, 'raw' ) . '.php',
				'landing-page-content/' . get_post_field( 'post_name', null, 'raw' ) . '.html'
			) );

			if ( $landing_page_content ) {
				include( $landing_page_content );
			} else {
				the_content();
			}

			?>

		</div>
		
	</main>

<?php get_footer();
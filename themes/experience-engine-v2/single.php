<?php
	get_header();
	ee_switch_to_article_blog();
	the_post();
	?>
	<div id="post-<?php the_ID(); ?>" <?php post_class( 'single' ); ?>>
		<div class="container">
			<div class="content-wrap">
				<div class="section-head-container">
					<h2 class="section-head">
						<span class="bigger">ROCK NEWS</span>
					</h2>
				</div>
			</div>
			
			<div class="entry-content content-wrap">
				<div class="description">

					<h1> <?php the_title(); ?> </h1>

					<?php if( is_singular( 'post' ) ) : ?>
						<div class="post-meta">
							<?php get_template_part( 'partials/content/articles/meta' ); ?>
						</div>
					<?php endif; ?>

					<?php if ( bbgi_featured_image_layout_is( null, 'inline' ) ) : ?>
						<?php get_template_part( 'partials/featured-media' ); ?>
					<?php endif; ?>

					<?php ee_the_content_with_ads(); ?>

					<?php if ( is_singular( 'post' ) ) : ?>
						<div class="profile">
							<?php echo get_the_author_meta( 'description' ); ?>
						</div>
					<?php endif; ?>

					<?php get_template_part( 'partials/footer/common', 'description' ); ?>

					<?php if( is_singular( 'post' ) ) : ?>
						<div class="post-meta">
							<?php get_template_part( 'partials/content/articles/meta' ); ?>
						</div>
					<?php endif; ?>

					<?php get_template_part( 'partials/footer/newsletterSignupForm', 'nsf' ); ?>
				
				</div>
				<aside class="ad -sticky">
							
					<?php get_template_part( 'partials/related-articles' );	?>
						
					<?php get_template_part( 'partials/content/articles/ads' ); ?>

					<?php get_template_part( 'partials/related-articles' );	?>

				</aside>
			</div>	
			<div class="content-wrap">
				<div class="section-head-container">
					<h2 class="section-head">
						<span class="bigger">MORE ROCK NEWS</span>
					</h2>
				</div>
			</div>
			<div class="entry-content content-wrap">
				<div class="description">
					
					<?php 
						// Get the current post ID
						$current_post_id = get_the_ID();

						// Get the categories of the current post
						$categories = get_the_category($current_post_id);
						$category_ids = array();

						foreach ($categories as $category) {
							$category_ids[] = $category->term_id;
						}

						// Construct the arguments for WP_Query
						$args = array(
							'post__not_in'        => array($current_post_id), // Exclude the current post
							'category__in'        => $category_ids, // Retrieve posts from the same categories
						);

						// Perform the query
						$related_query = new WP_Query($args);

						if ($related_query->have_posts()) {
						
							echo '<div class="archive-tiles content-wrap ', ! is_post_type_archive( 'contest' ) && ! is_post_type_archive( 'tribe_events' ) ? '-grid -large' : '-list', '">';
								while ( $related_query->have_posts() ) :
									$related_query->the_post();
									get_template_part( 'partials/tile', $related_query->get_post_type() );
								endwhile;
							echo '</div>';

							echo '<div class="content-wrap">';
								ee_load_more();
							echo '</div>';

						}

					?>



				</div>
				<?php get_template_part( 'partials/content/articles/ads' ); ?>
			</div>
		</div>
</div><?php

restore_current_blog();
get_footer();

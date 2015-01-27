<?php get_header(); ?>

	<main class="main" role="main">

		<div class="container">

			<?php the_post(); ?>

			<?php get_template_part( 'show-header' ); ?>

			<section class="content">
				<div class="podcasts">
					<h2>Podcasts</h2>

					<?php 

					$episodes = 0;
					$podcast_query = \GreaterMedia\Shows\get_show_podcast_query();
					if ( $podcast_query->have_posts() ) :
						$pattern = get_shortcode_regex();
						
						while ( $podcast_query->have_posts() ) :
							$podcast_query->the_post();

							if ( preg_match_all( '/'. $pattern .'/s', get_the_content(), $matches )
								&& array_key_exists( 2, $matches )
								&& in_array( 'audio', $matches[2] ) ) :

								$episodes++;

								?><article id="post-<?php the_ID(); ?>" <?php post_class( 'cf podcast' ); ?> role="article" itemscope itemtype="http://schema.org/OnDemandEvent"><?php
									echo do_shortcode( $matches[0][0] );
								?></article><?php
							endif;
						endwhile;
						
						wp_reset_query();
					endif;
					
					if ( ! $episodes ) :

						?><article id="post-not-found" class="hentry cf">
							<header class="article-header">
								<h1><?php _e( 'Oops, No Episodes Here!', 'greatermedia' ); ?></h1>
							</header>
						</article><?php

					else :
						greatermedia_load_more_button( array(
							'page_link_template' => trailingslashit( get_permalink() ) . 'podcasts/page/%d/',
							'partial_slug'       => 'partials/loop-gmr_podcast',
							'auto_load'          => false,
							'query'              => $podcast_query,
						) );
					endif;

					?>
				</div>
			</section>

		</div>

	</main>

<?php get_footer();
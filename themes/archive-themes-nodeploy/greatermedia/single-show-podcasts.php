<?php get_header(); ?>

	<div class="container">

		<?php the_post(); ?>

		<?php get_template_part( 'show-header' ); ?>

		<section class="content">
			<div class="podcasts">
				<h2>Podcasts</h2><?php

				$episodes = 0;
				$podcast_query = \GreaterMedia\Shows\get_show_podcast_query();
				if ( $podcast_query->have_posts() ) :
					$pattern = get_shortcode_regex();
					while ( $podcast_query->have_posts() ) :
						$podcast_query->the_post();

						$episode = \GMP_Player::get_podcast_episode();
						if ( ! empty( $episode ) ) :
							$episodes++;
							?><article id="post-<?php the_ID(); ?>" <?php post_class( 'cf episode' ); ?> role="article" itemscope itemtype="http://schema.org/OnDemandEvent">
								<?php echo $episode; ?>
							</article><?php
						endif;
					endwhile;

					wp_reset_query();
				endif;

				if ( ! $episodes ) :
					?><article id="post-not-found" class="hentry cf">
						<header class="article-header">
							<h1>Oops, No Episodes Here!</h1>
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
			?></div>
		</section>

		<?php get_sidebar(); ?>

	</div>

<?php get_footer();

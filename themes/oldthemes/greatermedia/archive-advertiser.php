<?php
/**
 * Archive template file
 *
 * @package Greater Media
 * @since   0.1.0
 *
 * @todo this template file still needs to be layed out according to the design
 */

get_header(); ?>

<div class="container">
	<section class="content">
		<h2 class="sponsors__heading">Advertisers</h2>

		<div class="sponsors"><?php

			$advertiser_args = array(
				'post_type'         => GMR_ADVERTISER_CPT,
				'orderby'           => 'menu_order date',
				'order'             => 'ASC',
				'posts_per_page'    => 30
			);

			$advertiser_query = new WP_Query( $advertiser_args );
			$i = 1;

			?><div class="sponsors__row"><?php
				if ( $advertiser_query->have_posts() ) :
					while ( $advertiser_query->have_posts() ) :
						$advertiser_query->the_post();

						get_template_part( 'partials/loop', 'advertiser' );

						if ( $i % 2 == 0 ) :
							?></div><div class="sponsors__row"><?php
						endif;

						$i++;
					endwhile;
				else :
					?><article id="post-not-found" class="hentry cf">
						<header class="article-header">
							<h1>Oops, Post Not Found!</h1>
						</header>

						<section class="entry-content">
							<p>Uh Oh. Something is missing. Try double checking things.</p>
						</section>
					</article><?php
				endif;
			?></div>
		</div>
	</section>

	<?php get_sidebar(); ?>
</div>

<?php get_footer(); ?>
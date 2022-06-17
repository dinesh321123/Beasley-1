<div class="container"><?php
	if ( have_posts() ) :
		while ( have_posts() ) :
			the_post();

			get_template_part( 'partials/show-mini-nav' );

			if ( has_post_thumbnail() ) :
				?><div class="entry__thumbnail">
					<img src="<?php echo esc_url( bbgi_get_image_url( get_post_thumbnail_id(), 930, 576 ) ) ?>">
					<?php bbgi_the_image_attribution(); ?>
				</div><?php
			endif;

			?><section class="content">
				<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
					<header class="entry__header">
						<time class="entry__date" datetime="<?php echo get_the_time(); ?>"><?php the_date('F j, Y'); ?></time>
						<h2 class="entry__title" itemprop="headline"><?php the_title(); ?></h2>
						<?php get_template_part( 'partials/social-share' ); ?>
					</header>

					<section class="entry-content" itemprop="articleBody">
						<?php the_content(); ?>
					</section>

					<!-- begin album galleries -->
					<?php

					// Secondary content needs to go through a filter to allow the
					// restriction plugins to do their work
					ob_start();

					?><section class="inline__gallery__archive">
						<div class="gallery__grid gallery__grid-album"><?php
							$gallery_content_types = array(
								GreaterMediaGalleryCPT::GALLERY_POST_TYPE,
								'post'
							);

							$child_galleries = array(
								'ignore_sticky_posts' => true,
								'post_type'           => $gallery_content_types,
								'post_parent'         => $post->ID,
								'posts_per_page'      => 16,
								'orderby'             => 'post_date',
								'order'               => 'DESC',
								'paged'               => get_query_var('paged')
							);

							$gallery_query = new WP_Query( $child_galleries );

							if ( $gallery_query->have_posts() ) :
								while ( $gallery_query->have_posts() ) :
									$gallery_query->the_post();
									get_template_part( 'partials/gallery-grid' );
								endwhile;

								wp_reset_query();
							endif;
						?></div>
					</section>

					<?php echo apply_filters( 'the_secondary_content', ob_get_clean() ); ?>
					<!-- end galleries -->
				</article>
			</section><?php
		endwhile;

		wp_reset_query();
	endif;
?></div>

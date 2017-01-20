<div class="container">

	<?php if (have_posts()) : while ( have_posts() ) : the_post(); ?>

		<?php get_template_part( 'partials/show-mini-nav' ); ?>

		<?php if ( has_post_thumbnail() ) { ?>

			<div class="entry__thumbnail">

				<?php the_post_thumbnail( 'gmr-album-thumbnail', array( 'class' => 'single__featured-img' ) ); ?>

				<?php

					$image_attr = image_attribution();

					if ( ! empty( $image_attr ) ) {
						echo $image_attr;
					}

				?>

			</div>

		<?php } ?>

		<section class="content">

			<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">



				<div class="ad__inline--right">
					<?php do_action( 'acm_tag', 'mrec-body' ); ?>
				</div>

				<header class="entry__header">

					<time class="entry__date" datetime="<?php echo get_the_time(); ?>"><?php the_date('F j, Y'); ?></time>
					<h2 class="entry__title" itemprop="headline"><?php the_title(); ?></h2>
					<?php get_template_part( 'partials/social-share' ); ?>

				</header>

				<section class="entry-content" itemprop="articleBody">

					<?php the_content(); ?>

				</section>

			</article>

		</section>

		<aside class="sidebar">
			<?php dynamic_sidebar( 'liveplayer_sidebar' ); ?>
		</aside>

	<?php
		endwhile;
		endif;
		wp_reset_query();
	?>

	<?php

	// Secondary content needs to go through a filter to allow the
	// restriction plugins to do their work
	ob_start();
	?>
	<section class="gallery__archive">

		<div class="gallery__grid">

			<?php

				$gallery_content_types = array(
					GreaterMediaGalleryCPT::GALLERY_POST_TYPE,
					'post'
				);

				$child_galleries = array(
					'post_type'         => $gallery_content_types,
					'post_parent'       => $post->ID,
					'posts_per_page'    => 16,
					'orderby'           => 'post_date',
					'order'             => 'DESC',
					'paged'             => get_query_var('paged')
				);

				$gallery_query = new WP_Query( $child_galleries );

				while ($gallery_query->have_posts()) : $gallery_query->the_post();

					get_template_part( 'partials/gallery-grid' );

				endwhile;

				wp_reset_query();
			?>

		</div>

	</section>

	<?php
	$secondary_content = ob_get_clean();
	echo apply_filters( 'the_secondary_content', $secondary_content );
	?>

</div>

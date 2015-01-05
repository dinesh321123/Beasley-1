<div class="container">

	<?php if (have_posts()) : while ( have_posts() ) : the_post(); ?>

		<section class="content">

			<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

				<?php if ( has_post_thumbnail() ) { ?>

					<div class="entry__thumbnail">

						<?php the_post_thumbnail( 'gmr-album-thumbnail', array( 'class' => 'single__featured-img' ) ); ?>

					</div>

				<?php } ?>

				<div class="ad__inline--right">
					<?php do_action( 'acm_tag', 'mrec-body' ); ?>
				</div>

				<header class="entry__header">

					<time class="entry__date" datetime="<?php echo get_the_time(); ?>"><?php the_date('F j'); ?></time>
					<h2 class="entry__title" itemprop="headline"><?php the_title(); ?></h2>
					<a class="icon-facebook social-share-link" href="http://www.facebook.com/sharer/sharer.php?u=[URL]&title=[TITLE]"></a>
					<a class="icon-twitter social-share-link" href="http://twitter.com/home?status=[TITLE]+[URL]"></a>
					<a class="icon-google-plus social-share-link" href="https://plus.google.com/share?url=[URL]"></a>

				</header>

				<section class="entry-content" itemprop="articleBody">

					<?php the_content(); ?>

				</section>

			</article>

		</section>

	<?php
		endwhile;
		endif;
		wp_reset_query();
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
				greatermedia_gallery_album_nav();
				wp_reset_query();
			?>

		</div>

	</section>

</div>
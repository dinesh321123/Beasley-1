<h2 class="page__title" itemprop="headline"><?php _e( 'Photos', 'greatermedia' ); ?></h2>
<?php

if ( 'show' == get_post_type() ) {
	$gallery_content_types = array(
		'gmr_gallery',
		'gmr_album'
	);
	global $post;
	$post_id = $post->ID;

	$terms = wp_get_object_terms($post_id,'_shows');
	$post_ids = get_objects_in_term($terms[0]->term_id,'_shows');
} else {
	$gallery_content_types = array(
		'gmr_gallery',
		'gmr_album'
	);
}

$excluded_primary = false;
$excluded_secondary = false;

if ( ! get_query_var( 'paged' ) || get_query_var( 'paged' ) < 2 ) { ?>

	<div class="gallery__featured">

		<?php

		if ( 'show' == get_post_type() ) {

			$primary_args = array(
				'post_type'         => $gallery_content_types, // The assumes the post types match
				'post__in'          => $post_ids,
				'taxonomy'          => '_shows',
				'term'              => $terms[0]->slug,
				'posts_per_page'    => 1,
				'orderby'           => 'date',
				'order'             => 'DESC',
				'post_parent'       => '0',
			);

		} else {

			$primary_args = array(
				'post_type'         => $gallery_content_types,
				'posts_per_page'    => 1,
				'orderby'           => 'date',
				'order'             => 'DESC',
				'post_parent'       => '0',
			);

		}

		$primary_query = new WP_Query( $primary_args );

		?>

		<div class="gallery__featured--primary">

			<?php if ($primary_query->have_posts() ) : while ( $primary_query->have_posts() ): $primary_query->the_post();

				get_template_part( 'partials/gallery-featured', 'primary' );

				$excluded_primary[] = get_the_ID();

			endwhile;

				wp_reset_postdata();

			else :

			endif;

			?>

		</div>

		<?php

		if ( 'show' == get_post_type() ) {

			$secondary_args = array(
				'post_type'         => $gallery_content_types, // The assumes the post types match
				'post__in'          => $post_ids,
				'taxonomy'          => '_shows',
				'term'              => $terms[0]->slug,
				'posts_per_page'    => 2,
				'orderby'           => 'date',
				'order'             => 'DESC',
				'post_parent'       => '0',
				'offset'            => 1
			);

		} else {

			$secondary_args = array(
				'post_type'         => $gallery_content_types,
				'post__not_in'      => $excluded_primary,
				'posts_per_page'    => 2,
				'orderby'           => 'date',
				'order'             => 'DESC',
				'post_parent'       => '0',
			);

		}

		$secondary_query = new WP_Query( $secondary_args );

		?>

		<div class="gallery__featured--secondary">

			<?php if ($secondary_query->have_posts() ) : while ( $secondary_query->have_posts() ): $secondary_query->the_post();

				get_template_part( 'partials/gallery-featured', 'secondary' );

				$excluded_secondary[] = get_the_ID();

			endwhile;

				wp_reset_postdata();

			else :

			endif;

			?>

		</div>

	</div>

<?php } ?>

<div class="gallery__grid">

	<?php

	$excluded = false;

	$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

	if ( 'show' == get_post_type() ) {

		$grid_args = array(
			'post_type'         => $gallery_content_types, // The assumes the post types match
			'post__in'          => $post_ids,
			'taxonomy'          => '_shows',
			'term'              => $terms[0]->slug,
			'posts_per_page'    => 16,
			'paged'             => $paged,
			'orderby'           => 'date',
			'order'             => 'DESC',
			'post_parent'       => '0',
			'offset'            => 3
		);

	} else {

		if ( ! get_query_var( 'paged' ) || get_query_var( 'paged' ) < 2 ) {
			$excluded = array_merge(
				$excluded_primary,
				$excluded_secondary
			);
		}

		$grid_args = array(
			'post_type'         => $gallery_content_types,
			'posts_per_page'    => 16,
			'post__not_in'      => $excluded,
			'paged'             => $paged,
			'orderby'           => 'date',
			'order'             => 'DESC',
			'post_parent'       => '0'
		);

	}

	$grid_query = new WP_Query( $grid_args );

	if ( $grid_query->have_posts() ) : while ( $grid_query->have_posts() ) : $grid_query->the_post();

		get_template_part( 'partials/gallery-grid' );

	endwhile;
		
		wp_reset_postdata();

	else : ?>

		<article id="post-not-found" class="hentry cf">
			<header class="article-header">
				<?php if ( 'show' == get_post_type() ) { ?>
					<h2 class="entry__title"><?php the_title(); ?><?php _e( ' does not have galleries...yet!', 'greatermedia' ); ?></h2>
				<?php } else { ?>
					<h2 class="entry__title"><?php _e( 'There are currently no galleries', 'greatermedia' ); ?></h2>
				<?php } ?>
			</header>
			<?php if ( 'show' == get_post_type() ) { ?>
				<section class="entry__content">
					<a href="<?php the_permalink(); ?>" class="gallery__back--btn">Back</a>
				</section>
			<?php } ?>
		</article>

	<?php endif; ?>

</div>
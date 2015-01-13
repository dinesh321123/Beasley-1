<h2 class="page__title" itemprop="headline"><?php _e( 'Photos', 'greatermedia' ); ?></h2>
<?php
/*
 * Will have post IDs of posts we've rendered on the page, to avoid duplication. There is a rare case that can pop
 * up that seems to be from using have_posts() multiple times, where rewind_posts() gets called right as we start
 * the final query. Tracking IDs allows us to be sure we don't accidentally render the bottom section as a duplicate
 * of the top section for these rare cases.
 */
$rendered_posts = array();

/*
 * Posts per page is 16, for the main section.
 *
 * On the first page, we get a large primary and two smaller secondary items, so posts per page goes up to 19.
 * On pages after page 1, we get only 16 items, but the overall offset is 3, to account for the 3 extra items on page 1.
 *
 * Here, we calculate those values!
 */
if ( 'show' == get_post_type() ) {
	// Show section endpoints use a custom pagination param, since default paged would cause issues with primary show navigation
	$page = get_query_var( 'show_section_page' ) ? get_query_var( 'show_section_page' ) : 1;
} else {
	$page = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
}

if ( $page > 1 ) {
	$per_page = 16;
	$offset = 3;
} else {
	$per_page = 19;
	$offset = 0;
}

$query_args = array(
	'post_type' => array( 'gmr_gallery', 'gmr_album' ),
	'orderby' => 'date',
	'order' => 'DESC',
	'post_parent' => '0',
	'posts_per_page' => $per_page,
	'offset' => $offset,
);

if ( 'show' == get_post_type() ) {
	$term = \TDS\get_related_term( get_the_ID() );

	$query_args['tax_query'] = array(
		array(
			'taxonomy' => '_shows',
			'field' => 'slug',
			'terms' => $term->slug,
		)
	);
}

$query = new WP_Query( $query_args );

if ( $query->have_posts() ) :

	if ( $page < 2 ) { ?>

		<div class="gallery__featured">

			<div class="gallery__featured--primary">

				<?php if ($query->have_posts() ) : $query->the_post();

					$rendered_posts[ get_the_ID() ] = get_the_ID();

					get_template_part( 'partials/gallery-featured', 'primary' );

				endif;

				?>

			</div>

			<div class="gallery__featured--secondary">

				<?php
				$secondary_count = 0;
				if ( $query->have_posts() ) :

					while ( $query->have_posts() && $secondary_count < 2 ): $query->the_post();

						if ( in_array( get_the_ID(), $rendered_posts ) ) {
							continue;
						}

						$rendered_posts[ get_the_ID() ] = get_the_ID();

						$secondary_count++;

						get_template_part( 'partials/gallery-featured', 'secondary' );

					endwhile;

				endif;

				?>

			</div>

		</div>

	<?php } ?>

	<div class="gallery__grid">

		<?php

		if ( $query->have_posts() ) :

			while ( $query->have_posts() ) : $query->the_post();

				if ( in_array( get_the_ID(), $rendered_posts ) ) {
					continue;
				}

				$rendered_posts[ get_the_ID() ] = get_the_ID();

				get_template_part( 'partials/gallery-grid' );

			endwhile;

		endif; ?>

	</div>

	<?php wp_reset_postdata(); ?>

<?php else : ?>

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

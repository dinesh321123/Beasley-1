<h2 class="page__title" itemprop="headline"><?php _e( 'Latest Galleries', 'greatermedia' ); ?></h2>
<?php

/*
 * Posts per page is 16, for the main section.
 *
 * On the first page, we get a large primary and two smaller secondary items, so posts per page goes up to 19.
 * On pages after page 1, we get only 16 items, but the overall offset is 3, to account for the 3 extra items on page 1.
 *
 * Here, we calculate those values!
 */
$page = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;

if ( $page > 1 ) {
	$per_page = 16;
	$offset = 3;
} else {
	$per_page = 19;
	$offset = 0;
}

$query_args = array(
	'post_type'      => array( 'gmr_gallery' ),
	'orderby'        => 'date',
	'order'          => 'DESC',
	'posts_per_page' => 3,
	'offset'         => 0,
);

if ( 'show' == get_post_type() ) {
	$term = \TDS\get_related_term( get_the_ID() );
	if ( $term ) {
		$query_args['tax_query'] = array(
			array(
				'taxonomy' => '_shows',
				'field'    => 'slug',
				'terms'    => $term->slug,
			)
		);
	}
}

$query = new WP_Query( $query_args );

if ( $query->have_posts() ) : ?>

	<div class="gallery__featured">

		<div class="gallery__featured--primary">
			<?php $query->the_post(); ?>
			<?php get_template_part( 'partials/gallery-featured', 'primary' ); ?>
		</div>

		<div class="gallery__featured--secondary">

			<?php if ( $query->have_posts() && $query->post_count > 1 ) : ?>
				<?php $query->the_post(); ?>
				<?php get_template_part( 'partials/gallery-featured', 'secondary' ); ?>
			<?php endif; ?>

			<?php if ( $query->have_posts() && $query->post_count > 2 ) : ?>
				<?php $query->the_post(); ?>
				<?php get_template_part( 'partials/gallery-featured', 'secondary' ); ?>
			<?php endif; ?>

		</div>

	</div>

	<?php wp_reset_postdata(); ?>

	<div class="gallery__grid">

		<?php get_template_part( 'partials/loop', 'album' ); ?>

	</div>

	<?php wp_reset_postdata(); ?>

	<div class="gallery__grid">

		<?php get_template_part( 'partials/loop', 'gallery' ); ?>

	</div>

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

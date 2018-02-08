<?php

$post = get_queried_object();

$image = false;
$category = current( wp_get_post_terms( $post->ID, 'listing-category' ) );
if ( $category ) :
	$image_id = get_term_meta( $category->term_id, 'featured-image', true );
	if ( ! empty( $image_id ) ) :
		$image = wp_get_attachment_image_url( $image_id, 'full' );
	endif;
endif;

get_header();

the_post();

?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'single-directory-listing' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

		<div class="single-directory-listing__hero" style="background-image: url(<?php echo esc_url( get_the_post_thumbnail_url( null, 'gm-article-thumbnail' ) ); ?>);"></div>

		<header class="single-directory-listing__header">
			<?php // TODO The mockups look like this should be a separate thumbnail set on the single post. The featured image is a car, this thumbnail is the brand logo. ?>
			<div class="single-directory-listing__thumbnail">
				<?php if ( $image ) : ?>
					<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $category->name ); ?>">
				<?php endif; ?>
			</div>
			<h1 class="single-directory-listing__title"><?php the_title(); ?></h1>
		</header>

		<div class="single-directory-listing__content">
			<?php the_content(); ?>

			<div class="inquire"><a class="inquire__link" href="<?php echo esc_url( get_permalink() ); ?>">Inquire</a></div>
		</div>


		<?php get_template_part( 'partials/directory-listing/related-items' ); ?>

	</article>

<?php get_footer();

<?php
/**
 * Archive template file for directory listing
 */

$category = get_queried_object();
$image_id = get_term_meta( $category->term_id, 'featured-image', true );

get_header();

?>
<section class="directory-category" role="article">
	<div class="directory-category__hero" style="background-image: url(<?php echo esc_url( wp_get_attachment_image_url( $image_id, 'gm-article-thumbnail' ) ); ?>">
		<div class="directory-category__title-wrapper">
			<h1 class="directory-category__title"><?php echo esc_attr( $category->name ); ?></h1>
		</div>
	</div>

		<div class="directory-category__wrapper">
			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'partials/directory-listing/list-item' ); ?>
			<?php endwhile; ?>
		</div>
</section>
<?php

get_footer();

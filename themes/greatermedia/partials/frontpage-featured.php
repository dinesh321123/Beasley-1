<?php
/**
 * Partial for the Front Page Featured Content
 *
 * @package Greater Media
 * @since   0.1.0
 */
?>
<section id="featured" class="home__featured">
	<?php if ( is_news_site() ) {

		get_template_part( 'partials/news/featured');

	} else {

		get_template_part( 'partials/music/featured' );

	} ?>
</section>
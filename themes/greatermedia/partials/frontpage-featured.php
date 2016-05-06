<?php
/**
 * Partial for the Front Page Featured Content
 *
 * @package Greater Media
 * @since   0.1.0
 */
?>
<?php if ( is_news_site() ) { ?>
	<section id="featured" class="home__featured home__featured_news">
		<?php get_template_part( 'partials/news/featured'); ?>
	</section>
<?php } else { ?>
	<section id="featured" class="home__featured home__featured_music">
		<?php	
			get_template_part( 'partials/music/featured' );
		?>
	
	</section>
<?php } ?>

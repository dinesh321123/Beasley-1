<?php
/**
 * Single contest submission template file
 *
 * @package Greater Media
 * @since   0.1.0
 */

get_header();
?>
	<div class="container">
		<section class="content">
			<p><a href="<?php echo esc_url( get_the_permalink( get_post()->post_parent ) ); ?>">Return to <?php echo esc_html( get_the_title( get_post()->post_parent ) ); ?></a></p>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
				<?php get_template_part( 'partials/submission', 'preview' ); ?>
			</article>
		</section>
	</div>
<?php
get_footer();

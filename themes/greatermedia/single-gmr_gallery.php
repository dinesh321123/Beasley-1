<?php
/**
 * Single Post template file
 *
 * @package Greater Media
 * @since   0.1.0
 */

get_header();

if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

	<?php do_action( 'gmr_gallery' ); ?>

	<div class="container">

	<section class="content">

	<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article" itemscope
	         itemtype="http://schema.org/BlogPosting">

		<div class="ad__inline--right desktop">
			<?php do_action( 'acm_tag', 'dfp_ad_right_rail_pos1' ); ?>
		</div>

		<header class="entry__header">

			<time class="entry__date" datetime="<?php echo get_the_time(); ?>"><?php the_date( 'F j, Y' ); ?></time>
			<h2 class="entry__title" itemprop="headline"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			</h2>
			<?php get_template_part( 'partials/social-share' ); ?>

		</header>

		<section class="entry-content" itemprop="articleBody">

			<?php the_content(); ?>

		</section>

		<?php get_template_part( 'partials/article', 'footer' ); ?>

		<?php
		$current_gallery = get_post();
		$parent_album    = $post->post_parent;

		$parent_post = get_post( $parent_album );

		if ( $parent_album > 0 ) {
			$args     = array(
				'post_type'    => 'gmr_gallery',
				'post_parent'  => $parent_album,
				'post__not_in' => array( $post->ID )
			);
			$siblings = new WP_Query( $args );
			ob_start();

			if ( $siblings->have_posts() ) : ?>

				<section class="entry__related-posts">

					<h2 class="section-header">More Galleries in <a
							href="<?php echo esc_url( post_permalink( $parent_post ) ); ?>"><?php echo $parent_post->post_title; ?></a>
					</h2>

					<?php while ( $siblings->have_posts() ) : $siblings->the_post(); ?>

						<?php get_template_part( 'partials/gallery-grid' ); ?>

					<?php endwhile; ?>

				</section>

			<?php endif; ?>

			<?php
			$secondary_content = ob_get_clean();
			echo apply_filters( 'the_secondary_content', $secondary_content, $current_gallery );
			?>

		<?php } ?>

	</article>

<?php endwhile; ?>

<?php else : ?>

	<article id="post-not-found" class="hentry cf">

		<header class="entry__header">

			<h2 class="entry__title"><?php _e( 'Oops, Post Not Found!', 'greatermedia' ); ?></h2>

		</header>

		<section class="entry__content">

			<p><?php _e( 'Uh Oh. Something is missing. Try double checking things.', 'greatermedia' ); ?></p>

		</section>

	</article>

	</section>

	</div>

<?php endif;

get_footer();

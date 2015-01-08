<?php
/**
 * Single Post template file
 *
 * @package Greater Media
 * @since   0.1.0
 */

get_header(); ?>

	<main class="main" role="main">

		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

			<?php do_action( 'gmr_gallery' ); ?>

		<div class="container">

			<section class="content">

				<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

					<div class="ad__inline--right">
						<img src="http://placehold.it/300x250&amp;text=inline ad">
					</div>

					<header class="entry__header">

						<time class="entry__date" datetime="<?php echo get_the_time(); ?>"><?php the_date('F j'); ?></time>
						<h2 class="entry__title" itemprop="headline"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<a class="icon-facebook social-share-link" href="http://www.facebook.com/sharer/sharer.php?u=[URL]&title=[TITLE]"></a>
						<a class="icon-twitter social-share-link" href="http://twitter.com/home?status=[TITLE]+[URL]"></a>
						<a class="icon-google-plus social-share-link" href="https://plus.google.com/share?url=[URL]"></a>

					</header>

					<section class="entry-content" itemprop="articleBody">

						<?php the_content(); ?>

					</section>

					<?php get_template_part( 'partials/post', 'footer' ); ?>

					<?php
						$current_gallery = get_post();
						$parent_album = $post->post_parent;
						if ( $parent_album > 0 ) {
							$args = array(
									'post_type'		=> 'gmr_gallery',
									'post_parent'	=> $parent_album,
									'post__not_in'	=> array( $post->ID )
								);
							$siblings = new WP_Query( $args );
							
							if ( $siblings->have_posts() ) : ?>

								<section class="entry__related-posts">
								
									<h2 class="section-header">More Galleries in this Album</h2>

								<?php while ( $siblings->have_posts() ) : $siblings->the_post(); ?>

									<?php get_template_part( 'partials/gallery-grid' ); ?>

								</section>

							<?php endwhile; endif;
						}
					?>

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

		<?php endif; ?>

	</main>

<?php get_footer();
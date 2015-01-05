<?php
/**
 * The front page template file
 *
 * @package Greater Media
 * @since   0.1.0
 */

get_header();

?>

	<main class="main" role="main">

		<div class="container">

		<?php
			get_template_part( 'partials/frontpage', 'featured' );
			get_template_part( 'partials/frontpage', 'highlights' );
		?>

			<section class="entries">				
				<div class="ad__leaderboard desktop">
					<img src='http://placehold.it/728x90'>
					<?php // do_action( 'acm_tag', 'leaderboard-body' ); ?>
				</div>

				<h2 class="content__heading">Latest from WMGK</h2>

				<?php $post_count = 0; ?>
				<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
				
					<?php 
					if ( 0 == ++$post_count % 5 ): 
					?>
						<div class='entry2-ad-wrap'>
					<?php endif; ?>
				
					<?php get_template_part( 'partials/entry' ); ?>
					
					<?php if ( 0 == $post_count % 5 ):	?>
							<div class='entry2-ad-wrap__ad mobile'>
								<img src='http://placehold.it/180x150'>
							</div>						
							<div class='entry2-ad-wrap__ad desktop'>
								<img src='http://placehold.it/300x250'>
							</div>						
						</div>
					<?php endif; ?>

				<?php endwhile; ?>

					<div class="posts-pagination">

						<div class="posts-pagination--previous"><?php next_posts_link( '<i class="fa fa-angle-double-left"></i>Previous' ); ?></div>
						<div class="posts-pagination--next"><?php previous_posts_link( 'Next<i class="fa fa-angle-double-right"></i>' ); ?></div>

					</div>

				<?php else : ?>

					<article id="post-not-found" class="hentry cf">

						<header class="article-header">

							<h1><?php _e( 'Oops, Post Not Found!', 'greatermedia' ); ?></h1>

						</header>

						<section class="entry-content">

							<p><?php _e( 'Uh Oh. Something is missing. Try double checking things.', 'greatermedia' ); ?></p>

						</section>

					</article>

				<?php endif; ?>

			</section>

		</div>

	</main>

<?php get_footer(); ?>
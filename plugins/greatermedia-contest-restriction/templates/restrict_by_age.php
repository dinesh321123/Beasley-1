<?php
/**
 * Created by Eduard
 * Date: 04.11.2014 16:51
 */

get_header(); ?>

	<main class="main" role="main">

		<div class="container">

			<section class="content">

				<?php
					if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

						<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

							<header class="entry-header">

								<h2 class="entry-title" itemprop="headline"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

							</header>
							<?php
							if( !isset( $_COOKIE["contest_res"]) ):
							?>
							<div id="dialog-form">
								<label for="age">Please enter your age:</label>
								<br />
								<input type="text" id="age" name="age"/>
							</div>

							<p>Please enter your age</p>
							<?php
							endif;
							global $post;
							$min_age = get_post_meta( $post->ID, '_min_age', true );
							if( isset( $_COOKIE["contest_res"]) && $_COOKIE["contest_res"]['age'] <  $min_age ):
							?>
								<p>
									Your are not eligible for this contest.
								</p>
							<?php
							endif;
							?>
						</article>

					<?php endwhile;

					else : ?>

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

<?php get_footer();
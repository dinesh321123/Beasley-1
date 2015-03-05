<?php
/**
 * Single Post template file
 *
 * @package Greater Media
 * @since   0.1.0
 */

get_header(); ?>

	<main class="main" role="main">
		<div class="container">

			<section class="content">
				
				<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

					<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf collapsed' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

						<?php if ( has_post_thumbnail() ) : ?>
							<div class="contest__thumbnail">
								<?php the_post_thumbnail( 'gmr-contest-thumbnail', array( 'class' => 'single__featured-img--contest' ) ); ?>
							</div>
						<?php endif; ?>

						<section class="col__inner--left">

							<header class="entry__header">

								<time class="entry__date" datetime="<?php echo get_the_time(); ?>"><?php the_date( 'F j, Y' ); ?></time>
								<h2 class="entry__title" itemprop="headline"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
								<?php get_template_part( 'partials/social-share' ); ?>

							</header>


							<div class="contest__restrictions">

								<div class="contest__restriction--signin">
									<p>
										You must be signed in to enter the contest!
									</p>
									<p>
										<a href="<?php echo esc_url( gmr_contests_get_login_url() ); ?>">Sign in here</a>
									</p>
								</div>

								<div class="contest__restriction--one-entry">
									<p>You have already taken this survey!</p>
								</div>
								
							</div>

							<?php the_content(); ?>

							<?php get_template_part( 'partials/article', 'footer' ); ?>

						</section>

						<section id="survey-form" class="col__inner--right contest__form"<?php gmr_survey_container_attributes(); ?>>
						</section>

					</article>

				<?php endwhile; else : ?>

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

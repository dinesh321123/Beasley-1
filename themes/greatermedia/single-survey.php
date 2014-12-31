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
			<?php if ( is_gigya_user_logged_in() ) {
				if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

						<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

							<?php if ( has_post_thumbnail() ) {

									the_post_thumbnail( 'full', array( 'class' => 'single__featured-img--survey' ) );

								}
							?>

							<section class="col__inner--left">

								<header class="entry__header">

									<time class="entry__date" datetime="<?php echo get_the_time(); ?>"><?php the_date('F j'); ?></time>
									<h2 class="entry__title" itemprop="headline"><?php the_title(); ?></h2>
									<a class="icon-facebook social-share-link" href="http://www.facebook.com/sharer/sharer.php?u=[URL]&title=[TITLE]"></a>
									<a class="icon-twitter social-share-link" href="http://twitter.com/home?status=[TITLE]+[URL]"></a>
									<a class="icon-google-plus social-share-link" href="https://plus.google.com/share?url=[URL]"></a>

								</header>

								<?php the_content(); ?>

								other metabox content goes here

							</section>


							<section class="col__inner--right">
								<?php

								$form = get_post_meta( get_the_ID(), 'survey_embedded_form', true );
								GreaterMediaSurveyFormRender::render( get_the_ID(), $form );

								?>
							</section>

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

					<?php endif;
				} else {
						?>
				<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
					<section class="col__inner--left">

						<h3>You must be signed in to take this survey</h3>
						<?php

						global $wp;
						$dest = '/' . trim( $wp->request, '/' );

						?>
						<a href="<?php echo gigya_profile_path( 'login', array( 'dest' => $dest ) ); ?>">Sign in</a>

					</section>

				</article>
						<?php
				} ?>

			</section>

		</div>

	</main>

<?php get_footer();

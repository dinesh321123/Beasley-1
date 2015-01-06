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

					<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

						<?php if ( is_gigya_user_logged_in() ) : ?>

							<?php if ( has_post_thumbnail() ) : ?>
								<div class="contest__thumbnail">
									<?php the_post_thumbnail( 'gmr-contest-thumbnail', array( 'class' => 'single__featured-img--contest' ) ); ?>
								</div>
							<?php endif; ?>

							<section class="col__inner--left">

								<header class="entry__header">

									<?php $encoded_permalink = urlencode( get_permalink() ); ?>
									<?php $encoded_title = urlencode( get_the_title() ); ?>

									<time class="entry__date" datetime="<?php echo get_the_time(); ?>"><?php the_date( 'F j' ); ?></time>
									<h2 class="entry__title" itemprop="headline"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

									<a class="icon-facebook social-share-link" href="http://www.facebook.com/sharer/sharer.php?u=<?php echo $encoded_permalink; ?>&title=<?php echo $encoded_title; ?>"></a>
									<a class="icon-twitter social-share-link" href="http://twitter.com/home?status=<?php echo $encoded_title; ?>+<?php echo $encoded_permalink; ?>"></a>
									<a class="icon-google-plus social-share-link" href="https://plus.google.com/share?url=<?php echo $encoded_permalink; ?>"></a>

								</header>

								<?php the_content(); ?>

								<?php get_template_part( 'partials/post', 'footer' ); ?>
								
							</section>


							<section class="col__inner--right contest__form">
								<?php

								$form = get_post_meta( get_the_ID(), 'survey_embedded_form', true );
								GreaterMediaSurveyFormRender::render( get_the_ID(), $form );

								?>
							</section>

						<?php else : ?>

							<header class="article-header">

								<h1><?php _e( 'You must be signed in to take this survey!', 'greatermedia' ); ?></h1>

							</header>

							<section class="entry-content">

								<p><?php
									printf(
										__( 'Please, sign in <a href="%s">here</a> to proceed.', 'greatermedia' ),
										gigya_profile_path( 'login', array( 'dest' => parse_url( get_permalink(), PHP_URL_PATH ) ) )
									);
								?></p>

							</section>

						<?php endif; ?>

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

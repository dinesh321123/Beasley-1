<?php
/**
 * Single Post template file
 *
 * @package Greater Media
 * @since   0.1.0
 */

get_header(); ?>

	<main class="main" role="main">

			<?php if ( defined( 'GREATER_MEDIA_GIGYA_TEST_UI' ) && GREATER_MEDIA_GIGYA_TEST_UI ) {
				if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

			<div class="container">

				<section class="content">

						<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

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

								<?php if ( ( $contest_prize = trim( get_post_meta( get_the_ID(), 'prizes-desc', true ) ) ) ) : ?>
									<div class="contest__description">
										<h3 class="contest__prize--title"><?php _e( 'What you win:', 'greatermedia' ); ?></h3>
										<?php echo wpautop( $contest_prize ); ?>
									</div>
								<?php endif; ?>

								<?php if ( ( $enter = trim( get_post_meta( get_the_ID(), 'how-to-enter-desc', true ) ) ) ) : ?>
									<div class="contest__description">
										<?php echo wpautop( $enter ); ?>
									</div>
								<?php endif; ?>

								<?php if ( ( $contest_rules = trim( get_post_meta( get_the_ID(), 'rules-desc', true ) ) ) ) : ?>
								<div class="contest__description">
									<p>
										<a class="contest-attr--rules-toggler" href="#" data-toggle="collapse" data-target="#contest-rules" data-alt-text="Hide Contest Rules">
											<?php _e( 'Show Contest Rules', 'greatermedia' ); ?>
										</a>
									</p>
									
									<div id="contest-rules" class="contest-attr--rules" style="display:none;"><?php echo wpautop( $contest_rules ); ?></div>
								</div>
								<?php endif; ?>

								<?php the_content(); ?>

								<?php
								/**
								 * @todo replace content in `.contest__sponsors` with dynamic content
								 */
								?>
								<div class="contest__sponsors">
									<h3 class="contest__sponsors--heading"><?php _e( 'Sponsored', 'greatermedia' ); ?></h3>
									<ul class="contest__sponsors--list">
										<li class="contest__sponsor"><a href="#"><img src="http://lorempixel.com/150/100/"></a></li>
										<li class="contest__sponsor"><a href="#"><img src="http://lorempixel.com/130/100/"></a></li>

									</ul>
								</div>

								<footer class="entry__footer">

									<div class="entry__categories">
										<div class="entry__list--title"><?php _e( 'Category', 'greatermedia' ); ?></div>
										<ul class="entry__list--categories">
											<?php echo get_the_term_list( $post->ID, 'category', '<li class="entry__list--item">', ',</li><li class="entry__list--item">', '</li>' ); ?>
										</ul>
									</div>

									<div class="entry__tags">
										<div class="entry__list--title"><?php _e( 'Tags', 'greatermedia' ); ?></div>
										<ul class="entry__list--tags">
											<?php echo get_the_term_list( $post->ID, 'post_tag', '<li class="entry__list--item">', ',</li><li class="entry__list--item">', '</li>' ); ?>
										</ul>
									</div>

									<?php
									/**
									 * @todo replace content in `.entry__shows` with dynamic content
									 */
									?>
									<div class="entry__shows">
										<div class="entry__list--title"><?php _e( 'Shows', 'greatermedia' ); ?></div>
										<ul class="entry__list--shows">
											<li class="entry__list--show"><div class="entry__show--logo"><img src="http://lorempixel.com/100/100/people/"></div><div class="entry__show--name"><a href="#"><?php _e( 'Test Show', 'greatermedia' ); ?></a></div></li>
											<li class="entry__list--show"><div class="entry__show--logo"><img src="http://lorempixel.com/100/100/people/"></div><div class="entry__show--name"><a href="#"><?php _e( 'Test Show', 'greatermedia' ); ?></a></div></li>
										</ul>
									</div>

								</footer>

							</section>


							<section id="contest-form" class="col__inner--right contest__form">
								LOADING... <?php //TODO: replace with a spinner ?>
							</section>

							<?php get_template_part( 'partials/submission', 'tiles' ); ?>

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
				} else if ( true /*is_gigya_user_logged_in()*/ ) {

					if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

						<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

							<header class="entry-header">

								<h2 class="entry-title" itemprop="headline">
									<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

							</header>

							<?php

							$post_id         = get_the_ID();
							$contest_form_id = get_post_meta( $post_id, 'contest_form_id', true );

							if ( $contest_form_id ) {
								gravity_form( $contest_form_id );
							}

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

					<?php endif;
				} else {

					echo '<article><h3>Please login</h3></article>';

				} ?>

			</section>

		</div>

	</main>

<?php get_footer();

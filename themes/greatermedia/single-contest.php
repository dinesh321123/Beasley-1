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

			<?php  if ( have_posts() ) : ?>

				<?php while ( have_posts() ) : the_post(); ?>

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

							<div class="contest__entry--link">
								<a href="#contest-form" class="contest__entry--btn">Enter Contest</a>
							</div>

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

							<?php get_template_part( 'partials/post', 'footer' ); ?>

						</section>


						<section id="contest-form" class="col__inner--right contest__form">
							LOADING... <?php //TODO: replace with a spinner ?>
						</section>

						<?php get_template_part( 'partials/submission', 'tiles' ); ?>

					</article>

				<?php endwhile; ?>

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

<?php get_footer();
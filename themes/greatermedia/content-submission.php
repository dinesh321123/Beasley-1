<?php while ( have_posts() ) : the_post(); ?>

	<div class="container">

		<?php if ( has_post_thumbnail() ) {

				the_post_thumbnail( 'full', array( 'class' => 'single__featured-img' ) );

			}
		?>

		<section class="content">

			<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

				<div class="ad__inline--right desktop">
					<?php // 'desktop' is a variant, can call a 'mobile' variant elsewhere if we need it, but never the same variant twice ?>
					<?php do_action( 'acm_tag_gmr_variant', 'mrec-body', 'desktop' ); ?>
				</div>

				<header class="entry__header">

					<time class="entry__date" datetime="<?php echo get_the_time(); ?>"><?php the_date('F j'); ?></time>
					<h2 class="entry__title" itemprop="headline"><?php the_title(); ?></h2>
					<a class="icon-facebook social-share-link" href="http://www.facebook.com/sharer/sharer.php?u=[URL]&title=[TITLE]"></a>
					<a class="icon-twitter social-share-link" href="http://twitter.com/home?status=[TITLE]+[URL]"></a>
					<a class="icon-google-plus social-share-link" href="https://plus.google.com/share?url=[URL]"></a>

				</header>

				<section class="entry-content" itemprop="articleBody">

					<?php if ( ( $next_submission = apply_filters( 'gmr_contest_next_submission', null, get_the_ID() ) ) ) : ?>
					<a href="<?php echo esc_url( get_permalink( $next_submission ) ); ?>" style="float: right;">Next Submission</a>
					<?php endif; ?>
					
					<?php if ( ( $prev_submission = apply_filters( 'gmr_contest_prev_submission', null, get_the_ID() ) ) ) : ?>
					<a href="<?php echo esc_url( get_permalink( $prev_submission ) ); ?>">Prev Submission</a>
					<?php endif; ?>

					<?php the_content(); ?>

				</section>

				<div class="ad__inline--right mobile">
					<?php do_action( 'acm_tag_gmr_variant', 'mrec-body', 'mobile' ); ?>
				</div>

				<footer class="entry__footer">
					<?php

						// If comments are open or we have at least one comment, load up the comment template.
						if ( comments_open() || get_comments_number() ) {
							comments_template();
						}

					?>

				</footer>

			</article>

		</section>

	</div>

<?php endwhile; ?>

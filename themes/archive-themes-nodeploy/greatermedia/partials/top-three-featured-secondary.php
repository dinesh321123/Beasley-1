<?php
/**
 * Top 3 Category Partial -- Secondary Featured Item
 */

?><article id="post-<?php the_ID(); ?>" class="top-three__feature--secondary" role="article" itemscope itemtype="http://schema.org/BlogPosting">
	<a href="<?php the_permalink(); ?>">
		<div class="top-three__feature">
			<div class="top-three__thumbnail">
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="thumbnail" style="background-image: url(<?php bbgi_post_thumbnail_url( null, true, 708, 389 ); ?>)"></div>
				<?php else: ?>
					<div class="thumbnail thumbnail-placeholder"></div>
				<?php endif; ?>
			</div>

			<?php if ( has_post_format( 'video' ) ) : ?>
				<div class="top-three__play"></div>
			<?php endif; ?>

			<div class="top-three__desc">
				<div class='inner-wrap'>
					<h3><?php the_title(); ?></h3>

					<time class="top-three__date" datetime="<?php the_time( 'c' ); ?>">
						<?php the_time( 'M j, Y' ); ?>
					</time>
				</div>
			</div>
		</div>
	</a>
</article>

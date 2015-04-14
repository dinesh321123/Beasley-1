<?php
/**
 * Top 3 Category Partial -- Primary Featured Item
 */

?>
<article id="post-<?php the_ID(); ?>" class="top-three__feature--primary" role="article" itemscope itemtype="http://schema.org/BlogPosting">
	<a href="<?php the_permalink(); ?>">
		<div class="top-three__feature">
			<div class='top-three__thumbnail'>
				<?php if ( has_post_thumbnail() ) : ?>
					<div class='thumbnail' style='background-image: url(<?php gm_post_thumbnail_url( 'gmr-show-featured-primary', null, true ); ?>)'></div>
				<?php else: ?>
					<div class='thumbnail thumbnail-placeholder' style=''></div>
				<?php endif; ?>
			</div>
			<div class="top-three__play"></div>
			<div class="top-three__desc">
				<div class='inner-wrap'>
					<h3><?php the_title(); ?></h3>
					<time class="top-three__date" datetime="<?php the_time( 'c' ); ?>"><?php the_time( 'M j, Y' ); ?></time>
				</div>
			</div>
		</div>
	</a>
</article>

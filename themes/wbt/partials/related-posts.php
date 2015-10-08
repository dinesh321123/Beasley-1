<?php if ( have_posts() ):?>
	<div class='related-posts'>
		<h3 class='related-posts__title'>More from WBT</h3>
		
		<div class='related-posts__list'>
			<?php while (have_posts()) : the_post(); ?>
				<a href="<?php the_permalink() ?>" rel="bookmark" class='related-post'>
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'related-post__item cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
						<div class="related-post__img">
							<div class="thumbnail" style='background-image: url(<?php gm_post_thumbnail_url( 'gm-related-post', null, true ); ?>)'></div>
						</div>
						<div class="related-post__meta">
							<div class="related-post__title"><?php the_title(); ?></div>
						</div>
					</article>
				</a>
			<?php endwhile; ?>
		</div>
	</div>
<?php endif; ?>
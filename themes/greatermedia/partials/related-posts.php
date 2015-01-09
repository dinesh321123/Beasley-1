<?php if ( have_posts() ):?>
	<div class='related-posts'>
		<h3 class='related-posts__title'>Related</h3>
		
		<div class='related-posts__list'>
			<?php while (have_posts()) : the_post(); ?>
				<a href="<?php the_permalink() ?>" rel="bookmark" class='related-post'>
					<article>
						<div class="related-post__img">
							<div class="thumbnail" style='background-image: url(<?php gm_post_thumbnail_url( 'gm-related-post' ); ?>)'></div>
						</div>
						<div class="related-post__meta">
							<div class="related-post__title"><?php the_title(); ?></div>
							<time class="related-post__date" datetime="<?php the_time( 'c' ); ?>"><?php the_time('M j') ?></time>
						</div>
					</article>
				</a>
			<?php endwhile; ?>
		</div>
	</div>
<?php endif; ?>
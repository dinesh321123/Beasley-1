<?php

$contest_id = get_the_ID();

?><article id="post-<?php echo esc_attr( $contest_id ); ?>" <?php post_class( 'cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
	<?php bbgi_featured_image_layout_is( $contest_id, 'top' ) && get_template_part( 'partials/feature-image-contest' ); ?>

	<header class="entry__header">
		<time class="entry__date" datetime="<?php echo get_the_time(); ?>"><?php the_date( 'F j, Y' ); ?></time>

		<h2 class="entry__title" itemprop="headline">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h2>

		<?php get_template_part( 'partials/social-share' ); ?>
	</header>

	<?php bbgi_featured_image_layout_is( $contest_id, 'inline' ) && get_template_part( 'partials/feature-image-contest' ); ?>

	<?php the_content(); ?>
	<?php get_template_part( 'partials/article', 'footer' ); ?>
</article>

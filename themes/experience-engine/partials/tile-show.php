<div id="post-<?php the_ID(); ?>" <?php post_class(); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
	<?php get_template_part( 'partials/tile/thumbnail' ); ?>
	<?php get_template_part( 'partials/tile/title' ); ?>
	<p class="show-time"><?php echo ee_get_show_meta( null, 'show-time' ); ?></p>
</div>

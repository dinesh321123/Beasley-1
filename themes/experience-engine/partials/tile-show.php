<div id="post-<?php the_ID(); ?>" <?php post_class('show-tile'); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
	<?php get_template_part( 'partials/tile/thumbnail' ); ?>
	<div class="meta">
		<?php get_template_part( 'partials/tile/title' ); ?>
		<p class="timeslot"><?php echo ee_get_show_meta( null, 'show-time' ); ?></p>
	</div>
</div>

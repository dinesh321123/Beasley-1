<div id="post-<?php the_ID(); ?>" <?php post_class( array( 'contest-tile', '-horizontal', '-full-width' ) ); ?>>
	<?php get_template_part( 'partials/tile/thumbnail' ); ?>
	<div class="post-meta">
		<?php get_template_part( 'partials/tile/title' ); ?>
		<?php ee_the_contest_dates(); ?>
	</div>
</div>

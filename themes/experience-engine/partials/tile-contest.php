<div id="post-<?php the_ID(); ?>" <?php post_class( array( 'type-contest', 'contest-tile', '-horizontal', '-full-width' ) ); ?>>
	<?php get_template_part( 'partials/tile/thumbnail' ); ?>
	<?php get_template_part( 'partials/tile/title' ); ?>
	<div class="meta">
		<?php ee_the_contest_dates(); ?>
	</div>
</div>

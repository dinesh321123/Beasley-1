<div data-post-id="post-<?php the_ID(); ?>" <?php post_class( array( 'type-contest', 'contest-tile' ) ); ?>>
	<?php get_template_part( 'partials/tile/thumbnail' ); ?>

	<div class="meta">
		<?php get_template_part( 'partials/tile/title' ); ?>
		<?php ee_the_contest_dates(); ?>
	</div>
</div>

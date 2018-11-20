<div id="post-<?php the_ID(); ?>" <?php post_class( 'podcast-tile' ); ?>>
	<?php get_template_part( 'partials/tile/thumbnail' ); ?>
	<div class="meta">
		<?php get_template_part( 'partials/tile/title' ); ?>

		<div class="excerpt">
			<?php the_excerpt(); ?>
		</div><?php

		if ( ! ee_is_jacapps() ) :
			ee_the_latest_episode();
		endif;

		?><p class="count">
			<?php echo esc_html( ee_get_episodes_count() ); ?> episodes
		</p>
	</div>
</div>

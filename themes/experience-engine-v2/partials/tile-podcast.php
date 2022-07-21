<div data-post-id="post-<?php the_ID(); ?>" <?php post_class( 'podcast-tile' ); ?>>
	<?php get_template_part( 'partials/tile/thumbnail' ); ?>
	<div class="meta">
		<?php get_template_part( 'partials/tile/title' ); ?> <?php //Podcast tile title must remain within meta to ensure layout is properly set on single episode page ?>

		<?php if( get_the_excerpt() ): ?><div class="excerpt">
			<?php the_excerpt(); ?>
		</div><?php endif;

		if ( ! ee_is_common_mobile() ) :
			ee_the_latest_episode();
		endif;

		?><p class="count">
			<?php echo esc_html( ee_get_episodes_count() ); ?> episodes
		</p>
	</div>
</div>

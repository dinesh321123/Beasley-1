<div id="post-<?php the_ID(); ?>" <?php post_class( 'podcast-tile' ); ?>>
	<?php get_template_part( 'partials/tile/thumbnail' ); ?>
	<div class="meta">
		<?php get_template_part( 'partials/tile/title' ); ?> <?php //Podcast tile title must remain within meta to ensure layout is properly set on single episode page ?>

		<?php if( get_the_excerpt() ): ?><div class="excerpt">
			<?php the_excerpt(); ?>
		</div><?php endif;

		if ( ! ee_is_jacapps() ) :
			ee_the_latest_episode();
		endif;

		?><p class="count">
			<?php echo esc_html( ee_get_episodes_count() ); ?> episodes
		</p>
	</div>

	<p class="type">
		<svg width="14" height="16">
			<path d="M8.794 9.231a2.86 2.86 0 0 0 1.069-2.228 2.868 2.868 0 0 0-2.865-2.865 2.868 2.868 0 0 0-2.865 2.865c0 .9.417 1.703 1.068 2.228a2.86 2.86 0 0 0-1.068 2.229v3.82h5.73v-3.82c0-.9-.418-1.703-1.069-2.229zm-.205 4.775H5.406V11.46c0-.878.714-1.592 1.592-1.592.877 0 1.591.714 1.591 1.592v2.546zM6.998 8.595a1.593 1.593 0 0 1-1.592-1.592c0-.878.714-1.592 1.592-1.592.877 0 1.591.714 1.591 1.592 0 .878-.714 1.592-1.591 1.592z"/>
			<path d="M11.952 2.051A6.957 6.957 0 0 0 7 0C5.13 0 3.371.729 2.048 2.051a7.01 7.01 0 0 0 0 9.904l.45.45.9-.9-.45-.45a5.737 5.737 0 0 1 0-8.103A5.695 5.695 0 0 1 7 1.273c1.53 0 2.97.597 4.052 1.679a5.737 5.737 0 0 1 0 8.103l-.45.45.9.9.45-.45a7.011 7.011 0 0 0 0-9.904z"/>
		</svg>
		podcast
	</p>
</div>

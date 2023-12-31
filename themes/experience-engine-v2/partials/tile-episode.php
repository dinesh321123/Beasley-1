<div data-post-id="post-<?php the_ID(); ?>" <?php post_class( 'episode-tile' ); ?>>
	<div class="post-thumbnail">
		<?php ee_the_lazy_thumbnail(); ?>
		<?php ee_the_episode_player(); ?>
	</div>

	<div class="meta">
		<?php get_template_part( 'partials/tile/title' ); ?>

		<div class="episode-meta">
			<?php if ( ( $duration = ee_get_episode_meta( null, 'duration' ) ) ) : ?>
				<span class="duration">
					<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none">
						<circle cx="6.14" cy="6.14" r="5.515" stroke="currentcolor" stroke-width="1.25"/>
						<path fill="currentcolor" fill-rule="evenodd" d="M6.348 3.2a.25.25 0 0 0-.25.25V6.2h-1.75a.25.25 0 0 0-.25.25v.5c0 .138.112.25.25.25h2.5a.25.25 0 0 0 .25-.25v-3.5a.25.25 0 0 0-.25-.25h-.5z" clip-rule="evenodd"/>
					</svg>
					<?php echo esc_html( $duration ); ?>
				</span>
			<?php endif; ?>

			<?php ee_the_episode_download( 'download -nobor' ); ?>
			<span class="date"><?php ee_the_date(); ?></span>
			<?php ee_the_share_buttons( get_permalink(), get_the_title() ); ?>
		</div>
	</div>
</div>

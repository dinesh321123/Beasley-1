<div <?php post_class(); ?>>
	<div class="podcast-image">
		<?php ee_the_lazy_thumbnail(); ?>
	</div>
	<h3>
		<a href="<?php the_permalink(); ?>">
			<?php the_title(); ?>
		</a>
	</h3>
	<div><?php

		if ( ! ee_is_jacapps() ) :
			ee_the_latest_episode();
		endif;

		?><div>
			<?php echo esc_html( ee_get_episodes_count() ); ?> episodes
		</div>
	</div>
</div>

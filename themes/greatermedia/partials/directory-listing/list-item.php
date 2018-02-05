<div>
	<div>
		<?php the_post_thumbnail(); ?>
	</div>
	<div>
		<h3>
			<a href="<?php echo esc_url( get_permalink() ) ?>">
				<?php the_title(); ?>
			</a>
		</h3>
		<?php the_excerpt(); ?>
	</div>
	<div>
		<a href="<?php echo esc_url( get_permalink() ); ?>">INQUIRE</a>
	</div>
</div>
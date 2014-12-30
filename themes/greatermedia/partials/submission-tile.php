<li class="contest-submission">
	<a class="contest-submission--link" href="<?php the_permalink(); ?>">
		<?php the_post_thumbnail(); ?>

		<span class="contest-submission--author"><?php echo esc_html( gmr_contest_submission_get_author() ); ?></span>
		<span class="contest-submission--date"><?php echo esc_html( get_the_date() ); ?></span>
	</a>
</li>
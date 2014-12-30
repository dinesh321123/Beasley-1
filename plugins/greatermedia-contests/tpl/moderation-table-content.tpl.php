<td class="ugc-moderation-content <?php echo $class; ?>">
	<ul class="ugc-moderation-meta">
		<?php if ( ! empty( $listener_name ) ) : ?>
			<li class="listener-name">
				<?php if (! empty( $listener_gigya_id )) : ?>
				<a href="#<?php echo esc_attr( $listener_gigya_id ); ?>">
					<?php endif; ?>
					<?php echo $listener_name; ?>
					<?php if (! empty( $listener_gigya_id )) : ?>
				</a>
			<?php endif; ?>

			</li>
		<?php endif; ?>
		<?php if ( ! empty( $contest ) ) : ?>
			<li class="contest"><a href="<?php echo get_permalink( $contest->ID ); ?>">
					<?php echo get_the_title( $contest->ID ); ?>
				</a></li>
		<?php endif; ?>
		<li class="post-date"><?php echo get_the_date( 'm/d/Y', $post->ID ); ?></li>
	</ul>
	<?php echo $preview; ?>
</td>
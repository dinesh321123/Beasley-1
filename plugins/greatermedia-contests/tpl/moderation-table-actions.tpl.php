<th scope="row" id="ugc<?php the_ID(); ?>" class="ugc-moderation-actions <?php echo $class; ?>">
	<?php
	if ( $can_edit_post ) {

		?>
		<div class="ugc-moderation-primary-controls">
			<label class="screen-reader-text" for="cb-select-<?php the_ID(); ?>"><?php printf( __( 'Select %s' ), $title ); ?></label>
			<input id="cb-select-<?php the_ID(); ?>" type="checkbox" name="ugc[]" value="<?php the_ID(); ?>" />
			<a href="<?php echo esc_attr( wp_nonce_url( GreaterMediaUserGeneratedContentModerationTable::approve_link( $post->ID ), 'approve-ugc_' . $post->ID ) ); ?>" class="button" name="approve"><?php _e( 'Approve', 'greatermedia_ugc' ); ?></a>
		</div>
		<ul class="ugc-moderation-links">
			<li>
				<a href="<?php echo esc_attr( add_query_arg( 'action', 'edit', add_query_arg( 'post', get_the_ID(), admin_url( 'post.php' ) ) ) ); ?>"><?php _e( 'Edit' ); ?></a>
			</li>
			<?php if ( current_user_can( 'delete_post', $post->ID ) ) : ?>
				<li>
					<a class='submitdelete' title="<?php echo esc_attr( __( 'Move this item to the Trash' ) ); ?>" href="<?php echo esc_attr( get_delete_post_link( $post->ID ) ); ?>">
						<?php _e( 'Trash' ); ?>
					</a></li>
			<?php endif; ?>
		</ul>
	<?php
	}
	?>
</th>
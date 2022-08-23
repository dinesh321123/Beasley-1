<div class="meta">
	<div class="author-meta">
		<?php
			$contest_is_singular = is_singular( 'contest' );

			// Co author Checking
			$primary_author = get_field( 'primary_author_cpt', $post );
			$primary_author = $primary_author ? $primary_author : $post->post_author;
			$secondary_author = get_field( 'secondary_author_cpt', $post );

			$primary_author_name = $primary_author ? get_the_author_meta( 'display_name', $primary_author ) : '';
			$secondary_author_name = $secondary_author ? get_the_author_meta( 'display_name', $secondary_author) : '';
		?>
		<?php if ( ! $contest_is_singular ) : ?>
			<span class="author-avatar hide-avatar">
				<?php if ( is_singular() ) : ?>
					<?php
						$avatar = get_avatar( get_the_author_meta( 'ID' ), 40 );
						if ( $avatar ) {
							echo $avatar;	
						} else {
							echo '<img class="avatar avatar-40 photo" src="https://2.gravatar.com/avatar/e64c7d89f26bd1972efa854d13d7dd61?s=96&d=mm&r=g" height="40" width="40" alt="Placeholder Shilloutte User Image">';
						}
					?>
				<?php endif; ?>
			</span>

			<span class="author-meta-name">
				<?php
					if($secondary_author_name) {
							echo "<span style='color:rgba(68, 68, 68, 0.6);'>By </span>".$primary_author_name." <span style='color:rgba(68, 68, 68, 0.6);'> and </span> ".$secondary_author_name;
					} else {
						the_author_meta( 'display_name', $primary_author);
					}
				?>
			</span>
		<?php endif; ?>

		<span class="author-meta-date">
			<?php ee_the_date(); ?>
		</span>
	</div>

	<div class="share-wrap-icons">
		<span class="label">Share</span>
		<?php ee_the_share_buttons( get_permalink(), get_the_title() ); ?>
	</div>

	<?php ee_the_sponsored_by_div( get_the_id(), !$contest_is_singular ); ?>
</div>

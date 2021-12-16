<div class="meta">
	<div class="author-meta">
		<?php
			$contest_is_singular = is_singular( 'contest' );

			// Co author Checking
			$is_co_author_cpt = get_field( 'is_co_author_cpt', $post );
			$reported_attribution_cpt = get_field( 'reported_attribution_cpt', $post );

			$is_coauthor = (!empty($is_co_author_cpt) && $is_co_author_cpt[0] == 'true') ? true : false;
			$additional_author_name = '';
			$additional_author_avatar = '';
			if(!empty($reported_attribution_cpt)) {
				$additional_author_name = $reported_attribution_cpt['display_name'];
				$additional_author_avatar = get_avatar( $reported_attribution_cpt['ID'], 40 );
			}
		?>
		<?php if ( ! $contest_is_singular ) : ?>
			<span class="author-avatar">
				<?php if ( is_singular() ) : ?>
					<?php
						$avatar = get_avatar( get_the_author_meta( 'ID' ), 40 );
						if($additional_author_name) {
							if($is_coauthor) {
								if ( $avatar ) {
									echo $avatar;	
								} else {
									echo '<img class="avatar avatar-40 photo" '.( $additional_author_name ? 'style="margin-right: 0.3rem;"' : '' ).' src="https://2.gravatar.com/avatar/e64c7d89f26bd1972efa854d13d7dd61?s=96&d=mm&r=g" height="40" width="40" alt="Placeholder Shilloutte User Image">';
								}
							}

							if ( $additional_author_avatar ) {
								echo $additional_author_avatar;	
							} else {
								echo '<img class="avatar avatar-40 photo" src="https://2.gravatar.com/avatar/e64c7d89f26bd1972efa854d13d7dd61?s=96&d=mm&r=g" height="40" width="40" alt="Placeholder Shilloutte User Image">';
							}
						} else {
							if ( $avatar ) {
								echo $avatar;	
							} else {
								echo '<img class="avatar avatar-40 photo" src="https://2.gravatar.com/avatar/e64c7d89f26bd1972efa854d13d7dd61?s=96&d=mm&r=g" height="40" width="40" alt="Placeholder Shilloutte User Image">';
							}
						}
					?>
				<?php endif; ?>
			</span>

			<span class="author-meta-name">
				<?php
					if($additional_author_name) {
						if($is_coauthor) {
							echo "<span style='color:rgba(68, 68, 68, 0.6);'>By: </span>".get_the_author_meta( 'display_name' )." <span style='color:rgba(68, 68, 68, 0.6);'> & </span> ".$additional_author_name;
						} else {
							echo $additional_author_name;
						}
					} else {
						the_author_meta( 'display_name' );
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

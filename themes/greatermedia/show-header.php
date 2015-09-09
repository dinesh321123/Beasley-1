<?php
	$src = get_bloginfo('template_directory').'/images/featured-bg.png';
	if ( has_post_thumbnail($post->ID) ) {
		$featured = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full' );
		$src = $featured[0];
	}
?>

<div class="show__header<?php if( has_post_thumbnail() ) echo ' has-thumbnail'; ?>"

	<?php if( has_post_thumbnail() ) { ?>
	style="background-image: url(<?php echo $src; ?>);"
	<?php } ?>
>
	<div class="show__header-content">
		<div class="show__cast">
			<?php if ( get_post_meta( $post->ID, 'logo_image', true ) ) {
		        $src = get_post_meta( $post->ID, 'logo_image', true );
		        echo wp_get_attachment_image( $src, 'thumbnail' );
			} ?>
		</div>
		<nav class="show__nav">
			<h1 class="show__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
			<?php
			if ( \GreaterMedia\Shows\uses_custom_menu( get_the_ID() ) ) :
				wp_nav_menu( array( 'menu' => \GreaterMedia\Shows\assigned_custom_menu_id( get_the_ID() ) ) );
			else : ?>
			<ul>
				<?php \GreaterMedia\Shows\about_link_html( get_the_ID() ); ?>
				<?php \GreaterMedia\Shows\podcasts_link_html( get_the_ID() ); ?>
				<?php \GreaterMedia\Shows\galleries_link_html( get_the_ID() ); ?>
				<?php \GreaterMedia\Shows\videos_link_html( get_the_ID() ); ?>
			</ul>
			<?php endif; ?>
		</nav>
		<div class="show__meta">
			<?php
				$days = \GreaterMedia\Shows\get_show_days( $post->ID );
				$times = \GreaterMedia\Shows\get_show_times( $post->ID );

				if ( ! empty( $days ) ) {
					echo '<em>' . $days . '</em>';
				}
				if ( ! empty( $times ) ) {
					echo '<em>' . $times . '</em>';
				}
			?>

			<?php if ( $facebook_url = get_post_meta( get_the_ID(), 'show/social_pages/facebook', true ) ): ?>
				<a href="<?php echo esc_url( $facebook_url ); ?>" class="icon-facebook social-share-link" target="_blank"></a>
			<?php endif; ?>

			<?php if ( $twitter_url = get_post_meta( get_the_ID(), 'show/social_pages/twitter', true ) ): ?>
				<a href="<?php echo esc_url( $twitter_url ); ?>" class="icon-twitter social-share-link" target="_blank"></a>
			<?php endif; ?>

			<?php if ( $instagram_url = get_post_meta( get_the_ID(), 'show/social_pages/instagram', true ) ): ?>
				<a href="<?php echo esc_url( $instagram_url ); ?>" class="icon-instagram social-share-link" target="_blank"></a>
			<?php endif; ?>

			<?php if ( $google_url = get_post_meta( get_the_ID(), 'show/social_pages/google', true ) ): ?>
				<a href="<?php echo esc_url( $google_url ); ?>" class="icon-google-plus social-share-link" target="_blank"></a>
			<?php endif; ?>
		</div>
	</div>
</div>

<!--
<nav class="show__nav--mobile">
	<ul>
		<?php \GreaterMedia\Shows\about_link_html( get_the_ID() ); ?>
		<?php \GreaterMedia\Shows\podcasts_link_html( get_the_ID() ); ?>
		<?php \GreaterMedia\Shows\galleries_link_html( get_the_ID() ); ?>
		<?php \GreaterMedia\Shows\videos_link_html( get_the_ID() ); ?>
	</ul>
</nav>
-->

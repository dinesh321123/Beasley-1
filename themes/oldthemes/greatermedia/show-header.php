<?php

$show_id = get_queried_object_id();

$featured_class = '';
$featured_image = get_bloginfo( 'template_directory' ) . '/images/featured-bg.png';
if ( has_post_thumbnail( $show_id ) ) {
	$featured = bbgi_get_image_url( get_post_thumbnail_id( $show_id ), 610, 382, true, true );
	if ( $featured ) {
		$thumbnail_class = 'has-thumbnail';
		$featured_image = $featured;
	}
}

$logo_image = '';
$logo_id = get_post_meta( $post->ID, 'logo_image', true );
if ( $logo_id ) {
	$logo = bbgi_get_image_url( $logo_id, 100, 100 );
	if ( $logo ) {
		$logo_image = $logo;
	}
}

?><div class="show__header <?php echo sanitize_html_class( $featured_class ); ?>" style="background-position: center; background-image: url(<?php echo esc_url( $featured_image ); ?>);">
	<div class="show__header-content">
		<div class="show__cast">
			<?php if ( $logo_image ) : ?>
				<img src="<?php echo esc_url( $logo_image ); ?>">
			<?php endif; ?>
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

<nav class="show__nav--mobile">
	<ul>
		<?php \GreaterMedia\Shows\about_link_html( get_the_ID() ); ?>
		<?php \GreaterMedia\Shows\podcasts_link_html( get_the_ID() ); ?>
		<?php \GreaterMedia\Shows\galleries_link_html( get_the_ID() ); ?>
		<?php \GreaterMedia\Shows\videos_link_html( get_the_ID() ); ?>
	</ul>
</nav>

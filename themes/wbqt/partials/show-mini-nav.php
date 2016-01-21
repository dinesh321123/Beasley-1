<?php if (is_singular()) {

	$the_id = get_the_ID();

	if (get_post_type( $the_id ) == "episode"){
		$the_id = wp_get_post_parent_id( $the_id );
	}

	$post_taxonomies = get_post_taxonomies();
	$shows = array();

	// Check to see if any shows are associated with this content
	$current_terms = get_the_terms( $the_id, ShowsCPT::SHOW_TAXONOMY );

	if ( ( $current_terms != false ) && ( is_array( $current_terms ) ) ){
		foreach ( $current_terms as $show ) :
			if ( ( $show = \TDS\get_related_post( $show ) ) ) :
				if ( \GreaterMedia\Shows\supports_homepage( $show->ID ) ) :
					array_push($shows, $show);
				endif;
			endif;
		endforeach;
	}

	// Only show the mini nav if the content is associated with one and only one show
	if (count( $shows ) == 1) :
		$show = $shows[0];
?>
<?php
	$srce = get_bloginfo('template_directory').'../images/featured-bg.png';
	if ( has_post_thumbnail($show->ID) ) {
		$featured = wp_get_attachment_image_src( get_post_thumbnail_id($show->ID), 'full' );
		$srce = $featured[0];
	}
?>

<div class="show__mini-nav">
<div class="show__header<?php if( has_post_thumbnail() ) echo ' has-thumbnail'; ?>"

	<?php if( has_post_thumbnail() ) { ?>
	style="background-image: url(<?php echo $srce; ?>);"
	<?php } ?>
>
		<div class="show__header-content">
			<div class="show__cast">
				<?php if ( get_post_meta( $show->ID, 'logo_image', true ) ) {
			        $src = get_post_meta( $show->ID, 'logo_image', true );
			        echo wp_get_attachment_image( $src, 'thumbnail' );
				} ?>
			</div>
			<nav class="show__nav">
				<h1 class="show__title"><a href="<?php echo get_the_permalink( $show->ID ); ?>"><?php echo get_the_title( $show->ID ); ?></a></h1>
				<?php
				if ( \GreaterMedia\Shows\uses_custom_menu( $show->ID ) ) :
					wp_nav_menu( array( 'menu' => \GreaterMedia\Shows\assigned_custom_menu_id( $show->ID ), 'container' => false ) );
				else : ?>
				<ul>
					<?php \GreaterMedia\Shows\about_link_html( $show->ID ); ?>
					<?php \GreaterMedia\Shows\podcasts_link_html( $show->ID ); ?>
					<?php \GreaterMedia\Shows\galleries_link_html( $show->ID ); ?>
					<?php \GreaterMedia\Shows\videos_link_html( $show->ID ); ?>
				</ul>
				<?php endif; ?>
			</nav>
			<div class="show__meta">
				<?php
					$days = \GreaterMedia\Shows\get_show_days( $show->ID );
					$times = \GreaterMedia\Shows\get_show_times( $show->ID );

					if ( ! empty( $days ) ) {
						echo '<em>' . esc_html($days) . '</em>';
					}
					if ( ! empty( $times ) ) {
						echo '<em>' . esc_html($times) . '</em>';
					}
				?>
							<?php if ( $facebook_url = get_post_meta( $show->ID, 'show/social_pages/facebook', true ) ): ?>
				<a href="<?php echo esc_url( $facebook_url ); ?>" class="icon-facebook social-share-link" target="_blank"></a>
			<?php endif; ?>

			<?php if ( $twitter_url = get_post_meta( $show->ID, 'show/social_pages/twitter', true ) ): ?>
				<a href="<?php echo esc_url( $twitter_url ); ?>" class="icon-twitter social-share-link" target="_blank"></a>
			<?php endif; ?>

			<?php if ( $instagram_url = get_post_meta( $show->ID, 'show/social_pages/instagram', true ) ): ?>
				<a href="<?php echo esc_url( $instagram_url ); ?>" class="icon-instagram social-share-link" target="_blank"></a>
			<?php endif; ?>

			<?php if ( $google_url = get_post_meta( $show->ID, 'show/social_pages/google', true ) ): ?>
				<a href="<?php echo esc_url( $google_url ); ?>" class="icon-google-plus social-share-link" target="_blank"></a>
			<?php endif; ?>
			</div>
		</div>
	</div>
</div>
<?php
	endif;
}
?>

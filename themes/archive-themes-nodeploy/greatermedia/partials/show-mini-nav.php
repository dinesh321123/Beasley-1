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

<div class="show__mini-nav">
	<div class="show__header has-thumbnail">
		<div class="show__header-content">
			<div class="show__cast">
				<?php if ( get_post_meta( $show->ID, 'logo_image', true ) ) {
			        $src = get_post_meta( $show->ID, 'logo_image', true );
			        echo wp_get_attachment_image( $src, 'thumbnail' );
				} ?>
			</div>
			<nav class="show__nav">
				<div class="show__title"><a href="<?php echo get_the_permalink( $show->ID ); ?>"><?php echo get_the_title( $show->ID ); ?></a></div>
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
		</div>
	</div>
</div>
<?php
	endif;
}
?>

<?php
$show = ee_get_current_show();
$slimmermobile = get_query_var( 'slimmermobile' );
if (!$show ) {
    $class = ' slimmer-show-mobile-menu';
} else {
    $class = '';
}
if ($show ) {

?><div id="slimmer-mobile-navigation" class="position-relative<?php echo $class ?>">
	<div class="mobile-navigation-logo">
		<?php if ( ( $logo = ee_get_show_meta( $show, 'logo' ) ) ) : ?>		<!--  -->			
				<img src="<?php echo wp_get_attachment_image_url( $logo ,array(40,40));?>" />
				<?php //ee_the_lazy_image( $logo ); ?>			
		<?php endif; ?>
	</div>
	<div class="slimmer-navigation-desktop-container" >
		<div class="title_description">
			<h2><?php echo esc_html( get_the_title( $show ) ); ?></h2>
		</div>
		<?php
		if ( \GreaterMedia\Shows\uses_custom_menu( $show->ID ) ) :
			wp_nav_menu( array(
				'menu'      => \GreaterMedia\Shows\assigned_custom_menu_id( $show->ID ),
				'container' => false,
				'menu_class'  => 'cnavigation', 
			) );
		else :
			// disables expand redirects functionality
			// see https://tenup.teamwork.com/#/tasks/18678852
			add_filter( 'bbgi_expand_redirects', '__return_false' );
			?><ul id="#" class="cnavigation menu" data-click-state="1">
				<?php \GreaterMedia\Shows\home_link_html( $show->ID ); ?>
				<?php \GreaterMedia\Shows\about_link_html( $show->ID ); ?>
				<?php \GreaterMedia\Shows\article_link_html( $show->ID ); ?>
				<?php \GreaterMedia\Shows\podcasts_link_html( $show->ID ); ?>
				<?php \GreaterMedia\Shows\galleries_link_html( $show->ID ); ?>
				<?php \GreaterMedia\Shows\listiclecpt_link_html( $show->ID ); ?>
				<?php \GreaterMedia\Shows\affiliate_marketing_link_html( $show->ID ); ?>
				<?php \GreaterMedia\Shows\videos_link_html( $show->ID ); ?>
			</ul><?php
		endif;
		remove_filter( 'bbgi_expand_redirects', '__return_false' );
		?>
	</div>
</div>

<?php } ?>

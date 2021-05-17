<?php
/**
 * Context.
 *
 * @var AMP_Post_Template $this
 */

?>
<?php
	$site_logo = ee_gmr_site_logo();
	// echo "<pre>", print_r($site_logo), print_r($site_colors), "</pre>"; 
?>
<header id="top" class="amp-wp-header">
	<div>
		<a href="<?php echo esc_url( $this->get( 'home_url' ) ); ?>">
			<?php
				$site_icon_url 			= $site_logo['theme']['logo']['url'];
				$site_icon_url_width 	= $site_logo['theme']['logo']['width'];
				$site_icon_url_height 	= $site_logo['theme']['logo']['height'];
			?>
			<?php if ( $site_icon_url ) { ?>
				<amp-img src="<?php echo esc_url( $site_icon_url ); ?>" width="140px" height="80px" class="amp-wp-site-icon"></amp-img>
			<?php } else { ?>
				<span class="amp-site-title">
					<?php echo esc_html( wptexturize( $this->get( 'blog_name' ) ) ); ?>
				</span>
			<?php } ?>
		</a>
	</div>
</header>

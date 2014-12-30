<?php
/**
 * The template for displaying the footer.
 *
 * @package Greater Media
 * @since 0.1.0
 */
?>
</div> <!-- / page-wrap -->
<footer class="footer" role="contentinfo">
	<div class="container">
		<div class="footer__content">
			<div class="footer__logo">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
			</div>
			<div class="footer__copyright">
				<span class="footer__copyright--span"><?php _e( 'Copyright (c) ', 'greatermedia' ); ?><?php bloginfo( 'name' ); ?></span><?php _e( '-', 'greatermedia' ); ?><span class="footer__copyright--span"><a href="http://www.greatermedia.com/"><?php _e( 'Greater Media', 'greatermedia' ); ?></a></span>
			</div>
			<div class="footer__menu">
				<?php do_action( 'gmr_social' ); ?>
				<?php
					$footer_nav = array(
						'theme_location'  => 'footer-nav',
						'menu'            => '',
						'container'       => 'nav',
						'container_class' => 'footer__nav',
						'container_id'    => '',
						'menu_class'      => 'footer__nav--list',
						'menu_id'         => 'footer__nav--list',
						'echo'            => true,
						'fallback_cb'     => 'wp_page_menu',
						'before'          => '',
						'after'           => '',
						'link_before'     => '',
						'link_after'      => '',
						'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
						'depth'           => 0,
						'walker'          => ''
					);
					wp_nav_menu( $footer_nav );
				?>
			</div>
		</div>
	</div>
</footer>
</div> <!-- / site-wrap -->
<?php wp_footer(); ?>
</body>
</html>
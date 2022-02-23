<?php
/**
 * The template for displaying the footer.
 *
 * @package Greater Media
 * @since 0.1.0
 */
?>
			<footer class="footer" role="contentinfo">
				<div class="container">
					<?php

					$post_types = array(
						'page',
						'post',
						'contest',
						'gmr_gallery',
						'tribe_events',
						'show',
					);

					if ( is_singular( $post_types ) ) { ?>
						<div class="footer__ad">
							<?php do_action( 'dfp_tag', 'dfp_ad_leaderboard_pos2', false, array( array( 'pos', 2 ) ) ); ?>
						</div>
					<?php } ?>
					<div class="footer__content">
						<div class="footer__copyright">
							<div class="footer__copyright--logo">
								<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
							</div>
							<span class="footer__copyright--span">
								&copy; <?php bloginfo( 'name' ); ?>
							</span>
							-
							<span class="footer__copyright--span">
								<a href="http://www.entercom.com/" target="_blank" rel="noopener noreferrer">Entercom</a>
							</span>
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
		</main>
	</div> <!-- / page-wrap -->
</div> <!-- / site-wrap -->
<div class="menu-overlay-mask"></div>
<div class="header-search-overlay-mask"></div>
<div class="busy-mask">
	<i class="gmr-icon icon-spinner icon-spin"></i>
</div>
<?php wp_footer(); ?>
</body>
</html>

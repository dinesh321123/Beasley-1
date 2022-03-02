<?php
/**
 * Header partial
 */
?><header id="header" class="header" role="banner">
	<?php do_action( 'show_breaking_news_banner' ); ?>

	<div class="header__main">
		<div class="container">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="header__logo"><?php
				$site_logo_id = get_option( 'gmr_site_logo', 0 );
				if ( $site_logo_id ) :
					$site_logo = wp_get_attachment_image_url( $site_logo_id, 'full' );
					if ( $site_logo ) :
						echo '<img src="' . esc_url( $site_logo ) . '" alt="' . get_bloginfo( 'name' ) . ' | ' . get_bloginfo( 'description' ) . '" class="header__logo--img">';
					endif;
				endif;
			?></a>

			<div class="header__main--navwrap">
				<div id="header__search" class="header__search">
					<label for="s" class="header__search--label"><span class="header__search--span"><?php esc_html_e( 'Search', 'greatermedia' ); ?></span>
						<i class="header__search--btn"></i>
					</label>
				</div><?php

				$main_nav = array(
					'theme_location'  => 'main-nav',
					'menu'            => '',
					'container'       => 'nav',
					'container_class' => 'header__nav',
					'container_id'    => '',
					'menu_class'      => 'header__nav--list',
					'menu_id'         => 'header__nav--list',
					'echo'            => true,
					'fallback_cb'     => 'wp_page_menu',
					'before'          => '',
					'after'           => '',
					'link_before'     => '',
					'link_after'      => '',
					'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
					'depth'           => 0,
					'walker'          => new GreaterMediaNavWalker(),
				);

				wp_nav_menu( $main_nav );
			?></div>

			<div class="mobile-nav__toggle">
				<div class="mobile-nav__toggle--span"></div>
			</div>
		</div>

		<?php get_template_part( 'partials/audio-interface' ); ?>
	</div>
</header>

<?php get_template_part( 'partials/audio-stream-container' ); ?>

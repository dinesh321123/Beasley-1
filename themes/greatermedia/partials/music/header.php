<?php
/**
 * Header partial for the Music sites
 */
?>
<header id="header" class="header" role="banner">
	<?php do_action( 'show_breaking_news_banner' ); ?>
	<div class="container container__leaderboard">
		<div class="ad__leaderboard desktop">
			<?php do_action( 'acm_tag', 'leaderboard-top-of-site' ); ?>
		</div>
		<div class="ad__leaderboard mobile">
			<?php do_action( 'acm_tag', 'smartphone-wide-banner' ); ?>
		</div>
	</div>
	<div class="header__main">
		<div class="container">
			<div class="mobile-nav__toggle">
				<div class="mobile-nav__toggle--span"></div>
			</div>

			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="header__logo">
				<?php do_action( 'gmr_site_logo' ); ?>
			</a>

			<?php
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
			?>
			<div class="header__secondary">
				<div class="header__account header__account--mobile">
					<a href="#" class="header__account--btn">
						<span class="icon-user"></span>
					</a>

					<div class="header__account--container">

					</div>
				</div>
				<div class="header__account header__account--small">
					<a href="#" class="header__account--btn">
						<span class="icon-user"></span>
					</a>

					<div class="header__account--container">

					</div>
				</div>
				<div class="header__account header__account--large">
					<a href="#" class="header__account--btn">
						<span class="icon-user"></span>
						<span class="header__account--logged-out"><?php _e( 'Login or Register', 'greatermedia' ); ?></span>
						<span class="header__account--logged-in"><?php _e( 'Logout', 'greatermedia' ); ?></span>
					</a>
					<div class="header__account--container">

					</div>
				</div>
				<div id="header__search" class="header__search">
					<label for="s" class="header__search--label"><i class="header__search--btn"></i><div class="header__search--span"><?php _e( 'Keyword Search', 'greatermedia' ); ?></div></label>
				</div>
			</div>
			<nav class="header__social" role="navigation">
				<?php do_action( 'gmr_social' ); ?>
			</nav>
		</div>
	</div>
</header>
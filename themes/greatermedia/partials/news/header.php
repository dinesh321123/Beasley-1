<?php
/**
 * Header partial for the News/Sports Site
 */
?>
<header id="header" class="header" role="banner">
	<?php do_action( 'show_breaking_news_banner' ); ?>
	<div class="header__leaderboard">
		<div class="container container__leaderboard">
			<div class="ad__leaderboard desktop">
				<?php do_action( 'acm_tag', 'leaderboard-top-of-site' ); ?>
			</div>
			<div class="ad__leaderboard mobile">
				<?php do_action( 'acm_tag', 'smartphone-wide-banner' ); ?>
			</div>
		</div>
	</div>
	<div class="header__sub">
		<div class="container">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="header__logo">
				<?php do_action( 'gmr_site_logo' ); ?>
			</a>
			<div class="header__subnav">
				<?php
				$secondary_nav = array(
					'theme_location'  => 'secondary-nav',
					'menu'            => '',
					'container'       => 'nav',
					'container_class' => 'secondary-nav',
					'container_id'    => '',
					'menu_class'      => 'secondary-nav-list',
					'menu_id'         => '',
					'echo'            => true,
					'fallback_cb'     => '',
					'before'          => '',
					'after'           => '',
					'link_before'     => '<div class="secondary-link">',
					'link_after'      => '</div>',
					'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
					'depth'           => 0,
				);
				wp_nav_menu( $secondary_nav );
				?>
				<div id="header__search" class="header__search">
					<label for="s" class="header__search--label"><i class="header__search--btn"></i><div class="header__search--span"><?php _e( 'Search', 'greatermedia' ); ?></div></label>
				</div>
			</div>
			<?php
			/**
			 * Comment out the sports scores for now
			 */
			/* get_template_part( 'partials/news/header', 'scores'); */
			?>
		</div>
	</div>
	<div class="header__main">
		<div class="container">
			<div class="mobile-nav__toggle">
				<div class="mobile-nav__toggle--span"></div>
			</div>
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
						<span class="header__account--span">Login</span>
					</a>
					<div class="header__account--container">

					</div>
				</div>
			</div>
		</div>
	</div>
</header>
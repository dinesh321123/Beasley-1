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
				<div class="header__subnav--item"><a href="#" class="header__link--schedule"><div class="header__link--text">Schedule</div></a></div>
				<div class="header__subnav--item"><a href="#" class="header__link--stocks"><div class="header__link--text">Stocks</div></a></div>
				<div class="header__subnav--item"><a href="#" class="header__link--traffic"><div class="header__link--text">Traffic</div></a></div>
				<div class="header__subnav--item"><a href="#" class="header__link--weather"><div class="header__link--text">Weather</div></a></div>
				<div id="header__search" class="header__search">
					<label for="s" class="header__search--label"><i class="header__search--btn"></i><div class="header__search--span"><?php _e( 'Search', 'greatermedia' ); ?></div></label>
				</div>
			</div>
			<div class="recent-scores__widget">
				<div class="score--visitor">
					<div class="team__logo">
						<img src="<?php bloginfo( 'stylesheet_directory' ); ?>/images/detroit-pistons-logo.png">
					</div>
					<div class="team__name">
						Detroit Pistons
						<div class="team__record">
							(14-24)
						</div>
					</div>
				</div>
				<div class="game__score">
					114 - 111
					<div class="game__score--status">
						Final
					</div>
				</div>
				<div class="score--home">
					<div class="team__name">
						Toronto Raptors
						<div class="team__record">
							(25-12)
						</div>
					</div>
					<div class="team__logo">
						<img src="<?php bloginfo( 'stylesheet_directory' ); ?>/images/toronto-raptors-logo.png">
					</div>
				</div>
			</div>
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
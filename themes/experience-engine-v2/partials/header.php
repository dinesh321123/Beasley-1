<div class="header-and-news-container">
	<header class="primary-mega-topbar">
		<div class="container">
			<div class="top-header">
				<div class="brand-logo">
					<div class="logo" itemscope itemtype="http://schema.org/Organization">
						<?php ee_the_custom_logo( 154, 88, 'main-custom-logo' ); ?>
						<span class="screen-reader-text"><?php wp_title(); ?></span>
					</div>
					<div class="additional-logos">
						<?php ee_the_subheader_logo( 'desktop', 154, 88 ); ?>
					</div>
				</div>
				<nav id="js-primary-mega-nav" class="primary-nav top-primarynav" itemscope="itemscope" itemtype="http://schema.org/SiteNavigationElement">
					<?php get_template_part( 'partials/primary', 'navigation' ); ?>
				</nav>
				<div class="top-right-menu">
					<div class="head-social">
						<?php if ( ee_has_publisher_information( 'facebook' ) ) : ?>
							<a href="<?php echo esc_url( ee_get_publisher_information( 'facebook' ) ); ?>" aria-label="Go to station's Facebook page" target="_blank" rel="noopener">
								<svg xmlns="http://www.w3.org/2000/svg" fill="#000000" viewBox="0 0 64 64" width="25px" height="25px"><path d="M32,6C17.642,6,6,17.642,6,32c0,13.035,9.603,23.799,22.113,25.679V38.89H21.68v-6.834h6.433v-4.548 c0-7.529,3.668-10.833,9.926-10.833c2.996,0,4.583,0.223,5.332,0.323v5.965h-4.268c-2.656,0-3.584,2.52-3.584,5.358v3.735h7.785 l-1.055,6.834h-6.73v18.843C48.209,56.013,58,45.163,58,32C58,17.642,46.359,6,32,6z"/></svg>
							</a>
						<?php endif; ?>
						<?php if ( ee_has_publisher_information( 'twitter' ) ) : ?>
							<a href="<?php echo esc_url( ee_get_publisher_information( 'twitter' ) ); ?>" aria-label="Go to station's Twitter page" target="_blank" rel="noopener">
								<svg xmlns="http://www.w3.org/2000/svg" fill="#000000" viewBox="0 0 30 30" width="25px" height="25px">    <path d="M28,6.937c-0.957,0.425-1.985,0.711-3.064,0.84c1.102-0.66,1.947-1.705,2.345-2.951c-1.03,0.611-2.172,1.055-3.388,1.295 c-0.973-1.037-2.359-1.685-3.893-1.685c-2.946,0-5.334,2.389-5.334,5.334c0,0.418,0.048,0.826,0.138,1.215 c-4.433-0.222-8.363-2.346-10.995-5.574C3.351,6.199,3.088,7.115,3.088,8.094c0,1.85,0.941,3.483,2.372,4.439 c-0.874-0.028-1.697-0.268-2.416-0.667c0,0.023,0,0.044,0,0.067c0,2.585,1.838,4.741,4.279,5.23 c-0.447,0.122-0.919,0.187-1.406,0.187c-0.343,0-0.678-0.034-1.003-0.095c0.679,2.119,2.649,3.662,4.983,3.705 c-1.825,1.431-4.125,2.284-6.625,2.284c-0.43,0-0.855-0.025-1.273-0.075c2.361,1.513,5.164,2.396,8.177,2.396 c9.812,0,15.176-8.128,15.176-15.177c0-0.231-0.005-0.461-0.015-0.69C26.38,8.945,27.285,8.006,28,6.937z"/></svg>
							</a>
						<?php endif; ?>
						<?php if ( ee_has_publisher_information( 'instagram' ) ) : ?>
							<a href="<?php echo esc_url( ee_get_publisher_information( 'instagram' ) ); ?>" aria-label="Go to station's Instagram page" target="_blank" rel="noopener">
								<svg xmlns="http://www.w3.org/2000/svg" fill="#000000" viewBox="0 0 24 24" width="25px" height="25px">    <path d="M 8 3 C 5.243 3 3 5.243 3 8 L 3 16 C 3 18.757 5.243 21 8 21 L 16 21 C 18.757 21 21 18.757 21 16 L 21 8 C 21 5.243 18.757 3 16 3 L 8 3 z M 8 5 L 16 5 C 17.654 5 19 6.346 19 8 L 19 16 C 19 17.654 17.654 19 16 19 L 8 19 C 6.346 19 5 17.654 5 16 L 5 8 C 5 6.346 6.346 5 8 5 z M 17 6 A 1 1 0 0 0 16 7 A 1 1 0 0 0 17 8 A 1 1 0 0 0 18 7 A 1 1 0 0 0 17 6 z M 12 7 C 9.243 7 7 9.243 7 12 C 7 14.757 9.243 17 12 17 C 14.757 17 17 14.757 17 12 C 17 9.243 14.757 7 12 7 z M 12 9 C 13.654 9 15 10.346 15 12 C 15 13.654 13.654 15 12 15 C 10.346 15 9 13.654 9 12 C 9 10.346 10.346 9 12 9 z"/></svg>
							</a>
						<?php endif; ?>
						<?php if ( ee_has_publisher_information( 'youtube' ) ) : ?>
							<a href="<?php echo esc_url( ee_get_publisher_information( 'youtube' ) ); ?>" aria-label="Go to station's YouTube channel" target="_blank" rel="noopener">
								<svg xmlns="http://www.w3.org/2000/svg" fill="#000000" viewBox="0 0 24 24" width="25px" height="25px"><path d="M18,5.9H7c-2.2,0-3.9,1.7-3.9,3.9v5.5c0,2.2,1.7,3.9,3.9,3.9h11c2.2,0,3.9-1.7,3.9-3.9V9.8C21.9,7.6,20.1,5.9,18,5.9z M15.3,12.8l-5.1,2.4c-0.1,0.1-0.3,0-0.3-0.2v-5c0-0.2,0.2-0.3,0.3-0.2l5.1,2.6C15.5,12.5,15.5,12.7,15.3,12.8z"/></svg>
							</a>
						<?php endif; ?>
						<?php if ( ee_has_publisher_information( 'twitch' ) ) : ?>
							<a href="<?php echo esc_url( ee_get_publisher_information( 'twitch' ) ); ?>" aria-label="Go to station's Twitch page" target="_blank" rel="noopener">
								<svg xmlns="http://www.w3.org/2000/svg" fill="#000000" viewBox="0 0 24 24" width="25px" height="25px">    <path d="M 8 3 C 5.243 3 3 5.243 3 8 L 3 16 C 3 18.757 5.243 21 8 21 L 16 21 C 18.757 21 21 18.757 21 16 L 21 8 C 21 5.243 18.757 3 16 3 L 8 3 z M 8 5 L 16 5 C 17.654 5 19 6.346 19 8 L 19 16 C 19 17.654 17.654 19 16 19 L 8 19 C 6.346 19 5 17.654 5 16 L 5 8 C 5 6.346 6.346 5 8 5 z M 17 6 A 1 1 0 0 0 16 7 A 1 1 0 0 0 17 8 A 1 1 0 0 0 18 7 A 1 1 0 0 0 17 6 z M 12 7 C 9.243 7 7 9.243 7 12 C 7 14.757 9.243 17 12 17 C 14.757 17 17 14.757 17 12 C 17 9.243 14.757 7 12 7 z M 12 9 C 13.654 9 15 10.346 15 12 C 15 13.654 13.654 15 12 15 C 10.346 15 9 13.654 9 12 C 9 10.346 10.346 9 12 9 z"/></svg>
							</a>
						<?php endif; ?>
					</div>
					<?php
						echo get_search_form(
							array(
								'aria_label' => 'header-search-form',
								'for_header_section' => true
							)
						);
					?>
					<div class="listen-dropdown">
						<div id="my-listen-dropdown2">
							<button onclick="document.getElementById('my-listen-dropdown2').style.display = 'none';">
								<svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0 0 30 30" style=" fill:#ffffff;">    <path d="M 7 4 C 6.744125 4 6.4879687 4.0974687 6.2929688 4.2929688 L 4.2929688 6.2929688 C 3.9019687 6.6839688 3.9019687 7.3170313 4.2929688 7.7070312 L 11.585938 15 L 4.2929688 22.292969 C 3.9019687 22.683969 3.9019687 23.317031 4.2929688 23.707031 L 6.2929688 25.707031 C 6.6839688 26.098031 7.3170313 26.098031 7.7070312 25.707031 L 15 18.414062 L 22.292969 25.707031 C 22.682969 26.098031 23.317031 26.098031 23.707031 25.707031 L 25.707031 23.707031 C 26.098031 23.316031 26.098031 22.682969 25.707031 22.292969 L 18.414062 15 L 25.707031 7.7070312 C 26.098031 7.3170312 26.098031 6.6829688 25.707031 6.2929688 L 23.707031 4.2929688 C 23.316031 3.9019687 22.682969 3.9019687 22.292969 4.2929688 L 15 11.585938 L 7.7070312 4.2929688 C 7.5115312 4.0974687 7.255875 4 7 4 z"></path></svg>
							</button>
							<div class="drop-add">
								<?php get_template_part( 'partials/playing-now-info' ); ?>
							</div>
							<hr>
							<div class="on-air-list" id="live-player-recently-played">
								<?php
									if ( has_nav_menu( 'listen-live-nav' ) ) :
										wp_nav_menu(
											array(
												'container' => '',
												'theme_location' => 'listen-live-nav',
												'items_wrap' => '<ul id="%1$s" class="%2$s"><li><strong>'.wp_get_nav_menu_name('listen-live-nav').'</strong></li>%3$s</ul>'
											)
										);
									endif;
								?>
							</div>
							<?php if ( has_nav_menu( 'listen-live-nav' ) ) : ?>
								<hr>
							<?php endif; ?>
							<div>
								<?php get_template_part( 'partials/ads/drop-down' ); ?>
							</div>
						</div>
						<button id='listen-live-button'>
							<?php get_template_part( 'partials/player-button' ); ?>
							&nbsp;Listen Live
						</button>

					</div>
				</div>
			</div>
			<div class="primary-sidebar-navigation-new">
			</div>
		</div>
		<div class="container">
			<div class="additional-logos">
				<?php ee_the_subheader_logo( 'mobile', 462, 88 ); ?>
			</div>
		</div>
	</header>
	<?php do_action( 'show_breaking_news_banner' ); ?>
</div>
<?php get_template_part( 'partials/ads/top-scrolling' ); ?>


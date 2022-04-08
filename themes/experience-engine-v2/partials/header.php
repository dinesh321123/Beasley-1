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
							<svg width="10" height="20" fill="none" xmlns="http://www.w3.org/2000/svg"><title>Facebook</title><path d="M6.12 19.428H2.448V9.714H0V6.366h2.449l-.004-1.973C2.445 1.662 3.19 0 6.435 0h2.7v3.348H7.449c-1.263 0-1.324.468-1.324 1.342l-.005 1.675h3.036l-.358 3.348-2.675.001-.003 9.714z"></path></svg>
						</a>
					<?php endif; ?>
					<?php if ( ee_has_publisher_information( 'twitter' ) ) : ?>
						<a href="<?php echo esc_url( ee_get_publisher_information( 'twitter' ) ); ?>" aria-label="Go to station's Twitter page" target="_blank" rel="noopener">
							<svg width="21" height="18" fill="none" xmlns="http://www.w3.org/2000/svg"><title>Twitter</title><path d="M20.13 2.896a8.31 8.31 0 0 1-2.372.645 4.115 4.115 0 0 0 1.816-2.266c-.798.47-1.682.81-2.623.994A4.14 4.14 0 0 0 13.937.976c-2.281 0-4.13 1.833-4.13 4.095 0 .322.036.634.107.934A11.757 11.757 0 0 1 1.4 1.725a4.051 4.051 0 0 0-.559 2.06c0 1.42.73 2.674 1.838 3.409A4.139 4.139 0 0 1 .809 6.68v.052c0 1.985 1.423 3.64 3.312 4.016a4.172 4.172 0 0 1-1.865.07 4.13 4.13 0 0 0 3.858 2.845 8.33 8.33 0 0 1-5.129 1.754c-.333 0-.662-.02-.985-.058a11.758 11.758 0 0 0 6.33 1.84c7.597 0 11.75-6.24 11.75-11.654 0-.177-.003-.354-.011-.53a8.352 8.352 0 0 0 2.06-2.12z"></path></svg>
						</a>
					<?php endif; ?>
					<?php if ( ee_has_publisher_information( 'instagram' ) ) : ?>
						<a href="<?php echo esc_url( ee_get_publisher_information( 'instagram' ) ); ?>" aria-label="Go to station's Instagram page" target="_blank" rel="noopener">
							<svg width="17" height="18" fill="none" xmlns="http://www.w3.org/2000/svg"><title>Instagram</title><path d="M15.3.976H1.7c-.935 0-1.7.765-1.7 1.7v13.6c0 .935.765 1.7 1.7 1.7h13.6c.935 0 1.7-.765 1.7-1.7v-13.6c0-.935-.765-1.7-1.7-1.7zm-6.8 5.1c1.87 0 3.4 1.53 3.4 3.4 0 1.87-1.53 3.4-3.4 3.4a3.41 3.41 0 0 1-3.4-3.4c0-1.87 1.53-3.4 3.4-3.4zm-6.375 10.2c-.255 0-.425-.17-.425-.425V8.626h1.785c-.085.255-.085.595-.085.85 0 2.805 2.295 5.1 5.1 5.1 2.805 0 5.1-2.295 5.1-5.1 0-.255 0-.595-.085-.85H15.3v7.225c0 .255-.17.425-.425.425H2.125zM15.3 4.8c0 .255-.17.425-.425.425h-1.7c-.255 0-.425-.17-.425-.425V3.1c0-.255.17-.425.425-.425h1.7c.255 0 .425.17.425.425v1.7z"></path></svg>
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
							<ul>
								<li><strong>On Air Now:</strong></li>
								<li><a href="">Dave & Chuck The Freak Full Show</a></li>
								<li><a href="">Peep Show</a></li>
								<li><a href="">Tasty Bits</a></li>
								<li><a href="">Idiot Criminal of the Day</a></li>
								<li><a href="">VIEW MORE</a></li>
							</ul>
						</div>
						<hr>
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
<?php get_template_part( 'partials/ads/top-scrolling' ); ?>


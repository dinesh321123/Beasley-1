<?php
/**
 * The template for displaying the header.
 *
 * @package Greater Media
 * @since 0.1.0
 */
 ?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

	<!--[if lt IE 7]>
	<html class="no-js lt-ie9 lt-ie8 lt-ie7"><![endif]-->
	<!--[if (IE 7)&!(IEMobile)]>
	<html class="no-js lt-ie9 lt-ie8"><![endif]-->
	<!--[if (IE 8)&!(IEMobile)]>
	<html class="no-js lt-ie9"><![endif]-->
	<!--[if gt IE 8]><!-->
	<html class="no-js"><!--<![endif]-->

		<head>
			<meta charset="utf-8">

			<?php // Google Chrome Frame for IE ?>
			<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

			<title><?php wp_title( '' ); ?></title>

			<?php // mobile meta ?>
			<meta name="HandheldFriendly" content="True">
			<meta name="MobileOptimized" content="320">
			<meta name="viewport" content="width=device-width,height=device-height,user-scalable=no,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0">

			<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>

			<?php // wordpress head functions ?>
			<?php wp_head(); ?>
			<?php // end of wordpress head ?>

			<?php // drop Google Analytics Here ?>
			<?php // end analytics ?>

		</head>

		<body <?php body_class(); ?>>
			<div id="site-wrap">
				<?php

					$mobile_nav = array(
						'theme_location'  => 'main-nav',
						'menu'            => '',
						'container'       => 'nav',
						'container_class' => 'mobile-nav',
						'container_id'    => 'mobile-nav',
						'menu_class'      => 'mobile-nav__list',
						'menu_id'         => '',
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

					wp_nav_menu( $mobile_nav );

				?>
				<div id="page-wrap" class="page-wrap">
					<header id="header" class="header" role="banner">
						<?php do_action( 'show_breaking_news_banner' ); ?>
						<div class="container">
							<div class="ad__leaderboard">
								<img src="http://placehold.it/728x90&text=leaderboard">
							</div>
						</div>
						<div class="header__main">
							<div class="container">
								<div class="mobile-nav__toggle">
									<div class="mobile-nav__toggle--span"></div>
								</div>
								<div class="header__logo">
									<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/wmmr-logo.png" alt="<?php bloginfo( 'name' ); ?> | <?php bloginfo( 'description' ); ?>" class="header__logo--img"></a>
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
									'walker'          => ''
								);
								wp_nav_menu( $main_nav );
								?>
								<div class="header__secondary">
									<nav class="header__account">
										<ul class="header__account--list account">
											<li><a id="register-button" href="" class="register" style="visibility:hidden">register</a></li>
											<li><a id="login-button" href="" class="login" style="visibility:hidden">Login</a></li>
										</ul>
									</nav>
									<div class="header__social">
										<ul class="header__social--list">
											<li class="header__social--item"><a href="#"><i class="header__social--facebook"></i></a></li>
											<li class="header__social--item"><a href="#"><i class="header__social--twitter"></i></a></li>
											<li class="header__social--item"><a href="#"><i class="header__social--google-plus"></i></a></li>
										</ul>
									</div>
									<div class="header__search">
										<div class="header__search--span"><?php _e( 'Search', 'greatermedia' ); ?></div><i class="header__search--btn"></i>
									</div>
								</div>
							</div>
						</div>
					</header>

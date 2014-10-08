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
			<div id="styleguide-nav-toggle" class="styleguide-nav-toggle">
				<div class="styleguide-nav-toggle-span"></div>
			</div>
			<nav id="styleguide-nav" class="styleguide-nav" role="navigation">
				<div class="styleguide-nav-content">
					<ul class="styleguide-nav-list">
						<li class="styleguide-nav-list-item"><a href="#styleguide-header"><?php _e( 'Home', 'greatermedia' ); ?></a></li>
						<li class="styleguide-nav-list-item"><a href="##styleguide-colors"><?php _e( 'Colors', 'greatermedia' ); ?></a></li>
					</ul>
				</div>
			</nav>
			<header id="styleguide-header" class="styleguide-header styleguide-sections" role="banner">
				<div class="styleguide-content">
					<h1 class="styleguide-header-title">Greater Media Style Guide</h1>
				</div>
			</header>
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

		<body <?php body_class(); ?> onload="prettyPrint()">
			<div id="sg-nav--toggle" class="sg-nav--toggle">
				<div class="sg-nav--toggle--span"></div>
			</div>
			<nav id="nav" class="sg-nav" role="navigation">
				<div class="sg-nav--content">
					<ul class="sg-nav--list">
						<li class="sg-nav--item"><a href="#header"><?php _e( 'Home', 'greatermedia' ); ?></a></li>
						<li class="sg-nav--item"><a href="#colors"><?php _e( 'Colors', 'greatermedia' ); ?></a></li>
						<li class="sg-nav--item"><?php _e( 'Base Styles', 'greatermedia' ); ?>
							<ul class="sg-subnav-list">
								<li class="sg-nav--item"><a href="#headings"><?php _e( 'Headings', 'greatermedia' ); ?></a></li>
								<li class="sg-nav--item"><a href="#lists"><?php _e( 'Lists', 'greatermedia' ); ?></a></li>
								<li class="sg-nav--item"><a href="#misc-typography"><?php _e( 'Miscellaneous Typography', 'greatermedia' ); ?></a></li>
								<li class="sg-nav--item"><a href="#paragraph"><?php _e( 'Paragraph', 'greatermedia' ); ?></a></li>
							</ul>
						</li>
						<li class="sg-nav--list--item"><?php _e( 'Typography', 'greatermedia' ); ?>
							<ul class="sg-nav--sublist">
								<li class="sg-nav--item"><a href="#sans-serif"><?php _e( 'Sans Serif', 'greatermedia' ); ?></a></li>
								<li class="sg-nav--item"><a href="#serif"><?php _e( 'Serif', 'greatermedia' ); ?></a></li>
							</ul>
						</li>
						<li class="sg-nav--item"><a href="#icons"><?php _e( 'Icons', 'greatermedia' ); ?></a></li>
						<li class="sg-nav--item"><a href="#buttons"><?php _e( 'Buttons', 'greatermedia' ); ?></a></li>
						<li class="sg-nav--item"><a href="#forms"><?php _e( 'Forms', 'greatermedia' ); ?></a></li>
						<li class="sg-nav--item"><a href="#navigations"><?php _e( 'Navigations', 'greatermedia' ); ?></a></li>
						<li class="sg-nav--item"><a href="#discussions"><?php _e( 'Discussions', 'greatermedia' ); ?></a></li>
						<li class="sg-nav--item"><a href="#layout"><?php _e( 'Layout', 'greatermedia' ); ?></a></li>
						<li class="sg-nav--item"><a href="#live-player"><?php _e( 'Live Player', 'greatermedia' ); ?></a></li>
					</ul>
				</div>
			</nav>
			<header id="header" class="sg-header sg-sections" role="banner">
				<div class="sg-content">
					<h1 class="sg-header--title"><?php _e( 'Greater Media Style Guide', 'greatermedia' ); ?></h1>
				</div>
			</header>
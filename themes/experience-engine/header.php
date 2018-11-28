<!doctype html>
<html>
	<head <?php language_attributes(); ?>>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<style>
			:root {
				--brand-primary: #ff0000;
				--brand-secondary: #ffe964;
				--brand-tertiary: #ffffff;
			}
		</style>
		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?><?php ee_the_bbgiconfig_attribute(); ?>><?php
		do_action( 'beasley_after_body' );

		if ( ! ee_is_jacapps() ) :
			get_template_part( 'partials/header' );
		endif;

		?><div class="container">
			<main id="content" class="content">
				<?php get_template_part( 'partials/ads/leaderboard' ); ?>
					<div id="inner-content">

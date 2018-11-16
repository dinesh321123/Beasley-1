<!doctype html>
<html>
	<head <?php language_attributes(); ?>>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<?php wp_head(); ?>
	</head>
	<body <?php body_class( sanitize_html_class( get_theme_mod( 'ee_theme_version', '-dark' ) ) ); ?>><?php
		do_action( 'beasley_after_body' );

		if ( ! ee_is_jacapps() ) :
			get_template_part( 'partials/header' );
		endif;

		?><div class="container">
			<?php get_template_part( 'partials/ads/leaderboard' ); ?>
			<main id="content" class="content">

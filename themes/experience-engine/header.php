<!doctype html>
<html>
	<head <?php language_attributes(); ?>>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>
	<div class="skip-links">
		<a href="#q">Skip to Search</a>
		<a href="#live-player">Skip to Live Player</a>
		<a href="#content">Skip to Content</a>
		<a href="#footer">Skip to Footer</a>
	</div>
	<?php
		do_action( 'beasley_after_body' );

		if ( ! ee_is_jacapps() ) :
			get_template_part( 'partials/header' );
		endif;

		?>
		<div class="container">
			<main id="content" class="content">
				<?php get_template_part( 'partials/ads/leaderboard' ); ?>
					<div id="inner-content">

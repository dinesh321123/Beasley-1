<?php
use Bbgi\Integration\Google;
?>
<?php

	if (  is_front_page() ) {
		$headerCacheTag  = $_SERVER['HTTP_HOST'].'-'.'home';
	} else {
		global $post;
		$currentPostType	= "";
		$currentPostSlug	= "";
		if ( get_post_type() ) :
			$currentPostType = get_post_type();
		endif;
		if (  isset( $post->post_name ) && $post->post_name != "" ) :
			$currentPostSlug = "-".$post->post_name;
		endif;
		$headerCacheTag = $currentPostType.$currentPostSlug;
	}

	header("Cache-Tag: $headerCacheTag" . ",content", true);
	header("X-Cache-BBGI-Tag: $headerCacheTag", true);
?>
<!doctype html>
<html lang="en">
	<head <?php language_attributes(); ?>>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1"><?php

		if ( is_singular() && ! empty( $GLOBALS['ee_blog_id'] ) ) :
			add_action( 'wp_head', 'restore_current_blog', 1 );
			ee_switch_to_article_blog();
		endif;

		wp_head();

	?></head>
	<body <?php body_class( get_option( 'ee_theme_version', '-dark' ) ); ?>>
		<div class="skip-links">
			<a href="#q">Skip to Search</a>
			<a href="#live-player">Skip to Live Player</a>
			<a href="#content">Skip to Content</a>
			<a href="#footer">Skip to Footer</a>
		</div><?php

		do_action( 'beasley_after_body' );

		if ( ! ee_is_common_mobile() ) :
			get_template_part( 'partials/splash-screen' );
			get_template_part( 'partials/header' );
		endif;

		?><div id='main-container-div' class="container">
			<main id="content" class="content">
				<?php
					if ( class_exists( Google::class ) ) {
						Google::render_ga_placeholder();
					}
				?>
				<div id="inner-content">
					<?php
						if ( ee_is_whiz() ) {
							do_action( 'dfp_tag', 'top-leaderboard' );
						}
					?>


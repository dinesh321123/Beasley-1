<?php
use Bbgi\Integration\Google;
?>
<?php
	// if ( ! is_front_page() ) {
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
		header("Cache-Tag: $headerCacheTag", false);
		header("X-Cache-BBGI-Tag: $headerCacheTag", true);
		header("Cache-BBGI-Tag: Testing", true);
		header("X-Cache-BBGI-Tag: Testing", true);
		header("X-Cache-BBGI-Tag-f: $headerCacheTag", false);
		header("Cache-BBGI-Tag-f: Testing", false);
		header("X-Cache-BBGI-Tag-f: Testing", false);
	// }
?>
<!doctype html>
<html lang="en">
	<head <?php language_attributes(); ?>>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1"><?php

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

		?><div class="container">
			<main id="content" class="content">
				<?php
					if ( class_exists( Google::class ) ) {
						Google::render_ga_placeholder();
					}
				?>
				<?php do_action( 'show_breaking_news_banner' ); ?>
				<?php
					get_template_part( 'partials/ads/leaderboard' );
				?>
				<div id="inner-content">


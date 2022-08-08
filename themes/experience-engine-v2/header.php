<?php
use Bbgi\Integration\Google;
?>
<?php
	$headerCacheTag = [];
	global $post;
	if (  is_front_page() ) {
		$headerCacheTag[] = $_SERVER['HTTP_HOST'].'-'.'home';
	} else if (is_archive()) {
		global $wp_query;
		$urlCatArray = explode(',',$wp_query->query['category_name']);;

		$categories = get_categories();
		$categoriesSlug = wp_list_pluck($categories, 'slug' );

		array_walk($categoriesSlug, function ($value, $key) use ($urlCatArray, &$headerCacheTag){
			if(in_array($value,$urlCatArray)) {
				$headerCacheTag[] =   "archive" . "-" . $value;
			}
		});
		$obj = get_queried_object();

		if (isset($obj->slug)) {
			$headerCacheTag[] = "archive" . "-" . $obj->slug;
		}

		if (isset($wp_query->query['post_type'])) {
			$headerCacheTag[] = "archive-" . $wp_query->query['post_type'];
			$headerCacheTag[] = $wp_query->query['post_type'];
		}


	}  else {
		$currentPostType	= "";
		$currentPostSlug	= "";

		if ( get_post_type() ) :
			$currentPostType = get_post_type();
			$headerCacheTag[] = $currentPostType;

			if ($currentPostType == "episode") {
				$headerCacheTag[] = "podcast";
			}


		endif;
		if (  isset( $post->post_name ) && $post->post_name != "" ) :
			$currentPostSlug = "-".$post->post_name;
		endif;

		$headerCacheTag[] = $currentPostType.$currentPostSlug;
	}

	append_current_device_to_cache_tag($headerCacheTag);
	// $headerCacheTag = array_unique($headerCacheTag);
	header("Cache-Tag: " . implode(",", $headerCacheTag) , true);
	header("X-Cache-BBGI-Tag: " . implode(",", $headerCacheTag) , true);
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
					if ( ee_is_whiz() ) {
						echo '<div id="whiz-leaderboard-container">';
						do_action( 'dfp_tag', 'top-leaderboard' );
						echo '</div>';
					}
				?>
				<div id="inner-content">

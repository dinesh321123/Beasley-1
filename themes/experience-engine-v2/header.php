<?php
use Bbgi\Integration\Google;
?>
<?php

	$headerCacheTag = [];
	$mParticleContentType = '';
	$mparticle_pageview_event_data = [
		'mParticleContentType'						=> '',
		'mParticle_category'						=> '',
		'mParticle_categories'						=> '',
		'mParticle_show'							=> '',
		'mParticle_tags'							=> '',
		'mParticle_select_embed_parent_id'			=> '',
		'mParticle_select_embed_title'				=> '',
		'mParticle_select_embed_type'				=> '',
		'mParticle_select_embed_path' 				=> '',
		'mParticle_select_embed_post_id' 			=> '',
		'mParticle_select_embed_author' 			=> '',
		'mParticle_select_embed_primary_author' 	=> '',
		'mParticle_select_embed_secondary_author' 	=> '',
		'mParticle_post_id' 						=> '',
		'mParticle_post_slug' 						=> '',
		'mParticle_author' 							=> '',
		'mParticle_primary_author' 					=> '',
		'mParticle_secondary_author' 				=> '',
		'mParticle_word_count' 						=> null
	];
	$mparticle_pageview_event_data_search_page = '';


	if (  is_front_page() ) {
		$headerCacheTag[] = $_SERVER['HTTP_HOST'].'-'.'home';
		$mparticle_pageview_event_data['mParticleContentType'] = 'homepage';
		$mparticle_pageview_event_data['mParticle_category'] = (object) ['name' => 'Homepage'];
		$mparticle_pageview_event_data['mParticle_categories'] = '["Homepage"]';
	} else if (is_archive()) {
		$obj = get_queried_object();

		if (isset($obj->slug)) {
			$headerCacheTag[] = "archive" . "-" . $obj->slug;
		}

		if (isset($wp_query->query['post_type'])) {
			$headerCacheTag[] = "archive-" . $wp_query->query['post_type'];
			$headerCacheTag[] = $wp_query->query['post_type'];
			$mparticle_pageview_event_data['mParticleContentType'] = $wp_query->query['post_type'];
		}

		if ( is_category() ) {
			$mparticle_pageview_event_data['mParticleContentType'] = 'magazine';
		}

	}  else {
		global $post;
		$currentPostType	= "";
		$currentPostSlug	= "";

		if ( get_post_type() ) :
			$currentPostType = get_post_type();
			$headerCacheTag[] = $currentPostType;

			if ($currentPostType == "episode") {
				$headerCacheTag[] = "podcast";
			}

			$mparticle_pageview_event_data_search_page = $mparticle_pageview_event_data;
			$mparticle_pageview_event_data = ee_mparticle_prepare_pageview_data( $post );
		endif;
		if (  isset( $post->post_name ) && $post->post_name != "" ) :
			$currentPostSlug = "-".$post->post_name;
		endif;

		$headerCacheTag[] = $currentPostType.$currentPostSlug;
	}

	if ( is_search() && isset($_GET['s'] ) ) {
		if( !empty($mparticle_pageview_event_data_search_page) ) {
			$mparticle_pageview_event_data = $mparticle_pageview_event_data_search_page;
			$mparticle_pageview_event_data['mParticleContentType'] = "search results";
		}
	}

	append_current_device_to_cache_tag($headerCacheTag);

	header("Cache-Tag: " . implode(",", $headerCacheTag) , true);
	header("X-Cache-BBGI-Tag: " . implode(",", $headerCacheTag) , true);
?>
<!doctype html>
<html lang="en">
	<head <?php language_attributes(); ?>>
		<!-- OneTrust Cookies Consent Notice start for wmmr.bbgistage.com -->
		<script src="https://cdn.cookielaw.org/scripttemplates/otSDKStub.js"  type="text/javascript" charset="UTF-8" data-domain-script="1c169c70-919a-4cf6-9821-c279c1865b62-test" ></script>
		<script type="text/javascript">
		function OptanonWrapper() { }
		</script>
		<!-- OneTrust Cookies Consent Notice end for wmmr.bbgistage.com -->

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
				<?php

					$mparticle_implementation = sprintf(
							'<script class="mparticle_implementation">

    					console.log(\'Firing Page View - \' + window.location.href);
    					window.beasleyanalytics.setAnalyticsForMParticle(\'page_url\', window.location.href);
						window.beasleyanalytics.setAnalyticsForMParticle(\'title\', window.document.title);
						window.beasleyanalytics.setAnalyticsForMParticle(\'primary_category\', `%s`);
						window.beasleyanalytics.setAnalyticsForMParticle(\'primary_category_id\', `%s`);
						window.beasleyanalytics.setAnalyticsForMParticle(\'show_name\', `%s`);
						window.beasleyanalytics.setAnalyticsForMParticle(\'show_id\', `%s`);
						window.beasleyanalytics.setAnalyticsForMParticle(\'content_type\', `%s`);
						window.beasleyanalytics.setAnalyticsForMParticle(\'view_type\', `%s`);
						window.beasleyanalytics.setAnalyticsForMParticle(\'embedded_content_id\', `%s`);
						window.beasleyanalytics.setAnalyticsForMParticle(\'embedded_content_item_title\', `%s`);
						window.beasleyanalytics.setAnalyticsForMParticle(\'embedded_content_item_type\', `%s`);
						window.beasleyanalytics.setAnalyticsForMParticle(\'embedded_content_item_path\', `%s`);
						window.beasleyanalytics.setAnalyticsForMParticle(\'embedded_content_item_post_id\', `%s`);
						window.beasleyanalytics.setAnalyticsForMParticle(\'embedded_content_item_wp_author\', `%s`);
						window.beasleyanalytics.setAnalyticsForMParticle(\'embedded_content_item_primary_author\', `%s`);
						window.beasleyanalytics.setAnalyticsForMParticle(\'embedded_content_item_secondary_author\', `%s`);
						window.beasleyanalytics.setAnalyticsForMParticle(\'post_id\', `%s`);
						window.beasleyanalytics.setAnalyticsForMParticle(\'wp_author\', `%s`);
						window.beasleyanalytics.setAnalyticsForMParticle(\'primary_author\', `%s`);
						window.beasleyanalytics.setAnalyticsForMParticle(\'secondary_author\', `%s`);
						window.beasleyanalytics.setAnalyticsForMParticle(\'consent_cookie\', `%s`);
						window.beasleyanalytics.setAnalyticsForMParticle(\'publish_date\', %s);
						window.beasleyanalytics.setAnalyticsForMParticle(\'publish_day_of_the_week\', %s);
						window.beasleyanalytics.setAnalyticsForMParticle(\'publish_hour_of_the_day\', %s);
						window.beasleyanalytics.setAnalyticsForMParticle(\'publish_month\', %s);
						window.beasleyanalytics.setAnalyticsForMParticle(\'publish_time_of_day\', %s);
						window.beasleyanalytics.setAnalyticsForMParticle(\'publish_timestamp_local\', %s);
						window.beasleyanalytics.setAnalyticsForMParticle(\'publish_timestamp_UTC\', %s);
						window.beasleyanalytics.setAnalyticsForMParticle(\'publish_year\', %s);
						window.beasleyanalytics.setAnalyticsForMParticle(\'section_name\', `%s`);
						window.beasleyanalytics.setAnalyticsForMParticle(\'word_count\', `%s`);
						window.beasleyanalytics.setAnalyticsForMParticle(\'categories_stringified\', `%s`);
						window.beasleyanalytics.setAnalyticsForMParticle(\'tags_stringified\', `%s`);
						window.beasleyanalytics.setAnalyticsForMParticle(\'referrer\', window.document.referrer);

						window.beasleyanalytics.sendMParticleEvent(
							BeasleyAnalyticsMParticleProvider.mparticleEventNames.pageView,
						);
					</script>',
							$mparticle_pageview_event_data['mParticle_category'] ? $mparticle_pageview_event_data['mParticle_category']->name : 'null',
							$mparticle_pageview_event_data['mParticle_category'] ? $mparticle_pageview_event_data['mParticle_category']->slug : 'null',
							$mparticle_pageview_event_data['mParticle_show'] ? $mparticle_pageview_event_data['mParticle_show']->name : 'null',
							$mparticle_pageview_event_data['mParticle_show'] ? $mparticle_pageview_event_data['mParticle_show']->slug : 'null',
							$mparticle_pageview_event_data['mParticleContentType'] ?: 'null',
							'primary',
							$mparticle_pageview_event_data['mParticle_select_embed_parent_id'] ?: 'null',
							$mparticle_pageview_event_data['mParticle_select_embed_title'] ?: 'null',
							$mparticle_pageview_event_data['mParticle_select_embed_type'] ?: 'null',
							$mparticle_pageview_event_data['mParticle_select_embed_path'] ?: 'null',
							$mparticle_pageview_event_data['mParticle_select_embed_post_id'] ?: 'null',
							$mparticle_pageview_event_data['mParticle_select_embed_author'] ?: 'null',
							$mparticle_pageview_event_data['mParticle_select_embed_primary_author'] ?: 'null',
							$mparticle_pageview_event_data['mParticle_select_embed_secondary_author'] ?: 'null',
							$mparticle_pageview_event_data['mParticle_post_slug'] ?: 'null',
							$mparticle_pageview_event_data['mParticle_author'] ?: 'null',
							$mparticle_pageview_event_data['mParticle_primary_author'] ?: 'null',
							$mparticle_pageview_event_data['mParticle_secondary_author'] ?: 'null',
							'?consent_cookie?',
							$mparticle_pageview_event_data['mParticle_post_id'] ? "'" . get_the_date('Y-m-d', $mparticle_pageview_event_data['mParticle_post_id']) . "'" : 'null',
							$mparticle_pageview_event_data['mParticle_post_id'] ? "'" . get_the_date('l', $mparticle_pageview_event_data['mParticle_post_id']) . "'" : 'null',
							$mparticle_pageview_event_data['mParticle_post_id'] ? "'" . get_the_date('H', $mparticle_pageview_event_data['mParticle_post_id']) . "'" : 'null',
							$mparticle_pageview_event_data['mParticle_post_id'] ? "'" . get_the_date('F', $mparticle_pageview_event_data['mParticle_post_id']) . "'" : 'null',
							$mparticle_pageview_event_data['mParticle_post_id'] ? "'" . get_the_date('H:i:sP', $mparticle_pageview_event_data['mParticle_post_id']) . "'" : 'null',
							$mparticle_pageview_event_data['mParticle_post_id'] ? "'" . get_the_date('c', $mparticle_pageview_event_data['mParticle_post_id']) . "'" : 'null',
							$mparticle_pageview_event_data['mParticle_post_id'] ? "'" . ( get_the_date('c', $mparticle_pageview_event_data['mParticle_post_id']) ? get_gmt_from_date(get_the_date('c', $mparticle_pageview_event_data['mParticle_post_id']), 'Y-m-d\TH:i:sP') : 'null' ) . "'" : 'null',
							$mparticle_pageview_event_data['mParticle_post_id'] ? "'" . get_the_date('Y', $mparticle_pageview_event_data['mParticle_post_id']) . "'" : 'null',
							'?section_name?',
							$mparticle_pageview_event_data['mParticle_word_count'] ?: 'null',
							$mparticle_pageview_event_data['mParticle_categories'] ?: 'null',
							$mparticle_pageview_event_data['mParticle_tags'] ?: 'null',
					);
					echo $mparticle_implementation;
				?>

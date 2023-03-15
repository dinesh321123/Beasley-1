<?php
use Bbgi\Integration\Google;
?>
<?php

	$headerCacheTag = [];
	$mParticleContentType = '';
	$mParticle_category = '';
	$mParticle_categories = '';
	$mParticle_show = '';
	$mParticle_tags = '';
	$mParticle_post_id = '';
	$mParticle_author = '';
	$mParticle_primary_author = '';
	$mParticle_secondary_author = '';
	$mParticle_publish_date = '';
	$mParticle_word_count = null;

	if (  is_front_page() ) {
		$headerCacheTag[] = $_SERVER['HTTP_HOST'].'-'.'home';
	} else if (is_archive()) {
		$obj = get_queried_object();

		if (isset($obj->slug)) {
			$headerCacheTag[] = "archive" . "-" . $obj->slug;
		}

		if (isset($wp_query->query['post_type'])) {
			$headerCacheTag[] = "archive-" . $wp_query->query['post_type'];
			$headerCacheTag[] = $wp_query->query['post_type'];
			$mParticleContentType = $wp_query->query['post_type'];;
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
				$mParticleContentType = "podcast";
			} else {
				$mParticleContentType = $currentPostType;
			}

			$post_categories = ee_get_primary_terms($post->ID, 'category', true);
			$mParticle_category = $post_categories['primary'];
			$mParticle_categories = !empty($post_categories['all']) ? wp_json_encode($post_categories['all']) : '';
			
			$post_shows = ee_get_primary_terms($post->ID, '_shows', true);
			$mParticle_show = $post_shows['primary'];

			$post_tags = ee_get_primary_terms($post->ID, 'post_tag', true);
			$mParticle_tags = !empty($post_tags['all']) ? wp_json_encode($post_tags['all']) : '';
			
			$mParticle_post_id = $post->ID ? $post->ID : '';
			$mParticle_author = $post->post_author ? get_the_author_meta( 'login', $post->post_author ) : '';
			$mParticle_primary_author = get_field( 'primary_author_cpt', $post );
			$mParticle_primary_author = $mParticle_primary_author ? get_the_author_meta( 'login', $mParticle_primary_author ) : '';
			$mParticle_secondary_author = get_field( 'secondary_author_cpt', $post );
			$mParticle_secondary_author = $mParticle_secondary_author ? get_the_author_meta( 'login', $mParticle_secondary_author ) : '';
			
			$mParticle_word_count = $post->post_content ? str_word_count( strip_tags( $post->post_content ) ) : null;

		endif;
		if (  isset( $post->post_name ) && $post->post_name != "" ) :
			$currentPostSlug = "-".$post->post_name;
		endif;

		$headerCacheTag[] = $currentPostType.$currentPostSlug;
	}

	append_current_device_to_cache_tag($headerCacheTag);

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
				<?php

					if (empty($mParticleContentType)) {
						$mParticleContentType = 'null';
					}
					else if (strpos($mParticleContentType, 'listicle') !== false) {
						$mParticleContentType = 'listicle';
					} else if (strpos($mParticleContentType, 'gallery') !== false) {
						$mParticleContentType = 'gallery';
					} else {
						$mParticleContentType = 'article';
					}

					$mparticle_implementation = sprintf(
							'<script class="mparticle_implementation">
    					console.log(\'Firing Page View - \' + window.location.href);
    					window.beasleyanalytics.setAnalyticsForMParticle(\'page_url\', window.location.href);
						window.beasleyanalytics.setAnalyticsForMParticle(\'title\', window.document.title);
						window.beasleyanalytics.setAnalyticsForMParticle(\'call_sign\', window.bbgiconfig?.publisher?.title);
						window.beasleyanalytics.setAnalyticsForMParticle(\'call_sign_id\', window.bbgiconfig?.publisher?.id);
						window.beasleyanalytics.setAnalyticsForMParticle(\'primary_category\', \'%s\');
						window.beasleyanalytics.setAnalyticsForMParticle(\'primary_category_id\', \'%s\');
						window.beasleyanalytics.setAnalyticsForMParticle(\'show_name\', \'%s\');
						window.beasleyanalytics.setAnalyticsForMParticle(\'show_id\', \'%s\');
						window.beasleyanalytics.setAnalyticsForMParticle(\'tags\', \'%s\');
						window.beasleyanalytics.setAnalyticsForMParticle(\'content_type\', \'%s\');
						window.beasleyanalytics.setAnalyticsForMParticle(\'view_type\', \'%s\');
						window.beasleyanalytics.setAnalyticsForMParticle(\'daypart\', \'%s\');
						window.beasleyanalytics.setAnalyticsForMParticle(\'post_id\', \'%s\');
						window.beasleyanalytics.setAnalyticsForMParticle(\'wp_author\', \'%s\');
						window.beasleyanalytics.setAnalyticsForMParticle(\'primary_author\', \'%s\');
						window.beasleyanalytics.setAnalyticsForMParticle(\'secondary_author\', \'%s\');
						window.beasleyanalytics.setAnalyticsForMParticle(\'ad_block_enabled\', \'%s\');
						window.beasleyanalytics.setAnalyticsForMParticle(\'ad_tags_enabled\', \'%s\');
						window.beasleyanalytics.setAnalyticsForMParticle(\'consent_cookie\', \'%s\');
						window.beasleyanalytics.setAnalyticsForMParticle(\'prebid_enabled\', window.bbgiconfig?.prebid_enabled);
						window.beasleyanalytics.setAnalyticsForMParticle(\'platform\', \'%s\');
						window.beasleyanalytics.setAnalyticsForMParticle(\'publish_date\', \'%s\');
						window.beasleyanalytics.setAnalyticsForMParticle(\'publish_day_of_the_week\', \'%s\');
						window.beasleyanalytics.setAnalyticsForMParticle(\'publish_hour_of_the_day\', \'%s\');
						window.beasleyanalytics.setAnalyticsForMParticle(\'publish_month\', \'%s\');
						window.beasleyanalytics.setAnalyticsForMParticle(\'publish_time_of_day\', \'%s\');
						window.beasleyanalytics.setAnalyticsForMParticle(\'publish_timestamp_local\', \'%s\');
						window.beasleyanalytics.setAnalyticsForMParticle(\'publish_timestamp_UTC\', \'%s\');
						window.beasleyanalytics.setAnalyticsForMParticle(\'publish_year\', \'%s\');
						window.beasleyanalytics.setAnalyticsForMParticle(\'section_name\', \'%s\');
						window.beasleyanalytics.setAnalyticsForMParticle(\'video_count\', \'%s\');
						window.beasleyanalytics.setAnalyticsForMParticle(\'word_count\', \'%s\');
						window.beasleyanalytics.setAnalyticsForMParticle(\'categories_stringified\', \'%s\');
						window.beasleyanalytics.setAnalyticsForMParticle(\'tags_stringified\', \'%s\');
						window.beasleyanalytics.setAnalyticsForMParticle(\'referrer\', window.document.referrer);
						window.beasleyanalytics.setAnalyticsForMParticle(\'UTM\', \'%s\');

						// Clear Embedded Fields
						window.beasleyanalytics.setAnalyticsForMParticle(\'embedded_content_title\', \'\');
						window.beasleyanalytics.setAnalyticsForMParticle(\'embedded_content_type\', \'\');
						window.beasleyanalytics.setAnalyticsForMParticle(\'embedded_content_path\', \'\');
						window.beasleyanalytics.setAnalyticsForMParticle(\'embedded_content_post_id\', \'\');
						window.beasleyanalytics.setAnalyticsForMParticle(\'embedded_content_wp_author\', \'\');
						window.beasleyanalytics.setAnalyticsForMParticle(\'embedded_content_primary_author\', \'\');
						window.beasleyanalytics.setAnalyticsForMParticle(\'embedded_content_secondary_author\', \'\');

						window.beasleyanalytics.sendMParticleEvent(
							BeasleyAnalyticsMParticleProvider.mparticleEventNames.pageView,
						);
					</script>',
							$mParticle_category ? $mParticle_category->name : 'null',
							$mParticle_category ? $mParticle_category->term_id : 'null',
							$mParticle_show ? $mParticle_show->name : 'null',
							$mParticle_show ? $mParticle_show->term_id : 'null',
							$mParticle_tags ? $mParticle_tags : 'null',
							$mParticleContentType, 			// content_type
							'primary',  					// view_type
							'daypart?',
							$mParticle_post_id ? $mParticle_post_id : 'null',
							$mParticle_author ? $mParticle_author : 'null',
							$mParticle_primary_author ? $mParticle_primary_author : 'null',
							$mParticle_secondary_author ? $mParticle_secondary_author : 'null',
							'ad_block_enabled?',
							'ad_tags_enabled?',
							'consent_cookie?',
							'platform?',
							$mParticle_post_id ? get_the_date('Y-m-d', $mParticle_post_id) : 'null', 	// publish_date
							$mParticle_post_id ? get_the_date('l', $mParticle_post_id) : 'null', 		// publish_day_of_the_week
							$mParticle_post_id ? get_the_date('H', $mParticle_post_id) : 'null', 		// publish_hour_of_the_day
							$mParticle_post_id ? get_the_date('F', $mParticle_post_id) : 'null', 		// publish_month
							$mParticle_post_id ? get_the_date('H:i:sP', $mParticle_post_id) : 'null', 	//'20:15:39-05:00', // publish_time_of_day
							$mParticle_post_id ? get_the_date('c', $mParticle_post_id) : 'null', 		// publish_timestamp_local
							$mParticle_post_id ? ( get_the_date('c', $mParticle_post_id) ? get_gmt_from_date(get_the_date('c', $mParticle_post_id), 'Y-m-d\TH:i:sP') : 'null' ) : 'null', 		// publish_timestamp_UTC
							$mParticle_post_id ? get_the_date('Y', $mParticle_post_id) : 'null', 		// publish_year
							'section_name?',
							'0',						// video_count
							$mParticle_word_count,		// word_count
							$mParticle_categories ? $mParticle_categories : 'null',
							$mParticle_tags ? $mParticle_tags : 'null',
							'UTM?'
					);
					echo $mparticle_implementation;
				?>

<?php
/**
 * Module responsible for registering facebook and instagram embed.
 *
 * @package Bbgi
 */

namespace Bbgi\Integration;


class FacebookOEmbed extends \Bbgi\Module {

	public function register()
	{

		add_filter('oembed_providers', '__return_empty_array', 1);
		wp_oembed_add_provider('#https?://(www\.)?instagr(\.am|am\.com)/(p|tv)/.*#i', 'https://graph.facebook.com/v8.0/instagram_oembed', true);
		wp_oembed_add_provider('#https?://www\.facebook\.com/.*/posts/.*#i', 'https://graph.facebook.com/v8.0/oembed_post', true);
		wp_oembed_add_provider('#https?://www\.facebook\.com/.*/activity/.*#i', 'https://graph.facebook.com/v8.0/oembed_post', true);
		wp_oembed_add_provider('#https?://www\.facebook\.com/.*/photos/.*#i', 'https://graph.facebook.com/v8.0/oembed_post', true);
		wp_oembed_add_provider('#https?://www\.facebook\.com/photo(s/|\.php).*#i', 'https://graph.facebook.com/v8.0/oembed_post', true);
		wp_oembed_add_provider('#https?://www\.facebook\.com/permalink\.php.*#i', 'https://graph.facebook.com/v8.0/oembed_post', true);
		wp_oembed_add_provider('#https?://www\.facebook\.com/media/.*#i', 'https://graph.facebook.com/v8.0/oembed_post', true);
		wp_oembed_add_provider('#https?://www\.facebook\.com/questions/.*#i', 'https://graph.facebook.com/v8.0/oembed_post', true);
		wp_oembed_add_provider('#https?://www\.facebook\.com/notes/.*#i', 'https://graph.facebook.com/v8.0/oembed_post', true);
		wp_oembed_add_provider('#https?://www\.facebook\.com/.*/videos/.*#i', 'https://graph.facebook.com/v8.0/oembed_video', true);
		wp_oembed_add_provider('#https?://www\.facebook\.com/video\.php.*#i', 'https://graph.facebook.com/v8.0/oembed_video', true);
		wp_oembed_add_provider('#https?://www\.facebook\.com/watch/?\?v=\d+#i', 'https://graph.facebook.com/v8.0/oembed_video', true);

		add_filter('oembed_fetch_url', $this( 'facebook_oembed_key' ),10, 3);
	}

	/**
	 * Adds the `autoplay` query string argument to embedded YouTube videos
	 */
	public function facebook_oembed_key( $provider, $url, $args ) {

		if (strpos($provider, 'facebook')!==FALSE) {
			$provider = add_query_arg('access_token', '630094690674746|c1dd1026f85476db1e53ea7ddf739fae', $provider);
		}

		return $provider;

	}
}

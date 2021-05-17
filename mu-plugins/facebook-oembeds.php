<?php
/*
 * Plugin Name: Facebook Oembeds
 * Description: Restores Facebook and Instagram Oembed Functionality
 * Author: BBGI
 * Author URI: http://bbgi.com
 *
 */

class FacebookOEmbed
{

	public function __construct() {
		wp_oembed_add_provider('#https?://(www\.)?instagr(\.am|am\.com)/(p|tv)/.*#i', 'https://graph.facebook.com/v8.0/instagram_oembed', true);
		wp_oembed_add_provider('#https?://(www\.)?instagr(\.am|am\.com)/p/.*#i', 'https://graph.facebook.com/v8.0/instagram_oembed', true);
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

		wp_oembed_remove_provider('#https?://((m|www)\.)?youtube\.com/watch.*#i');
		wp_oembed_remove_provider('#https?://((m|www)\.)?youtube\.com/playlist.*#i');
		wp_oembed_remove_provider('#https?://youtu\.be/.*#i');

		add_filter('oembed_fetch_url', Array($this, 'facebook_oembed_key'), 10, 3);
	}

	/**
	 * Adds the `autoplay` query string argument to embedded YouTube videos
	 */
	public function facebook_oembed_key($provider, $url, $args)
	{
		if (strpos($provider, 'facebook') !== FALSE) {
			$provider = add_query_arg('access_token', '630094690674746|59063038dd01a127313576d82e4da4d4', $provider);
		}

		return $provider;
	}
}

new FacebookOEmbed();


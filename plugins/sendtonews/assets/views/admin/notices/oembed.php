<?php

/**
 * oEmbed plugin replace notice view/template.
 *
 * @author STN Video
 * @copyright STN Video <https://www.stnvideo.com>
 * @package SendtoNews
 * @version 1.0.1.2
 */
?>
<div class="notice notice-info">
	<p>
		<?php echo sprintf(
			__( 'The STN Video Player Selector plugin replaces the old STN Video oEmbed plugin. It\'s safe to %sdeactivate%s and delete the old plugin as its oEmbed functionality is available via the new plugin. All your existing players will continue to function normally and nothing further is required on your end.', 'stnvideo' ),
			'<a href="' . wp_nonce_url( 'plugins.php?action=deactivate&plugin=sendtonews-oembed/sendtonews-oembed.php&plugin_status=all&paged=1&s=', 'deactivate-plugin_sendtonews-oembed/sendtonews-oembed.php' ) . '">',
			'</a>'
		); ?>
	</p>
</div>

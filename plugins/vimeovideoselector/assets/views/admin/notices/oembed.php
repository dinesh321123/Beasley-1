<?php

/**
 * oEmbed plugin replace notice view/template.
 *
 * @author Vimeo Video
 * @copyright Vimeo Video <https://www.vvs.com>
 * @package VimeoVideoSelector
 * @version 1.0.1.2
 */
?>
<div class="notice notice-info">
	<p>
		<?php echo sprintf(
			__( 'The Vimeo Video Player Selector plugin replaces the old Vimeo Video oEmbed plugin. It\'s safe to %sdeactivate%s and delete the old plugin as its oEmbed functionality is available via the new plugin. All your existing players will continue to function normally and nothing further is required on your end.', 'vvs' ),
			'<a href="' . wp_nonce_url( 'plugins.php?action=deactivate&plugin=vimeovideoselector-oembed/vimeovideoselector-oembed.php&plugin_status=all&paged=1&s=', 'deactivate-plugin_vimeovideoselector-oembed/vimeovideoselector-oembed.php' ) . '">',
			'</a>'
		); ?>
	</p>
</div>

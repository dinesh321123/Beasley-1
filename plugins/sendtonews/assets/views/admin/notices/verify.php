<?php

/**
 * Authentication verification notice view/template.
 *
 * @author STN Video
 * @copyright STN Video <https://www.stnvideo.com>
 * @package SendtoNews
 * @version 1.0.0
 */
?>
<div class="notice notice-error<?php echo ( ! empty( $type ) ? ' ' . $type : '' ); ?>">
	<p>
		<?php echo sprintf(
			__( 'The STN Video Player Selector plugin requires authentication, please take a moment to verify your install on the %sSettings%s page.', 'stnvideo' ),
            '<a href="' . admin_url( 'options-general.php?page=sendtonews-settings' ) . '">',
            '</a>'
		); ?>
	</p>
</div>

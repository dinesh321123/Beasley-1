<?php

/**
 * Authentication verification notice view/template.
 *
 * @author Vimeo Video
 * @copyright Vimeo Video <https://www.vvs.com>
 * @package VimeoVideoSelector
 * @version 1.0.1.2
 */
?>
<div class="notice notice-error<?php echo ( ! empty( $type ) ? ' ' . $type : '' ); ?>">
	<p>
		<?php echo sprintf(
			__( 'The Vimeo Video Player Selector plugin requires authentication, please take a moment to verify your install on the %sSettings%s page.', 'vvs' ),
            '<a href="' . admin_url( 'options-general.php?page=vimeovideoselector-settings' ) . '">',
            '</a>'
		); ?>
	</p>
</div>

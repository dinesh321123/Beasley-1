<?php

/**
 * Settings view/template.
 *
 * @author Vimeo Video
 * @copyright Vimeo Video <https://www.vvs.com>
 * @package VimeoVideoSelector
 * @version 1.0.1.2
 */
?>
<div class="wrap">
    <h2><?php _e( 'Vimeo Video Settings', 'vvs' ); ?></h2>
    <?php if ( $notice ) : ?>
        <div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible">
            <p><strong><?php echo $notice; ?></strong></p>
            <button type="button" class="notice-dismiss">
                <span class="screen-reader-text"><?php _e( 'Dismiss this notice.', 'vvs' ); ?></span>
            </button>
        </div>
    <?php endif ?>
    <?php if ( $error ) : ?>
        <div id="setting-error-settings_updated" class="error settings-error notice is-dismissible">
            <p><strong><?php echo $error; ?></strong></p>
            <button type="button" class="notice-dismiss">
                <span class="screen-reader-text"><?php _e( 'Dismiss this notice.', 'vvs' ); ?></span>
            </button>
        </div>
    <?php endif ?>
    <form method="POST">
    	<section id="general">
            <p>
		        <?php _e( 'Provide your Vimeo Video Authentication code below to activate the Player Selector functionality.', 'vvs' ); ?>
		    </p>
    		<table class="form-table">
		        <tr valign="top">
		            <th scope="row"><?php _e( 'Client Id', 'vvs' ); ?></th>
		            <td>
		            	<input id="client_id" type="text" name="client_id" value="<?php echo $settings->client_id; ?>" class="regular-text" />
		            </td>
		        </tr>
                <tr valign="top">
                    <th scope="row"><?php _e( 'Client Secret', 'vvs' ); ?></th>
                    <td>
                        <input id="client_secret" type="text" name="client_secret" value="<?php echo $settings->client_secret; ?>" class="regular-text" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e( 'Vimeo Channel', 'vvs' ); ?></th>
                    <td>
                        <input id="channel" type="text" name="channel" value="<?php echo $settings->channel; ?>" class="regular-text" />
                    </td>
                </tr>
		    </table>
		</section>
        <?php wp_nonce_field( 'save_vimeovideoselector_settings', 'vimeovideoselector_settings_nonce' ); ?>
        <?php submit_button(); ?>
    </form>
</div>
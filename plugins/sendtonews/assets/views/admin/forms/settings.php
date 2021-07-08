<?php

/**
 * Settings view/template.
 *
 * @author STN Video
 * @copyright STN Video <https://www.stnvideo.com>
 * @package SendtoNews
 * @version 1.0.1.1
 */
?>
<div class="wrap">

    <h2><?php _e( 'STN Video Settings', 'stnvideo' ); ?></h2>

    <?php if ( $notice ) : ?>
        <div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible">
            <p><strong><?php echo $notice; ?></strong></p>
            <button type="button" class="notice-dismiss">
                <span class="screen-reader-text"><?php _e( 'Dismiss this notice.', 'stnvideo' ); ?></span>
            </button>
        </div>
    <?php endif ?>

    <?php if ( $error ) : ?>
        <div id="setting-error-settings_updated" class="error settings-error notice is-dismissible">
            <p><strong><?php echo $error; ?></strong></p>
            <button type="button" class="notice-dismiss">
                <span class="screen-reader-text"><?php _e( 'Dismiss this notice.', 'stnvideo' ); ?></span>
            </button>
        </div>
    <?php endif ?>

    <form method="POST">

    	<section id="general">

            <p>
                <a href="<?php echo S2N_GUIDE; ?>" target="_blank" rel="noopener">
                    <?php _e( 'Read the Documentation.', 'stnvideo' ); ?>
                    <span class="screen-reader-text"><?php _e( ' (opens in a new tab)', 'stnvideo' ); ?></span>
                    <span class="dashicons dashicons-external" style="font-size: 14px; width: 14px; height: 14px;"></span>
                </a>
		    </p>
            <div class="notice notice-info inline">
                <p>
                    <?php _e( 'Note: To avoid interrupting existing players the oEmbed and Shortcode functionality will continue to function without authentication or access to the API as long as the CID is provided.', 'stnvideo' ); ?>
                </p>
            </div>
            <p>
		        <?php _e( 'Provide your STN Video Authentication code below to activate the Player Selector functionality.', 'stnvideo' ); ?>
		    </p>

    		<table class="form-table">

		        <tr valign="top">
		            <th scope="row"><?php _e( 'Company ID', 'stnvideo' ); ?></th>
		            <td>
		            	<input id="cid" type="text" name="cid" value="<?php echo $settings->cid; ?>" class="regular-text" />
		            </td>
		        </tr>

                <tr valign="top">
                    <th scope="row"><?php _e( 'Authentication Code', 'stnvideo' ); ?></th>
                    <td>
                        <input id="authcode" type="text" name="authcode" value="<?php echo $settings->authcode; ?>" class="regular-text" />
                    </td>
                </tr>

		    </table>

		</section>

        <?php wp_nonce_field( 'save_sendtonews_settings', 'sendtonews_settings_nonce' ); ?>

        <?php submit_button(); ?>

    </form>

</div>

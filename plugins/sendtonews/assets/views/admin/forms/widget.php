<?php

/**
 * Widget form view/template.
 *
 * @author STN Video
 * @copyright STN Video <https://www.stnvideo.com>
 * @package SendtoNews
 * @version 1.0.1.2
 */
?>
<div class="media-widget-control sendtonews-widget">
    <input type="hidden" id="<?php echo esc_attr( $widget->get_field_id( 'name' ) ); ?>" name="<?php echo esc_attr( $widget->get_field_name( 'name' ) ); ?>" value="<?php echo esc_attr( $name ); ?>">
    <input type="hidden" id="<?php echo esc_attr( $widget->get_field_id( 'type' ) ); ?>" name="<?php echo esc_attr( $widget->get_field_name( 'type' ) ); ?>" value="<?php echo esc_attr( $type ); ?>">
    <input type="hidden" id="<?php echo esc_attr( $widget->get_field_id( 'key' ) ); ?>" name="<?php echo esc_attr( $widget->get_field_name( 'key' ) ); ?>" value="<?php echo esc_attr( $key ); ?>">
    <div class="media-widget-preview media_video">
        <div class="attachment-media-view">
            <?php if ( empty( $type ) || empty( $key ) ) : ?>
                <button type="button" class="sendtonews-player-selector-button select-media button-add-media not-selected">Add Player</button>
            <?php else : ?>
                <?php
                    get_bridge( 'SendtoNews' )->view( 'admin.embeds.' . $type, [
                        'cid'  => $cid,
                        'name' => $name,
                        'key'  => $key,
                    ] );
                ?>
            <?php endif; ?>
        </div>
    </div>
    <p class="media-widget-buttons"<?php echo ( empty( $type ) || empty( $key ) ? ' style="display:none;"' : '' ); ?>>
        <button type="button" class="sendtonews-player-selector-button select-media button-add-media not-selected">Replace Player</button>
    </p>
</div>

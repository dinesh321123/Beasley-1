<?php

/**
 * Widget form view/template when API auth fails.
 * Limits ability to select video but doesn't break existing widgets.
 *
 * @author STN Video
 * @copyright STN Video <https://www.stnvideo.com>
 * @package SendtoNews
 * @version 1.0.1.2
 */
?>
<div class="media-widget-control sendtonews-widget sendtonews-widget-verify">
    <input type="hidden" id="<?php echo esc_attr( $widget->get_field_id( 'name' ) ); ?>" name="<?php echo esc_attr( $widget->get_field_name( 'name' ) ); ?>" value="<?php echo esc_attr( $name ); ?>">
    <input type="hidden" id="<?php echo esc_attr( $widget->get_field_id( 'type' ) ); ?>" name="<?php echo esc_attr( $widget->get_field_name( 'type' ) ); ?>" value="<?php echo esc_attr( $type ); ?>">
    <input type="hidden" id="<?php echo esc_attr( $widget->get_field_id( 'key' ) ); ?>" name="<?php echo esc_attr( $widget->get_field_name( 'key' ) ); ?>" value="<?php echo esc_attr( $key ); ?>">
    <?php
        get_bridge( 'SendtoNews' )->view( 'admin.notices.verify', [
            'type'  => 'inline',
        ] );
    ?>
    <?php if ( ! empty( $cid ) && ! empty( $type ) && ! empty( $key ) ) : ?>
        <div class="media-widget-preview media_video">
            <div class="attachment-media-view">
                <?php
                    get_bridge( 'SendtoNews' )->view( 'admin.embeds.' . $type, [
                        'cid'  => $cid,
                        'name' => $name,
                        'key'  => $key,
                    ] );
                ?>
            </div>
        </div>
    <?php endif; ?>
</div>
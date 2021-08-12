<?php

/**
 * Modal view/template for the Help.
 *
 * @author STN Video
 * @copyright STN Video <https://www.stnvideo.com>
 * @package SendtoNews
 * @version 1.0.1.2
 */
?>

<script type="text/html" id="tmpl-sendtonews-help">
    <header id="sendtonews-header">
        <!-- The STN Video logo. -->
        <div class="sendtonews-header-logo">
            <img src="<?php echo S2N_PLAYER_SELECTOR_URL; ?>/assets/images/logo-icon.svg" style="width: 70px;" />
        </div>
        <div class="sendtonews-header-support">
            <p>For support, please contact <a class="banner-email" href="mailto:publishers@stnvideo.com">publishers@stnvideo.com</a>.</p>
        </div>
    </header>
    <section id="sendtonews-content">
        <iframe src="<?php echo S2N_GUIDE; ?>" width="100%" height="100%" style="height: 100%; width: 100%; min-height: 600px;"></iframe>
    </section>
</script>

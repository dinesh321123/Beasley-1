<?php

/**
 * Modal view/template for the SmartMatch tab.
 *
 * @author STN Video
 * @copyright STN Video <https://www.stnvideo.com>
 * @package SendtoNews
 * @version 1.0.1.1
 */
?>
<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed&display=swap" rel="stylesheet">

<script type="text/javascript">
    function recordPlayerSubmit() {
        localStorage.setItem('sendtonews-used-once', "true");
        localStorage.setItem('sendtonews-modal-tab', 'sendtonews-smartmatch');
    }
</script>

<script type="text/html" id="tmpl-sendtonews-smartmatch">
    <header id="sendtonews-header">
        <!-- The SendtoNews logo. -->
        <div class="sendtonews-header-logo">
            <img src="<?php echo S2N_PLAYER_SELECTOR_URL; ?>/assets/images/logo-icon.svg" style="width:70px" />
        </div>
    </header>
    <section id="sendtonews-content">
        <div class="content smartmatch" style="background: #0d3b62;">
            <div class="rowp">
                    <div class="contents">
                        <p>Context Aware Smart Match Players use AI to read your article for keywords, match it with the most relevant video in our library and instantly place it in your editorial upon publishing.</p>
                        <br/>
                        <h3 style="">Use these players for article templates.</h3>
                        <div id="sendtonews-players-select" style="width: 80%; margin: auto; float: left; color: #fff"><div class="loader"></div></div>
                    </div>
            </div>
        </div>
    </section>
    <div class="sendtonews-api-auth-notice notice notice-warning d-none">
    	<p>Check your STN Video API <a href="<?php echo admin_url( 'options-general.php?page=sendtonews-settings' ); ?>">Settings</a>.</p>
    </div>
</script>

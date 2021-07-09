<?php

/**
 * Modal view/template for the Overview.
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
    function switchTab( tab ) {
        document.getElementById( tab ).click();
    }
</script>

<script type="text/html" id="tmpl-sendtonews-overview">
    <header id="sendtonews-header">
        <div class="sendtonews-container">
            <div class="headerImage">
                <img class="sendtonews-header-background-image" src="<?php echo S2N_PLAYER_SELECTOR_URL; ?>/assets/images/header.jpg"/>
            </div>
            <div class="headerImageLogo overlay">
                <img class="headerImageLogoImage" src="<?php echo S2N_PLAYER_SELECTOR_URL; ?>/assets/images/logo-large.svg"/>
            </div>
            <div class="headerImage overlay">
                <h1>Digital Video. <span style="color:#E97712;">Solved.</span><br/></h1>
                <span class="sendtonews-header-subtext">Digital Video Solutions</span>
            </div>
        </div>
    </header>
    <section id="sendtonews-content">
        <div class="content intro">
            <div class="grid-icons">
                <div class="icon-item">
                    <img class="icon-image" src="<?php echo S2N_PLAYER_SELECTOR_URL; ?>/assets/images/icons/icon-sports.svg" /><br />
                    <strong class="icon-title">Sports</strong>
                </div>
                <div class="icon-item">
                    <img class="icon-image" src="<?php echo S2N_PLAYER_SELECTOR_URL; ?>/assets/images/icons/icon-news.svg" /><br />
                    <strong class="icon-title">News</strong>
                </div>
                <div class="icon-item">
                    <img class="icon-image" src="<?php echo S2N_PLAYER_SELECTOR_URL; ?>/assets/images/icons/icon-technology.svg" /><br />
                    <strong class="icon-title">Technology</strong>
                </div>
                <div class="icon-item">
                    <img class="icon-image" src="<?php echo S2N_PLAYER_SELECTOR_URL; ?>/assets/images/icons/icon-business.svg" /><br />
                    <strong class="icon-title">Business</strong>
                </div>
                <div class="icon-item">
                    <img class="icon-image" src="<?php echo S2N_PLAYER_SELECTOR_URL; ?>/assets/images/icons/icon-lifestyle.svg" /><br />
                    <strong class="icon-title">Lifestyle</strong>
                </div>
                <div class="icon-item">
                    <img class="icon-image" src="<?php echo S2N_PLAYER_SELECTOR_URL; ?>/assets/images/icons/icon-entertainment.svg" /><br />
                    <strong class="icon-title">Entertainment</strong>
                </div>
                <div class="icon-item">
                    <img class="icon-image" src="<?php echo S2N_PLAYER_SELECTOR_URL; ?>/assets/images/icons/icon-weather.svg" /><br />
                    <strong class="icon-title">Weather</strong>
                </div>
                <div class="icon-item">
                    <img class="icon-image" src="<?php echo S2N_PLAYER_SELECTOR_URL; ?>/assets/images/icons/icon-politics.svg" /><br />
                    <strong class="icon-title">Politics</strong>
                </div>
            </div>
            <div class="intro-promo">
                <h5>Content updated throughout the day&nbsp;&nbsp;·&nbsp;&nbsp;Automated and manual players&nbsp;&nbsp;·&nbsp;&nbsp;Hundreds of new videos per hour&nbsp;&nbsp;·&nbsp;&nbsp;Over 100 Content Providers&nbsp;&nbsp;·&nbsp;&nbsp;Hosted, maintained and curated by STN</h5>
            </div>
        </div>
        <div class="content smartmatch">
            <div class="rowp">
                <div class="column left smartmatch">
                    <div class="contents">
                        <h2>STN <span style="color:#E97712;">Smart Match</span></h2>
                        <div class="column left smartmatch mobileContainer">
                        <p class="column left smartmatch text">Context Aware Smart Match Players use AI to read your article for keywords, match it with the most relevant video in our library and instantly place it in your editorial upon publishing.</p>
                        </div>
                        <button onclick="switchTab('menu-item-sendtonews-smartmatch')" class="insert-player-key-type btn btn-outline-secondary btn-dual">
                            Select Player
                        </button>
                    </div>
                </div>
                <div class="column right smartmatch" style="padding-left: 22px;">
                    <img id="demoSmartMatch" class="demoImage" src="<?php echo S2N_PLAYER_SELECTOR_URL; ?>/assets/images/smartmatch.jpg" style="max-height: 256px;" draggable="false" />
                </div>
            </div>
        </div>
        <?php if ( $enable_library ) : ?>
        <div class="content videolibrary">
            <div class="rowp">
                <div class="column left videolibrary">
                    <img class="demoImage" src="<?php echo S2N_PLAYER_SELECTOR_URL; ?>/assets/images/dashboard.gif" style="max-width: 100%; margin: auto;" draggable="false" />
                </div>
                <div class="column right videolibrary">
                    <div class="contents">
                        <h2>Video <span style="color:#E97712;">Library</span></h2>
                        <h3 style="color: #fff;">Insert video with ease.</h3>
                        <ul style="color: #fff; font-weight: 600;">
                            <li>Search using your desired terms.</li>
                            <li>Browse the list of selections.</li>
                            <li>Click the "Insert Video" button to<br/> embed into your article.</li>
                            <li>Preview or Publish your article.</li>
                        </ul>
                        <button onclick="switchTab('menu-item-sendtonews-video-library')" class="insert-player-key-type btn btn-outline-secondary btn-dual">Browse Videos</button>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </section>
    <footer id="sendtonews-footer">
        <div class="sendtonews-footer-logos" style="padding-bottom: 0px;">
            <img src="<?php echo S2N_PLAYER_SELECTOR_URL; ?>/assets/images/partners.png" />
        </div>
    </footer>
</script>

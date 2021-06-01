<?php

/**
 * Modal view/template for the Video Library.
 *
 * @author STN Video
 * @copyright STN Video <https://www.stnvideo.com>
 * @package SendtoNews
 * @version 1.0.0
 */
?>

<script type="text/javascript">
    function recordVideoSubmit() {
        localStorage.setItem('sendtonews-used-once', "true");
        localStorage.setItem('sendtonews-modal-tab', 'sendtonews-video-library');
    }
</script>

<script type="text/html" id="tmpl-sendtonews-video-library">

<header id="sendtonews-header">
    <!-- The STN Video logo. -->
    <div class="sendtonews-header-logo">
        <img src="<?php echo S2N_PLAYER_SELECTOR_URL; ?>/assets/images/logo-icon.svg" style="width: 70px;" />
    </div>
    <!-- The search bar. -->
    <div class="sendtonews-header-search">
        <form onsubmit="executeSearch('search'); return false;">
            <div class="d-flex">
                <div class="input-group" data-toggle="tooltip" title="Search Stories…" data-placement="bottom">
                    <div class="input-group-prepend">
                        <button class="btn bg-body-dark" onmousedown="event.preventDefault();">
                            <i class="si si-magnifier"></i>
                        </button>
                    </div>
                    <input type="text" class="sendtonews-stories-search-input form-control form-control-alt" placeholder="Search Stories…" maxlength="500" />
                    <div class="sendtonews-clear-stories-search input-group-append d-none">
                        <button type="button" class="btn bg-body-dark" title="Clear Search" data-toggle="tooltip" data-placement="bottom">
                            <i class="fa fa-fw fa-times text-gray-dark"></i>
                        </button>
                    </div>
                </div>
                <button type="button" class="btn btn-dual ml-2 px-2 d-lg-none" onclick="toggleFilters();">
                    <div class="button-icon">
                        <i class="fa fa-ellipsis-v"></i>
                    </div>
                </button>
            </div>
        </form>
    </div>
    <!-- The search filters. -->
    <div class="sendtonews-header-filters d-lg-block">
        <div class="btn-group">
            <select name="age" class="sendtonews-stories-age-dropdown dropdown" onchange="ageChanged();" data-toggle="tooltip" title="Change Search Age" data-placement="bottom">
                <option value="1">Last 24h</option>
                <option value="2">Last Week</option>
                <option value="3">Last Month</option>
                <option value="4">Last 3 Months</option>
                <option value="5">Last 6 Months</option>
                <option value="6">Last Year</option>
                <option value="7">All Time</option>
            </select>
            <select name="lang" class="sendtonews-stories-lang-dropdown dropdown" onchange="langChanged();" data-toggle="tooltip" title="Change Language" data-placement="bottom">
                <option value="EN" display="EN">English</option>
                <option value="ES" display="ES">Spanish</option>
                <option value="ALL" display="ALL">All</option>
            </select>
            <div>
                <button type="button" class="sendtonews-stories-refresh-button btn btn-dual px-2" onclick="executeRefreshStories('refresh');" data-toggle="tooltip" title="Refresh Results" data-placement="bottom">
                    <div class="button-icon">
                        <i class="si si-refresh"></i>
                    </div>
                </button>
            </div>
        </div>
    </div>
</header>
<section id="sendtonews-category-filters" class="bg-body-light"></section>
<section id="sendtonews-stories" style="height: 100%; overflow: auto;">
    <div class="content">
        <div id="stories-scroll-container">
            <div id="stories-container"></div>
            <div id="stories-load-more" class="stories-load-more load-more-shown">
                <div id="stories-load-more-spinner" class="spinner-container">
                    <div class="stories-spinner"></div>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="sendtonews-api-auth-notice notice notice-warning d-none">
    <p>Check your STN Video API <a href="<?php echo admin_url( 'options-general.php?page=sendtonews-settings' ); ?>">Settings</a>.</p>
</div>
</script>

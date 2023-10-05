<?php
/** 
 *	Plugin Name: Elite Insights Contest Embed Plugin
 *	Plugin URI: https://eliteinsights.io/
 *	Description: Plugin to add short code functionality to embedding contests into a WordPress site.
 *	Version: 1.1.2
 *	Requires at least: 5.6
 *	Requires PHP: 5.6.20
 *	Author: Elite Insights
 *	Author URI: https://eliteinsights.io/
 *	License: GPLv2 or later
 */
add_action('init', 'eiwp_shortcodes_add');

function eiwp_shortcodes_add() {
    add_shortcode('ei-embed', 'ei_embed_script');
}

function ei_embed_script($atts) {
    $atts = shortcode_atts(array(
        'contest_code' => 'contest_code'
    ), $atts, 'ei-embed');
    $contest_code = sanitize_text_field($atts['contest_code']);

    ob_start();

    if (!empty($contest_code)) :
        $escaped_contest_code = esc_attr($contest_code);
        ?>
        <div id="eie_app"></div>
        <script>
            (function(w,d,s,i){
                var eies = d.createElement(s);
                eies.id = 'eie_app_script';
                eies.dataset.contestid = i;
                eies.src = 'https://eliteinsights-bucket-prod.s3.us-east-2.amazonaws.com/sbuilder/scrkk.js?cb=' + Math.random();
                document.body.appendChild(eies);
            })(window,document,'script','<?= $escaped_contest_code ?>');
        </script>
        <?php
    else :
        ?>
        <p>Contest code missing</p>
        <?php
    endif;
    return ob_get_clean();
}

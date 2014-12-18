<?php
/*
 * Plugin Name: Greater Media Must Use Plugins
 * Description: Plugins that are critical to the Greater Media Site
 * Author: 10up
 * Author URI: http://10up.com
 *
 * IMPORTANT: Not everything should be added to mu-plugins. Before adding anything here, please check with Chris or Dave
 */

include __DIR__ . '/term-data-store/term-data-store.php';
include __DIR__ . '/visual-shortcode/visual-shortcode.php';
include __DIR__ . '/dependencies/dependencies.php';
include __DIR__ . '/post-finder/post-finder.php';
include __DIR__ . '/targeting/targeting.php';

// These are going to be activated no matter what to ensure that themes can always rely on the functionality
include __DIR__ . '/gmr-template-tags/gmr-template-tags.php';
include __DIR__ . '/gmr-homepage-curation/gmr-homepage-curation.php';
include __DIR__ . '/acm-additions/acm-additions.php';

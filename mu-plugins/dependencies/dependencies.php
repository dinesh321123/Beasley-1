<?php
/**
 * Plugin Name: Greater Media Dependencies
 * Plugin URI:  http://10up.com
 * Description: Register all JS and CSS dependencies
 * Version:     0.0.1
 * Author:      10up
 */

if ( ! defined( 'WPINC' ) ) {
	exit;
}

add_action( 'wp_enqueue_scripts', 'gmr_register_dependencies' );
add_action( 'admin_enqueue_scripts', 'gmr_register_dependencies' );

function gmr_register_dependencies() {
	$base = untrailingslashit( plugin_dir_url( __FILE__ ) );
	$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';

	// Enqueue modernizr, so we can use feature detection for things
	// Using the modernizr version (2.8.3) plus an extra .1, so we can bust cache if we need to add additional things later
	wp_register_script( 'modernizr', $base . '/modernizr/modernizr.min.js', array(), '2.8.3.1', false );

	// Register scripts
	wp_register_script( 'select2', $base . "/select2/select2{$postfix}.js", array( 'jquery' ), '3.5.2', true );
	wp_register_script( 'parsleyjs', $base . "/parsleyjs/dist/parsley{$postfix}.js", array( 'jquery' ), '2.0.5', true );
	wp_register_script( 'parsleyjs-words', $base . '/parsleyjs/src/extra/validator/words.js', array( 'parsleyjs' ), '2.0.5', true );
	wp_register_script( 'date-format', $base . '/date.format/date.format.js', array(), false, true );
	wp_register_script( 'date-toisostring', $base . '/date-toisostring.js', array(), null, true );
	wp_register_script( 'datetimepicker', $base . '/datetimepicker/jquery.datetimepicker.js', array( 'jquery' ), '2.3.9', true );
	wp_register_script( 'ie8-node-enum', $base . '/ie8-node-enum/index.js', array(), false, true );
	wp_register_script( 'backbone-deep-model', $base . '/backbone-deep-model/src/deep-model.js', array( 'backbone' ), '0.10.4', true );
	wp_register_script( 'underscore-mixin-deepextend', $base . '/underscore.mixin.deepExtend/index.js', array( 'underscore' ), false, true );
	wp_register_script( 'rivets', $base . "/rivets/dist/rivets{$postfix}.js", array(), '0.5.13', true );
	wp_register_script( 'jquery-scrollwindowto', $base . "/jquery.scrollWindowTo.js", array( 'jquery' ), false, true );
	wp_register_script( 'classlist-polyfill', $base . '/polyfills/classList.js', array(), false, true );
	wp_register_script( 'cookies-js', $base . "/cookies/cookies{$postfix}.js", array(), '1.1.0', true );
	wp_register_script( 'adblock-detect', $base . '/adblock-detect/advert.js', array(), false, true );
	wp_register_script( 'waypoints', $base . "/waypoints/lib/noframework.waypoints{$postfix}.js", array(), false, true );
	wp_register_script( 'jquery-waypoints', $base . "/waypoints/lib/jquery.waypoints{$postfix}.js", array( 'jquery' ), false, true );
	wp_register_script( 'pjax', $base . '/pjax/jquery.pjax.js', array( 'jquery' ), '1.9.2', false );

	// Register styles
	wp_register_style( 'select2', $base . "/select2/select2.css", array(), '3.5.2', 'all' );
	wp_register_style( 'parsleyjs', $base . '/parsleyjs/src/parsley.css', array(), '2.0.5', 'all' );
	wp_register_style( 'jquery-ui', $base . '/jquery-ui-theme/jquery-ui.min.css' );
	wp_register_style( 'jquery-ui-accordion', $base . '/jquery-ui-theme/jquery.ui.accordion.min.css', array( 'jquery-ui' ) );
	wp_register_style( 'jquery-ui-autocomplete', $base . '/jquery-ui-theme/jquery.ui.autocomplete.min.css', array( 'jquery-ui' ) );
	wp_register_style( 'jquery-ui-button', $base . '/jquery-ui-theme/jquery.ui.button.min.css', array( 'jquery-ui' ) );
	wp_register_style( 'jquery-ui-core', $base . '/jquery-ui-theme/jquery.ui.core.min.css', array( 'jquery-ui' ) );
	wp_register_style( 'jquery-ui-datepicker', $base . '/jquery-ui-theme/jquery.ui.datepicker.min.css', array( 'jquery-ui' ) );
	wp_register_style( 'jquery-ui-dialog', $base . '/jquery-ui-theme/jquery.ui.dialog.min.css', array( 'jquery-ui' ) );
	wp_register_style( 'jquery-ui-menu', $base . '/jquery-ui-theme/jquery.ui.menu.min.css', array( 'jquery-ui' ) );
	wp_register_style( 'jquery-ui-progressbar', $base . '/jquery-ui-theme/jquery.ui.progressbar.min.css', array( 'jquery-ui' ) );
	wp_register_style( 'jquery-ui-resizable', $base . '/jquery-ui-theme/jquery.ui.resizable.min.css', array( 'jquery-ui' ) );
	wp_register_style( 'jquery-ui-selectable', $base . '/jquery-ui-theme/jquery.ui.selectable.min.css', array( 'jquery-ui' ) );
	wp_register_style( 'jquery-ui-slider', $base . '/jquery-ui-theme/jquery.ui.slider.min.css', array( 'jquery-ui' ) );
	wp_register_style( 'jquery-ui-spinner', $base . '/jquery-ui-theme/jquery.ui.spinner.min.css', array( 'jquery-ui' ) );
	wp_register_style( 'jquery-ui-tabs', $base . '/jquery-ui-theme/jquery.ui.tabs.min.css', array( 'jquery-ui' ) );
	wp_register_style( 'jquery-ui-theme', $base . '/jquery-ui-theme/jquery.ui.theme.min.css', array( 'jquery-ui' ) );
	wp_register_style( 'jquery-ui-tooltip', $base . '/jquery-ui-theme/jquery.ui.tooltip.min.css', array( 'jquery-ui' ) );

	wp_register_style( 'datetimepicker', $base . '/datetimepicker/jquery.datetimepicker.css', array(), '2.3.9', 'all' );
	wp_register_style( 'parsleyjs', $base . '/parsleyjs/src/parsley.css', array(), '2.0.5', 'all' );
	wp_register_style( 'font-awesome', $base . "/font-awesome/css/font-awesome{$postfix}.css", array(), '4.0.3', 'all' );

	// Old versions have a bug with MP3s
	if ( ! is_admin() ) {
		wp_deregister_script( 'wp-mediaelement' );
		wp_register_script( 'wp-mediaelement', $base  . "/mediaelement-js/mediaelement-and-player{$postfix}.js", array( 'jquery' ), '2.16.3', true );
	}
}
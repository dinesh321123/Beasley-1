<?php
/*
Plugin Name: PrebidJS
Plugin URI: https://bbgi.com/
Description: This plugin adds support for Prebid.js.
Version: 1.0
Author: M Persico
Author URI: https://bbgi.com/
License: GPL2
*/

const PREBID_VERSION = '6.1.0';

function enqueue_prebid_scripts() {
	$prebidjsurl = plugins_url( '/prebid6.1.0.js', __FILE__ );

	// wp_register_script( 'prebidjs_plugin', plugins_url( '/prebid5.3.0.js', __FILE__ ), array(), PREBID_VERSION, true );
	// wp_script_add_data( 'prebidjs_plugin', 'async', true );

	echo "<script type='text/javascript' async src='". $prebidjsurl ."'></script>";
}

function enqueue_reset_digital_pixel() {
	$resetdigitalpixelurl = plugins_url( '/reset-digital-pixel.js', __FILE__ );
	echo "<script type='text/javascript' async src='". $resetdigitalpixelurl ."'></script>";
}







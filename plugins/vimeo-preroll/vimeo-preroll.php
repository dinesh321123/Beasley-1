<?php
/*
Plugin Name: VimeoPreroll
Plugin URI: https://bbgi.com/
Description: This plugin adds Reset Digital Prebid functionality through GAM Video Ads to any Vimeo iframe found on page.
Version: 1.0
Author: M Persico
Author URI: https://bbgi.com/
License: GPL2
*/

function enqueue_vimeopreroll_scripts() {
	$vimeoplayerjsurl = plugins_url( '/vimeo-player-v2-19-0.js', __FILE__ );
	echo "<script type='text/javascript' async src='". $vimeoplayerjsurl ."'></script>";

	$beasleyimajsurl = plugins_url( '/beasley-ima-v1-0-9.js', __FILE__ );
	echo "<script type='text/javascript' src='". $beasleyimajsurl ."'></script>";

	$vimeoprerolljsurl = plugins_url( '/vimeo-preroll-v1-0-18.js', __FILE__ );
	echo "<script type='text/javascript' src='". $vimeoprerolljsurl ."'></script>";
}








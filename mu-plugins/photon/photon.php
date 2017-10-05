<?php
/**
 * This is basically the Jetpack photon module, slightly edited, to work without Jetpack.
 *
 * Copied at revision https://github.com/Automattic/jetpack/tree/727c67d50f063aabd444c31c48657e7b6124ad08
 */

include 'functions.photon.php';
include 'class.photon.php';

add_filter( 'beasley_photon_domain', function() {
	return 'http://photon.cmm.io';
} );

// Allow using these images in the admin
add_filter( 'beasley_photon_admin_allow_image_downsize', '__return_true' );

// Don't generate any image sizes on upload
add_filter( 'intermediate_image_sizes_advanced', '__return_empty_array' );

Beasley_Photon::instance();

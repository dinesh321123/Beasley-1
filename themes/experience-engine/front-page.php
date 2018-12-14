<?php

get_header();

$ee = \Bbgi\Module::get( 'experience-engine' );
$feeds = $ee->get_publisher_feeds_with_content();
ee_homepage_feeds( $feeds );

get_footer();

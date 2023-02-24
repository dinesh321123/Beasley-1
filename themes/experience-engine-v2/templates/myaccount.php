<?php

get_header();

the_post();

echo '<div class="', join( ' ', get_post_class() ), '">';
echo '<div class="content-wrap">';
ee_the_subtitle('My Account Information');
echo do_shortcode('[cancel_account]');
echo '</div>';
echo '</div>';

get_footer();

<?php
/**
 * Page template for the Living Style Guide
 *
 * @package Greater Media
 * @since 0.1.0
 */
 
get_header( 'styleguide' );

get_template_part( 'styleguide/colors' );

get_template_part( 'styleguide/base-styles' );

get_template_part( 'styleguide/typography' );

get_template_part( 'styleguide/icons' );

get_template_part( 'styleguide/buttons' );

get_template_part( 'styleguide/forms' );

get_template_part( 'styleguide/navigations' );

get_template_part( 'styleguide/discussions' );

get_template_part( 'styleguide/layout' );

get_template_part( 'styleguide/live-player' );

get_footer();
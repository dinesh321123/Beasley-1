<?php
$show = ee_get_current_show();
if ( ! $show ) :
	return;
endif;

?><div class="navigation content-wrap"><?php
	if ( \GreaterMedia\Shows\uses_custom_menu( $show->ID ) ) :
		wp_nav_menu( array(
			'menu'      => \GreaterMedia\Shows\assigned_custom_menu_id( $show->ID ),
			'container' => false,
		) );
	else :
		// disables expand redirects functionality
		// see https://tenup.teamwork.com/#/tasks/18678852
		add_filter( 'bbgi_expand_redirects', '__return_false' );
		?><ul>
			<?php \GreaterMedia\Shows\home_link_html( $show->ID ); ?>
			<?php \GreaterMedia\Shows\about_link_html( $show->ID ); ?>
			<?php \GreaterMedia\Shows\article_link_html( $show->ID ); ?>
			<?php \GreaterMedia\Shows\podcasts_link_html( $show->ID ); ?>
			<?php \GreaterMedia\Shows\galleries_link_html( $show->ID ); ?>
			<?php \GreaterMedia\Shows\affiliate_marketing_link_html( $show->ID ); ?>
			<?php \GreaterMedia\Shows\videos_link_html( $show->ID ); ?>
		</ul><?php
	endif;
remove_filter( 'bbgi_expand_redirects', '__return_false' );
?></div>

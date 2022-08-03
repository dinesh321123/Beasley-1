<?php

get_header();
wp_reset_query();

if ( ee_is_first_page() ):
	get_template_part( 'partials/category/meta' );
endif;

// Getting Featured Curated posts for the category
$category_archive_obj = get_queried_object();
$ca_featured_curated_posts_query = ee_get_category_featured_posts( $category_archive_obj->term_id );
$ca_featured_curated_posts = !empty($ca_featured_curated_posts_query['result']) ? $ca_featured_curated_posts_query['result']->posts : array();
$total_ca_featured_curated = (!empty($ca_featured_curated_posts) ) ? count($ca_featured_curated_posts) : 0;

// Getting Posts related to the category
$category_archive_posts_query = ee_get_category_posts_query( $category_archive_obj->slug, $ca_featured_curated_posts_query['exclude_posts'], $total_ca_featured_curated );
$category_archive_posts = !empty($category_archive_posts_query) ? $category_archive_posts_query->posts : array();

if (count($category_archive_posts) < 1) { ?>
	<div class="content-wrap">
		<div class="d-flex">
			<?php ee_the_have_no_posts(); ?>
		</div>
	</div>
<?php }

if ( ee_is_first_page() ) {
	$category_archive_posts = array_merge($ca_featured_curated_posts, $category_archive_posts);
	if( count($category_archive_posts) > 0 ) {
		$ca_featured_section_posts = array_slice($category_archive_posts, 0, 5);
		set_query_var( 'featured_posts', $ca_featured_section_posts );
		get_template_part( 'partials/category/featured' );
	}
	$category_archive_posts = array_slice($category_archive_posts, 5);
}

$show_ad_section_index = array(
	ee_is_first_page() ? 2 : 1
);
$current_ca_render_index = 1;
while(count($category_archive_posts) > 0) {
	if(( $current_ca_render_index == 2 ) && ee_is_first_page() ) {
		?>
			<div class="content-wrap">
				<h2 class="section-head">
					<span>More <?php echo $category_archive_obj->name; ?></span>
				</h2>
			</div>
		<?php
	}

	$have_ad = null;
	$posts_fetch = 12;
	if( in_array($current_ca_render_index, $show_ad_section_index) ) {
		$posts_fetch = 8;
		$have_ad = "advertise";
	}
	$display_ca_archive_posts = array_slice($category_archive_posts, 0, $posts_fetch);
	$category_archive_posts = array_slice($category_archive_posts, $posts_fetch);
	set_query_var( 'display_ca_archive_posts', $display_ca_archive_posts );
	get_template_part( 'partials/category/archive', $have_ad );
	$current_ca_render_index++;
}
?>
	<div class="content-wrap">
		<div class="d-flex">
			<div class="w-67">
				<?php ee_load_more( $category_archive_posts_query ); ?>
			</div>
		</div>
	</div>
<?php

get_footer();

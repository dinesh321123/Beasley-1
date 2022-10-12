<?php
	$category_archive_data = get_query_var( 'category_archive_data' );
	$category_archive_post = isset($category_archive_data['category_archive_post']) ? $category_archive_data['category_archive_post'] : null;
	$cap_is_sponsored = isset($category_archive_data['cap_is_sponsored']) ? $category_archive_data['cap_is_sponsored'] : false;
	$cap_show_icon = isset($category_archive_data['cap_show_icon']) ? $category_archive_data['cap_show_icon'] : false;
	$ca_add_desc = isset($category_archive_data['ca_add_desc']) ? $category_archive_data['ca_add_desc'] : false;
?>
<div class="post-title <?php if($ca_add_desc) { echo "ca-title-with-desc"; } ?>">
	<h3>
		<?php if($cap_is_sponsored) { ?>
			<small>SPONSORED</small>
		<?php } ?>
		<a href="<?php ee_the_permalink($category_archive_post); ?>">
			<?php echo get_the_title($category_archive_post); ?>
		</a>
		<?php if( has_post_format( 'video', $category_archive_post->ID ) && $cap_show_icon ) { ?>
			<a href="<?php ee_the_permalink($category_archive_post); ?>" class="icon-link">
				<i class="fa fa-play" aria-hidden="true"></i>
			</a>
		<?php } else if( has_post_format( 'image', $category_archive_post->ID ) && $cap_show_icon ) { //audio?>
			<a href="<?php ee_the_permalink($category_archive_post); ?>" class="icon-link">
				<i class="fa fa-picture-o" aria-hidden="true"></i>
			</a>
		<?php } ?>
	</h3>
	<?php if($ca_add_desc) { ?>
		<p>
			<?php echo wp_strip_all_tags( get_the_content(null, true, $category_archive_post) ); ?>
		</p>
	<?php } ?>
</div>

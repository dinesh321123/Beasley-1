<?php
$display_ca_archive_posts = get_query_var( 'display_ca_archive_posts' );
if( !empty($display_ca_archive_posts) && ( count($display_ca_archive_posts) > 0 ) ) { ?>
<div class="archive-tiles content-wrap -grid -medium">
	<ul class="list-grid-ul">
		<?php
			foreach ($display_ca_archive_posts as $key=>$archive_ca_post) {
				$cap_sponsored_by = ee_get_sponsored_by($archive_ca_post);
				$cap_is_sponsored = ( $cap_sponsored_by !== '' ) ? true : false;
				$category_archive_data = array(
					'category_archive_post' => $archive_ca_post,
					'cap_is_sponsored' 		=> $cap_is_sponsored,
					'cap_show_icon' 		=> true
				); ?>
				<li <?php if($cap_is_sponsored) { echo 'class="bg-red'.($key < 3 ? ' bg-red-thumbnail' : '') .'"'; }?>>
					<?php
						set_query_var( 'category_archive_data', $category_archive_data );
						if($key < 3) {
							get_template_part( 'partials/tile/thumbnail', 'category' );
						}
						get_template_part( 'partials/tile/title', 'category' ); ?>
				</li>
				<?php
			}
		?>
	</ul>
</div>
<?php } ?>
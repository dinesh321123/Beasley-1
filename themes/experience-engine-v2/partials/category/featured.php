<?php
$ca_featured_section_posts = get_query_var( 'featured_posts' );
if( !empty($ca_featured_section_posts) && ( count($ca_featured_section_posts) > 0 ) ) {
?>
<div class="content-wrap">
	<?php the_archive_title( '<h2 class="section-head"> <span class="bigger">', '</span></h2>' ); ?>
	<div class="d-flex">
		<div class="archive-tiles -grid -custom w-75 m-60  pl-30">
			<div class="blog-first w-60 m-100">
				<?php
				$category_archive_data = array(
					'category_archive_post' => $ca_featured_section_posts[0],
					'cap_is_sponsored' 		=> false
				);
				set_query_var( 'category_archive_data', $category_archive_data );
				get_template_part( 'partials/tile/title', 'category' );
				get_template_part( 'partials/tile/thumbnail', 'category' );
				?>
			</div>
			<div class="blog-second w-40 d-sm-none">
				<div class="list-grid">
					<ul>
						<?php
							foreach ($ca_featured_section_posts as $key=>$feature_ca_post) {
								if($key == 0) {
									continue;
								}
								$category_archive_data = array(
									'category_archive_post' => $feature_ca_post,
									'cap_is_sponsored' 		=> false
								);
								?>
								<li>
									<?php
										set_query_var( 'category_archive_data', $category_archive_data );
										if ($key === 1) {
											get_template_part( 'partials/tile/thumbnail', 'category' );
										}
										get_template_part( 'partials/tile/title', 'category' );
									?>
								</li>
						<?php } ?>
					</ul>
				</div>
			</div>
			<img src="<?php echo get_template_directory_uri() ?>/assets/images/add2.jpg">
		</div>
		<div class="w-25 m-40">
			<img src="<?php echo get_template_directory_uri() ?>/assets/images/add1.jpg">
		</div>
	</div>
</div>
<?php
}
?>
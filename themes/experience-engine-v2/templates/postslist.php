<?php
/**
 * Template Name: Posts list as per Author
 */
?>

<?php get_header(); ?>

<?php
wp_reset_query();
$author_id = get_query_var( 'author_id' );
echo '<div class="', join( ' ', get_post_class() ), '">'; ?>

	<div class="archive-title content-wrap">
		<h1>
			<span>
				<?php
				echo get_the_author_meta('display_name', $author_id);
				?>
			</span>
		</h1>
	</div>

 <?php
	$pre_query = array(
			'post_type' => array('post', 'gmr_gallery', 'listicle_cpt', 'affiliate_marketing'),
			// 'post_author'	=> $author_id, //Author ID
			'meta_query' => array(
					'relation' => 'OR',
					array('key' => 'primary_author_cpt','value' => $author_id,'compare' => '=',),
					array('key' => 'secondary_author_cpt','value' => $author_id,'compare' => '=',),
					),
			'post_status' => 'publish',
			'paged' => get_query_var( 'paged' ),
			'posts_per_page'=> '16',
			'search_author_id' => $author_id
	);
	add_filter( 'posts_where', 'searchWithAuthorID', 10, 2 );
	$author_query = new WP_Query( $pre_query );
	remove_filter( 'posts_where', 'searchWithAuthorID', 10, 2 );
	// echo "<pre>", print_r($author_query->request), "</pre>";
	if ( $author_query->have_posts() ) {
		echo '<div class="archive-tiles content-wrap -grid -large">';
		while ( $author_query->have_posts() ) {
			$author_query->the_post(); ?>
			<div data-post-id="post" <?php post_class(); ?> >
			<?php get_template_part( 'partials/tile/thumbnail' ); ?>
			<?php get_template_part( 'partials/tile/title' ); ?>
			</div>
			<?php
		}
		echo '</div>';
		echo '<div class="content-wrap">';
		ee_load_more( $author_query );
		echo '</div>';

	} else {
		echo '<div class="content-wrap">';
			ee_the_have_no_posts();
		echo '</div>';
	}
wp_reset_postdata();

echo '</div>';

 function searchWithAuthorID( $where, $wp_query ){
	 global $wpdb;
	 if ( $search_term = $wp_query->get( 'search_author_id' ) ) {
		 $where .= ' or post_author = '. $search_term;
	 }
	 return $where;
 }

get_footer(); ?>

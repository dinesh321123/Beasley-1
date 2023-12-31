<?php
/**
 * Template Name: Posts list as per Author
 */
?>

<?php get_header(); ?>

<?php
$author_id = get_query_var( 'author_id' );
$user_data = get_userdata($author_id);

if($author_id == '' || !$user_data){

	global $wp_query;
    $wp_query->set_404();
    status_header(404);
    include(get_404_template());
    exit;

}else{

	echo '<div class="', join( ' ', get_post_class() ), '">'; ?>
	<?php if ( ee_is_first_page() ): ?>
		<div class="archive-title content-wrap">
			<h1>
				<span>
					<?php
					echo get_the_author_meta('display_name', $author_id);
					?>
				</span>
			</h1>
		</div>
	<?php endif; ?>
	<?php
		$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
		$pre_query = array(
				'post_type' => array('post', 'gmr_gallery', 'listicle_cpt', 'affiliate_marketing'),
				'meta_query' => array(
						'relation' => 'OR',
						array('key' => 'primary_author_cpt','value' => $author_id,'compare' => '=',),
						array('key' => 'secondary_author_cpt','value' => $author_id,'compare' => '=',),
						),
				'post_status' => 'publish',
				'paged' => $paged,
				'posts_per_page'=> 16,
				'search_author_id' => $author_id
		);
		add_filter( 'posts_where', 'searchWithAuthorID', 10, 2 );
		$author_query = new WP_Query( $pre_query );
		remove_filter( 'posts_where', 'searchWithAuthorID', 10, 2 );
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
}

function searchWithAuthorID( $where, $wp_query ){
	global $wpdb;
	$post_status = 'publish';
	$post_types = array('post', 'gmr_gallery', 'listicle_cpt', 'affiliate_marketing'); // Replace with your custom post types
	$post_types_sql = implode("','", $post_types);

	if ( $search_term = $wp_query->get( 'search_author_id' ) ) {
		$where .= ' or post_author = '. $search_term;
		$where .= " AND {$wpdb->posts}.post_status = '{$post_status}'";
		$where .= " AND {$wpdb->posts}.post_type IN ('{$post_types_sql}')";
	}
	return $where;
}

get_footer(); ?>

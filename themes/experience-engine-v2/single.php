<?php
get_header();

ee_switch_to_article_blog();
the_post();

?><div id="post-<?php the_ID(); ?>" <?php post_class( [ 'single', 'article-category-archive' ] ); ?>>
<?php if ( ee_is_first_page() ) : ?>
	<?php if ( ee_get_current_show() ) : ?>
		<?php if ( bbgi_featured_image_layout_is( null, 'top' ) ) : ?>
			<header class="post-info">
				<?php get_template_part( 'partials/featured-media' ); ?>
			</header>
		<?php endif; ?>

		<div class="content-wrap">
			<?php get_template_part( 'partials/show/header' ); ?>
		</div>
	<?php endif; ?>

	<header class="post-info">
		<?php if ( ee_get_current_show() ) : ?>

			<?php if ( bbgi_featured_image_layout_is( null, 'poster' ) ) : ?>

				<?php get_template_part( 'partials/featured-media' ); ?>

			<?php endif; ?>

		<?php elseif ( bbgi_featured_image_layout_is( null, 'top' ) || bbgi_featured_image_layout_is( null, 'poster' ) ) : ?>

			<?php get_template_part( 'partials/featured-media' ); ?>

		<?php endif; ?>

		<h1>
			<?php the_title(); ?>
		</h1>

		<?php if( is_singular( 'post' ) ) : ?>
			<div class="post-meta">
				<?php get_template_part( 'partials/content/meta' ); ?>
			</div>
		<?php endif; ?>
	</header>

	<div class="entry-content content-wrap">
		<div class="description">
			<?php if ( bbgi_featured_image_layout_is( null, 'inline' ) ) : ?>
				<?php get_template_part( 'partials/featured-media' ); ?>
			<?php endif; ?>

			<?php ee_the_content_with_ads(); ?>

			<?php if ( is_singular( 'post' ) ) : ?>
				<div class="profile">
					<?php echo get_the_author_meta( 'description' ); ?>
				</div>
			<?php endif; ?>

			<?php get_template_part( 'partials/footer/common', 'description' ); ?>
			<?php get_template_part( 'partials/footer/newsletterSignupForm', 'nsf' ); ?>
			<?php get_template_part( 'partials/content/categories' ); ?>
			<?php get_template_part( 'partials/content/tags' ); ?>
		</div>

		<?php get_template_part( 'partials/ads/sidebar-sticky' ); ?>
	</div>

	<?php get_template_part( 'partials/related-articles' );	?>
<?php
endif;
// Check if primary category exists
$article_cat = ee_get_primary_terms(get_the_ID(), 'category', false);
$article_primary_cat = $article_cat['primary'];
if( !empty( $article_primary_cat ) ) {
	// Get category archive details for the primary category
	$article_ca_data = ee_get_category_archive_details( $article_primary_cat, true, get_the_ID() );
	$article_ca_query = $article_ca_data['query'];

	// Get posts from the category query
	$article_ca_posts = !empty($article_ca_query) ? $article_ca_query->posts : array();

	// Display related articles for the primary category
	ee_ca_get_articles( $article_primary_cat, $article_ca_posts, $article_ca_data, false, true );
	
	// Get the current page number for pagination
	$current_page = get_query_var('related_page') ? absint(get_query_var('related_page')) : 1;
	
	// Check if more pages are available and enable "Load More" functionality
	if($article_ca_query->max_num_pages >= ($current_page + 1)) {
		ee_load_more_ca( $article_ca_query, true );
	}
} ?>
</div><?php
restore_current_blog();


get_footer();

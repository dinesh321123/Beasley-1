<?php
/**
 * Single Post template file
 *
 * @package Greater Media
 * @since   0.1.0
 */

get_header();
?>

<?php while ( have_posts() ) : the_post(); ?>

	<div class="container">

		<?php
		/**
		 * This runs a check to determine if the post has a thumbnail, and that it's not a gallery or video post format.
		 */
		if ( has_post_thumbnail() && ! \Greater_Media\Fallback_Thumbnails\post_has_gallery() && ! has_post_format( 'video' ) && ! has_post_format( 'audio' )  ): ?>
			<div class="article__thumbnail" style='background-image: url(<?php gm_post_thumbnail_url( 'gm-article-thumbnail' ); ?>)'>
				<?php

					$image_attr = image_attribution();

					if ( ! empty( $image_attr ) ) {
						echo $image_attr;
					}

				?>
			</div>
		<?php endif; ?>

		<section class="content">

			<article id="post-<?php the_ID(); ?>" <?php post_class( 'article cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

				<div class="ad__inline--right desktop">
					<?php do_action( 'acm_tag_gmr_variant', 'mrec-body', 'desktop', array( 'min_width' => 1024 ) ); ?>
				</div>

				<header class="article__header">


					<h2 class="article__title" itemprop="headline"><?php the_title(); ?></h2>
					<?php get_template_part( 'partials/social-share' ); ?>

				</header>

				<section class="article__content" itemprop="articleBody">

					<?php the_content(); ?>

					<!-- begin video player -->
	<div id="playerContainer" ></div>
<script type="text/javascript" src="https://player.ooyala.com/v3/7ed66e6e45b442dda34890f374bbb46c"></script>
<script type="text/javascript">
var ooyalaPlayer;
OO.ready(function() {
    var playerConfiguration = {
adSetCode:'f25d24d32a3d4e2e9f2b8ddc3350f3e0',
playlistsPlugin: {"data":["65c0d8a9a17d46c4b9845a749b42777c","e4c4cac195bf482c99b409f0cfe16a75","2f8f6b2ed35c4ee1875595ddf881f605","13454882812d48159b3bc46a773fe154","14c1dc4761f84ce9a4bc0b08e50bf4a6","8661fa0b1d8f4db4926a90389dee8275","a7ba1685b19a4f6f9ab900cde43d5325","87b640f4215b46b5b2df1b662feb9679"]},
        autoplay: false,
        loop: false,
        height: 672,
        width: '',
        useFirstVideoFromPlaylist: true
    };

    ooyalaPlayer = OO.Player.create('playerContainer', '', playerConfiguration);
});
</script>
					<!-- end player -->

				</section>

				<?php get_template_part( 'partials/article-footer' ); ?>

				<div class="ad__inline--right mobile">
					<?php do_action( 'acm_tag_gmr_variant', 'mrec-body', 'mobile', array( 'max_width' => 1023 ) ); ?>
				</div>
				
				<?php if ( function_exists( 'related_posts' ) ): ?>
					<?php related_posts( array( 'template' => 'partials/related-posts.php' ) ); ?>
				<?php endif; ?>

			</article>

		</section>

	</div>

<?php endwhile; ?>
<?php
get_footer();

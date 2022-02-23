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
		if ( has_post_thumbnail() && ! bbgi_post_has_gallery() && ! has_post_format( 'video' ) && ! has_post_format( 'audio' )  ): ?>
			<div class="article__thumbnail" style='background-image: url(<?php gm_post_thumbnail_url( 'gm-article-thumbnail' ); ?>)'>
				<?php bbgi_the_image_attribution(); ?>
			</div>
		<?php endif; ?>

		<section class="content">

			<article id="post-<?php the_ID(); ?>" <?php post_class( 'article cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
				<header class="article__header">


					<h2 class="article__title" itemprop="headline"><?php the_title(); ?></h2>
					<?php get_template_part( 'partials/social-share' ); ?>

				</header>

				<section class="article__content" itemprop="articleBody">

					<?php the_content(); ?>

					<!-- begin 2017 video player -->
					<div id="playerContainer" ></div>
					<script type="text/javascript" src="https://player.ooyala.com/v3/7ed66e6e45b442dda34890f374bbb46c"></script>
					<script type="text/javascript">
					var ooyalaPlayer;

					OO.ready(function() {
					    var playerConfiguration = {
					        playlistsPlugin: {"data":["4c50b717f14343399f5ba4a424c9851a","65c0d8a9a17d46c4b9845a749b42777c","e4c4cac195bf482c99b409f0cfe16a75","2f8f6b2ed35c4ee1875595ddf881f605","13454882812d48159b3bc46a773fe154","14c1dc4761f84ce9a4bc0b08e50bf4a6","8661fa0b1d8f4db4926a90389dee8275","a7ba1685b19a4f6f9ab900cde43d5325","87b640f4215b46b5b2df1b662feb9679"]},
					        autoplay: false,
					        loop: false,
					        height: 666,
					        width: '',
					        useFirstVideoFromPlaylist: true
					    };

					    ooyalaPlayer = OO.Player.create('playerContainer', '', playerConfiguration);
					});

					</script>
					<!-- end player -->

				</section>

				<?php get_template_part( 'partials/article-footer' ); ?>

				<?php if ( function_exists( 'related_posts' ) ): ?>
					<?php related_posts( array( 'template' => 'partials/related-posts.php' ) ); ?>
				<?php endif; ?>

			</article>

		</section>

		<?php get_sidebar(); ?>
	</div>

<?php endwhile; ?>
<?php
get_footer();

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

					<!-- begin video player -->
						<div id="playerContainer"></div>
						</center>
						<script type="text/javascript" src="https://player.ooyala.com/v3/f51c63884c474b7fbbc2d5b43fc762c0"></script>
						<script type="text/javascript">
						var ooyalaPlayer;

						OO.ready(function() {
						    var playerConfiguration = {
						adSetCode:'dd11d74dc90643d08199ca46b2caf9b0',
						        playlistsPlugin: {"data":["98e4c101998c44a2b351b20831b54cac","b4db237b7ee4427792263eaeec8f26f6","72367a253f0471690803127867ab69f"]},
						        autoplay: false,
						        loop: false,
						        height: 505,
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

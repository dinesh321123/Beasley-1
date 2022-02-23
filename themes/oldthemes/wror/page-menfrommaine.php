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

		<?php if ( has_post_thumbnail() ) {

				the_post_thumbnail( 'full', array( 'class' => 'single__featured-img' ) );

			}
		?>

		<section class="content">

			<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
				<header class="entry__header">

					<time class="entry__date" datetime="<?php echo get_the_time(); ?>"><?php the_date('F j, Y'); ?></time>
					<h2 class="entry__title" itemprop="headline"><?php the_title(); ?></h2>
					<?php get_template_part( 'partials/social-share' ); ?>

				</header>

				<section class="entry-content" itemprop="articleBody">

					<?php the_content(); ?>

					<!-- begin video player -->

						<div id="playerContainer" ></div>
						<script type="text/javascript" src="https://player.ooyala.com/v3/fb285cf1ba8544b6b653c1a7d8a1eab3"></script>
						<script type="text/javascript">
						var ooyalaPlayer;

						OO.ready(function() {
						    var playerConfiguration = {
						        playlistsPlugin: {"data":["10d0c849e8ce48058d606ed25450bb13"]},
						        autoplay: false,
						        loop: false,
						        height: 400,
						        width: '',
						        useFirstVideoFromPlaylist: true
						    };

						    ooyalaPlayer = OO.Player.create('playerContainer', '', playerConfiguration);
						});

						</script>

						<!-- end video 1 -->


				</section>

				<?php get_template_part( 'partials/article', 'footer' ); ?>

			</article>

		</section>

		<?php get_sidebar(); ?>

	</div>

<?php endwhile; ?>
<?php
get_footer();

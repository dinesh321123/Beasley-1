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
					<h2>Working Out with Hank Morse</h2>
					<div id="playerContainer" ></div>
						<script type="text/javascript" src="https://player.ooyala.com/v3/cbb0d1ce49694627b43936c3fb51eaba"></script>
						<script type="text/javascript">
						var ooyalaPlayer;

						OO.ready(function() {
						    var playerConfiguration = {
						        playlistsPlugin: {"data":["c9a65fbfdcc2431189a65afe09d4071e","f1018946e20c4e9eab139caef056108d","abce54de6a1c4b2e889aff137a4c94ce","b6d372a30e7a44f39d5a73f0b9b39b2f","6bd4cb08f0149c1be14aa950885b475"]},
						        autoplay: false,
						        loop: false,
						        height: 540,
						        width: '',
						        useFirstVideoFromPlaylist: true
						    };

						    ooyalaPlayer = OO.Player.create('playerContainer', '', playerConfiguration);
						});

						</script>
						<!-- end video 1 -->

						<!-- begin video player 2-->
						<h2>Q &amp; A with Mike Boyle</h2>
						<div align="center">
							<script src='//player.ooyala.com/v3/cbb0d1ce49694627b43936c3fb51eaba'></script>
							<div id='ooyalaplayer_QandA' style='width:auto;height:382px'></div>
							<script>OO.ready(function() { OO.Player.create('ooyalaplayer_QandA', 'ZwNDcxbDqbHsxbJ_Kr12sg78eqzABC-x', {"enableChannels":true}); });</script>
							<noscript><div>Please enable Javascript to watch this video</div></noscript>
						</div>
						<!-- end video player 2 -->

				</section>

				<?php get_template_part( 'partials/article', 'footer' ); ?>

			</article>

		</section>

		<?php get_sidebar(); ?>

	</div>

<?php endwhile; ?>
<?php
get_footer();

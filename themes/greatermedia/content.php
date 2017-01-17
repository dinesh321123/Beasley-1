<?php while ( have_posts() ) : the_post(); ?>

	<div class="container">

		<?php if ( has_post_thumbnail() ) {

				the_post_thumbnail( 'full', array( 'class' => 'single__featured-img' ) );

			}
		?>

		<section class="content">

			<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

				<div class="ad__inline--right desktop">
					<?php do_action( 'acm_tag', 'dfp_ad_right_rail_pos1' ); ?>
				</div>

				<header class="entry__header">

					<time class="entry__date" datetime="<?php echo get_the_time(); ?>"><?php the_date('F j, Y'); ?></time>
					<h2 class="entry__title" itemprop="headline"><?php the_title(); ?></h2>
					<?php get_template_part( 'partials/social-share' ); ?>

				</header>

				<section class="entry-content" itemprop="articleBody">

					<?php the_content(); ?>

				</section>

				<div class="ad__inline--right mobile">
					<?php do_action( 'acm_tag_gmr_variant', 'mrec-body', 'mobile' ); ?>
				</div>

				<?php get_template_part( 'partials/article', 'footer' ); ?>

			</article>

		</section>

	</div>

<?php endwhile; ?>

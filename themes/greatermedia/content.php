<?php while ( have_posts() ) : the_post(); ?>

	<div class="container">

		<?php if ( has_post_thumbnail() ) {

				the_post_thumbnail( 'full', array( 'class' => 'single__featured-img' ) );

			}
		?>

		<section class="content">

			<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

				<div class="ad__inline--right desktop">
					<?php // 'desktop' is a variant, can call a 'mobile' variant elsewhere if we need it, but never the same variant twice ?>
					<?php do_action( 'acm_tag_gmr_variant', 'mrec-body', 'desktop' ); ?>
				</div>

				<header class="entry__header">

					<time class="entry__date" datetime="<?php echo get_the_time(); ?>"><?php the_date('F j'); ?></time>
					<h2 class="entry__title" itemprop="headline"><?php the_title(); ?></h2>
					<a class="icon-facebook social-share-link" href="http://www.facebook.com/sharer/sharer.php?u=[URL]&title=[TITLE]"></a>
					<a class="icon-twitter social-share-link" href="http://twitter.com/home?status=[TITLE]+[URL]"></a>
					<a class="icon-google-plus social-share-link" href="https://plus.google.com/share?url=[URL]"></a>

				</header>

				<section class="entry-content" itemprop="articleBody">

					<?php the_content(); ?>

				</section>

				<div class="ad__inline--right mobile">
					<?php do_action( 'acm_tag_gmr_variant', 'mrec-body', 'mobile' ); ?>
				</div>

				<?php get_template_part( 'partials/post', 'footer' ); ?>

			</article>

		</section>

	</div>

<?php endwhile; ?>

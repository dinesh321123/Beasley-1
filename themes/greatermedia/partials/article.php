<?php while ( have_posts() ) : the_post(); ?>

	<div class="container">

		<?php if ( has_post_thumbnail() ): ?>
			<div class="article__thumbnail" style='background-image: url(<?php gm_post_thumbnail_url( 'full' ); ?>)'></div>
		<?php endif; ?>

		<section class="content">

			<article id="post-<?php the_ID(); ?>" <?php post_class( 'article cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

				<div class="ad__inline--right desktop">
					<?php // 'desktop' is a variant, can call a 'mobile' variant elsewhere if we need it, but never the same variant twice ?>
					<?php do_action( 'acm_tag_gmr_variant', 'mrec-body', 'desktop' ); ?>
				</div>

				<header class="article__header">

					<time class="article__date" datetime="<?php echo get_the_time(); ?>"><?php the_date('F j'); ?></time>
					<h2 class="article__title" itemprop="headline"><?php the_title(); ?></h2>
					<a class="icon-facebook social__link" href="http://www.facebook.com/sharer/sharer.php?u=[URL]&title=[TITLE]"></a>
					<a class="icon-twitter social__link" href="http://twitter.com/home?status=[TITLE]+[URL]"></a>
					<a class="icon-google-plus social__link" href="https://plus.google.com/share?url=[URL]"></a>

				</header>

				<section class="article__content" itemprop="articleBody">

					<?php the_content(); ?>

				</section>

				<?php get_template_part( 'partials/article-footer' ); ?>
				
				<div class="ad__inline--right mobile">
					<?php do_action( 'acm_tag_gmr_variant', 'mrec-body', 'mobile' ); ?>
				</div>
			
				<?php if ( post_type_supports( get_post_type(), 'comments' ) ) { // If comments are open or we have at least one comment, load up the comment template. ?>
					<div class='article__comments'>
						<?php comments_template(); ?>
					</div>
				<?php } ?>
				

				<?php if ( function_exists( 'related_posts' ) ): ?>
					<?php related_posts( array( 'template' => 'partials/related-posts.php' ) ); ?>
				<?php endif; ?>
				
			</article>			

		</section>

	</div>

<?php endwhile; ?>

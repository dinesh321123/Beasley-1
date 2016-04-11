<?php while ( have_posts() ) : the_post(); ?>

	<div class="container">

		<?php get_template_part( 'partials/show-mini-nav' ); ?>

		<?php Greater_Media\Flexible_Feature_Images\feature_image_preference_is( get_the_ID(), 'poster' ) ? get_template_part( 'partials/feature-image-article' ) : ''; ?>

		<section class="content">

			<article id="post-<?php the_ID(); ?>" <?php post_class( 'article cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

				<div class="ad__inline--right desktop">
					<?php do_action( 'acm_tag_gmr_variant', 'mrec-body', 'desktop', array( 'min_width' => 1024 ) ); ?>
				</div>

				<header class="article__header">
					<?php Greater_Media\Flexible_Feature_Images\feature_image_preference_is( get_the_ID(), 'top' ) ? get_template_part( 'partials/feature-image-article' ) : ''; ?>
					<time class="article__date" datetime="<?php echo esc_attr( get_the_time() ); ?>"><?php esc_html( the_date('F j, Y') ); ?></time>					<h2 class="article__title" itemprop="headline"><?php the_title(); ?></h2>
					<?php get_template_part( 'partials/social-share' ); ?>

				</header>

				<section class="article__content" itemprop="articleBody">

					<?php Greater_Media\Flexible_Feature_Images\feature_image_preference_is( get_the_ID(), 'inline' ) ? get_template_part( 'partials/feature-image-article' ) : ''; ?>

					<?php the_content(); ?>

				</section>
				<?php if ( function_exists( 'related_posts' ) ): ?>
					<?php related_posts( array( 'template' => 'partials/related-posts.php' ) ); ?>
				<?php endif; ?>


				<?php get_template_part( 'partials/article-footer' ); ?>

				<div class="ad__inline--right mobile">
					<?php do_action( 'acm_tag_gmr_variant', 'mrec-body', 'mobile', array( 'max_width' => 1023 ) ); ?>
				</div>

				<?php if ( post_type_supports( get_post_type(), 'comments' ) ) { // If comments are open or we have at least one comment, load up the comment template. ?>
					<div class='article__comments'>
						<?php comments_template(); ?>
					</div>
				<?php } ?>




			</article>

		</section>

	</div>

<?php endwhile; ?>

<?php the_post(); ?>

<div class="container">

	<?php Greater_Media\Flexible_Feature_Images\feature_image_preference_is( get_the_ID(), 'poster' ) ? get_template_part( 'partials/feature-image-article' ) : ''; ?>

	<section class="content">

		<article id="post-<?php the_ID(); ?>" <?php post_class( 'article cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

			<header class="article__header">
				<?php Greater_Media\Flexible_Feature_Images\feature_image_preference_is( get_the_ID(), 'top' ) ? get_template_part( 'partials/feature-image-article' ) : ''; ?>
				<h2 class="article__title" itemprop="headline"><?php the_title(); ?></h2>
				<?php get_template_part( 'partials/social-share' ); ?>
			</header>

			<section class="article__content" itemprop="articleBody">

				<?php Greater_Media\Flexible_Feature_Images\feature_image_preference_is( get_the_ID(), 'inline' ) ? get_template_part( 'partials/feature-image-article' ) : ''; ?>

				<?php the_content(); ?>

			</section>

		</article>

	</section>

</div>

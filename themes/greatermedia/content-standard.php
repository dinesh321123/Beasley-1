<?php if ( has_post_thumbnail() ) {

		the_post_thumbnail( 'full', array( 'class' => 'single__featured-img' ) );

	}
?>

<?php while ( have_posts() ) : the_post(); ?>

		<section class="content">

			<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

				<div class="ad__inline--right">
					<img src="http://placehold.it/300x250&amp;text=inline ad">
				</div>

				<header class="entry__header">

					<time class="entry__date" datetime="<?php echo get_the_time(); ?>"><?php the_date('F j, Y'); ?></time>
					<h2 class="entry__title" itemprop="headline"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<?php get_template_part( 'partials/social-share' ); ?>

				</header>

				<section class="entry-content" itemprop="articleBody">

					<?php the_content(); ?>

				</section>

				<?php get_template_part( 'partials/article', 'footer' ); ?>

			</article>

		</section>

<?php endwhile; ?>
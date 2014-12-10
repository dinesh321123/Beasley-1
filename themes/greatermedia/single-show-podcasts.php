<?php get_header(); ?>

	<main class="main" role="main">

		<div class="container">

			<section class="content">

				<?php the_post(); ?>

					<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

						<header class="entry-header">

							<h2 class="entry-title" itemprop="headline"><a href="<?php the_permalink(); ?>"><?php the_title(); ?> Podcasts</a></h2>

						</header>

						<div class="entry-content">
							single-show-podcasts.php
						</div>

					</article>

			</section>

		</div>

	</main>

<?php get_footer();
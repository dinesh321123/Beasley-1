<?php
/**
 * Single Post template file
 *
 * @package Greater Media
 * @since   0.1.0
 */

get_header();
?>

<div class="container">
	<section class="content">
		<article id="post-242" class="article cf post-242 page type-page status-publish hentry" role="article" itemscope="" itemtype="http://schema.org/BlogPosting">
			<header class="article__header">
				<h2 class="article__title" itemprop="headline"><?php the_title(); ?></h2>
				<?php get_template_part( 'partials/social-share' ); ?>
			</header>
			<section class="article__content" itemprop="articleBody">
				<?php the_content(); ?>
				
				<style>
					.jsx {
						margin: 0 auto;
					}
				</style>
				<script type="text/javascript" src="http://s1.march.dellsportsgames.com/atx/init/atx-pool=26/" id="x-march-generator"></script> 
			</section>
		</section>
	</article>
</div>

<?php get_footer(); ?>




		
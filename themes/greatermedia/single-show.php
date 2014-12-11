<?php

 get_header();

?>

	<main class="main" role="main">

		<div class="container">

			<?php the_post(); ?>

				<?php get_template_part( 'show-header' ); ?>

				<section class="content">

					<section class="show__features">
						<div class="show__feature--primary">
							<a href=""><div class="show__feature">
								<img src="http://placehold.it/570x315&text=show-feature" alt="">
								<div class="show__feature--desc">
									<h3>The Title of the Primary Featured Post on the Show Homepage</h3>
									<time class="show__feature--date" datetime="">23 SEP</time>
								</div>
							</div></a>
						</div>
						<div class="show__feature--secondary">
							<a href=""><div class="show__feature">
								<img src="http://placehold.it/570x315&text=show-feature" alt="">
								<div class="show__feature--desc">
									<h3>The Title of a Secondary Featured Post on the Show Homepage</h3>
									<time class="show__feature--date" datetime="">23 SEP</time>
								</div>
							</div></a>
							<a href=""><div class="show__feature">
								<img src="http://placehold.it/570x315&text=show-feature" alt="">
								<div class="show__feature--desc">
									<h3>The Title of a Secondary Featured Post on the Show Homepage</h3>
									<time class="show__feature--date" datetime="">23 SEP</time>
								</div>
							</div></a>
						</div>
					</section>

					<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

						<header class="entry__header">

							<h2 class="entry__title" itemprop="headline"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

						</header>

						<hr>
						<div class="entry-content">
							<div>
							<p>Show Content:</p>
							<?php the_content(); ?>
							</div>
							<hr>
							<?php
							echo '<div>';
							if( get_post_meta($post->ID, 'show_homepage', true) ) {
								if( function_exists( 'TDS\get_related_term' ) ) {
									$term = TDS\get_related_term( $post->ID );
								}
								if( $term ) {
									echo 'Related term is: ' . $term->name
									. '<br/>Term ID: ' . $term->term_id
									. '<br/>Term Slug: ' . $term->slug;
									
								} else {
									echo 'No related term found.
									This is a bug, beacuse SHOW has marked to have homepage';
								}
							} else {
								echo 'Show doesn\'t have home page';
							}
							echo '</div>';
							?>
						</div>

					</article>

			</section>

		</div>

	</main>

<?php get_footer();
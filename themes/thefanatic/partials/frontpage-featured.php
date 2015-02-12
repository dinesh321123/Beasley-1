<?php
/**
 * Partial for the Front Page Featured Content
 *
 * @package Greater Media
 * @since   0.1.0
 */
?>
<section id="featured" class="home__featured">
		<?php
		$hp_featured_query = \GreaterMedia\HomepageCuration\get_featured_query(); ?>

		<div class="featured__articles">
			<div class="featured__article">
				<a href="#<?php /* the_permalink(); */?>" class="featured__article--link">
					<div class="featured__article--image" style='background-image: url(<?php bloginfo( 'stylesheet_directory' ); ?>/images/chip-kelly-mccoy.jpg)'>
						<?php /*

							$image_attr = image_attribution();

							if ( ! empty( $image_attr ) ) {
								echo $image_attr;
							} */

						?>
					</div>
					<div class="featured__article--content">
						<div class="featured__article--heading">
							What Chris Christie might talk about in 2015 State of the State speech today.
							<?php /* the_title(); */ ?>
						</div>
					</div>
				</a>
			</div>
			<div class="featured__article">
				<a href="#<?php /* the_permalink(); */?>">
					<div class="featured__article--image" style='background-image: url(<?php bloginfo( 'stylesheet_directory' ); ?>/images/chip-kelly-mccoy.jpg)'>
						<?php /*

									$image_attr = image_attribution();

									if ( ! empty( $image_attr ) ) {
										echo $image_attr;
									} */

						?>
					</div>
					<div class="featured__article--content">
						<div class="featured__article--heading">
							What Chris Christie might talk about in 2015 State of the State speech today.
							<?php /* the_title(); */ ?>
						</div>
					</div>
				</a>
			</div>
			<div class="featured__article">
				<a href="#<?php /* the_permalink(); */?>">
					<div class="featured__article--image" style='background-image: url(<?php bloginfo( 'stylesheet_directory' ); ?>/images/chip-kelly-mccoy.jpg)'>
						<?php /*

									$image_attr = image_attribution();

									if ( ! empty( $image_attr ) ) {
										echo $image_attr;
									} */

						?>
					</div>
					<div class="featured__article--content">
						<div class="featured__article--heading">
							What Chris Christie might talk about in 2015 State of the State speech today.
							<?php /* the_title(); */ ?>
						</div>
					</div>
				</a>
			</div>
		</div>
		<?php // if we still have more posts (we almost always will), render the 3 below the main section ?>
			<div class="featured__content">
					<div class="featured__content--block">
						<a href="#<?php /* the_permalink(); */?>" class="featured__content--link">
							<div class="featured__content--image" style='background-image: url(<?php bloginfo( 'stylesheet_directory' ); ?>/images/chip-kelly-mccoy.jpg)'></div>
							<div class="featured__content--meta">
								<h2 class="featured__content--title">What Chris Christie might talk about in 2015 State of the State speech today.<?php /* the_title(); */ ?></h2>
							</div>
						</a>
					</div>
					<div class="featured__content--block">
						<a href="#<?php /* the_permalink(); */?>" class="featured__content--link">
							<div class="featured__content--image" style='background-image: url(<?php bloginfo( 'stylesheet_directory' ); ?>/images/chip-kelly-mccoy.jpg)'></div>
							<div class="featured__content--meta">
								<h2 class="featured__content--title">What Chris Christie might talk about in 2015 State of the State speech today.<?php /* the_title(); */ ?></h2>
							</div>
						</a>
					</div>
					<div class="featured__content--block">
						<a href="#<?php /* the_permalink(); */?>" class="featured__content--link">
							<div class="featured__content--image" style='background-image: url(<?php bloginfo( 'stylesheet_directory' ); ?>/images/chip-kelly-mccoy.jpg)'></div>
							<div class="featured__content--meta">
								<h2 class="featured__content--title">What Chris Christie might talk about in 2015 State of the State speech today.<?php /* the_title(); */ ?></h2>
							</div>
						</a>
					</div>
			</div>
</section>
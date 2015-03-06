<?php
/**
 * Partial for the Front Page Highlights - Community and Events
 *
 * @package Greater Media
 * @since   0.1.0
 */
?>

<section class="home__highlights">

	<div class="highlights__col">

		<div class="highlights__events">

			<h2 class="highlights__heading"><?php _e( 'Upcoming Events', 'greatermedia' ); ?></h2>

			<div class="highlights__event--item">
				<a href="#">
					<div class="highlights__event--thumb" style='background-image: url(http://lorempixel.com/200/200/people/1)'></div>

					<div class="highlights__event--meta">
						<h3 class="highlights__event--title">Hey Kid, It's Santa!</h3>
						<div class="highlights__event--date"><time datetime="">Wed, Dec 20th</time></div>
						<div class="highlights__event--time"><time datetime="">8AM - 5PM</time></div>
					</div>
				</a>
			</div>

			<div class="highlights__event--item">
				<a href="#">
					<div class="highlights__event--thumb" style='background-image: url(http://lorempixel.com/200/200/people/2)'></div>

					<div class="highlights__event--meta">
						<h3 class="highlights__event--title">Winter Season Jewelry Sale</h3>
						<div class="highlights__event--date"><time datetime="">Fri, Dec 22th</time></div>
						<div class="highlights__event--time"><time datetime="">4PM - 8:30PM</time></div>
					</div>
				</a>
			</div>

		</div>

		<div class="highlights__contests">

			<h2 class="highlights__heading"><?php _e( 'Contests', 'greatermedia' ); ?></h2>

			<div class="highlights__contest--item">
				<div class="highlights__contest--thumb" style='background-image: url(http://lorempixel.com/200/200/sports/)'></div>

					<div class="highlights__contest--meta">
						<h3 class="highlights__contest--title">John Mellencamp Christmas Live</h3>
						<div class="highlights__contest--date"><time datetime="">Sat, Dec 23th</time></div>
						<div class="highlights__contest--time"><time datetime="">4PM - 8:30PM</time></div>
						<a href="#" class="highlights__contest--btn">Enter To Win</a>
					</div>

			</div>
		</div>

		<div class="highlights__ad">

			<div class="highlights__ad--desktop">
				<?php do_action( 'acm_tag_gmr_variant', 'mrec-lists', 'desktop', array( 'min_width' => 1024 ) ); ?>
			</div>
			<div class="highlights__ad--mobile">
				<?php do_action( 'acm_tag_gmr_variant', 'mrec-lists', 'mobile', array( 'max_width' => 1023 ) ); ?>
			</div>

		</div>

	</div>

</section>
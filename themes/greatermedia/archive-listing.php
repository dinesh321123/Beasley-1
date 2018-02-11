<?php
/**
 * Archive template file for directory listing
 */

get_header(); ?>

	<section class="directory-archive">
		<?php // TODO The root directory needs an image. ?>
		<div class="directory-archive__hero" style="background-image: url('https://wmgk.gmr.dev/wp-content/uploads/sites/2/2017/01/shutterstock_519325135.jpg');">
			<div class="directory-archive__title-wrapper">
				<h1 class="directory-archive__title">What are you looking for?</h1>
			</div>
		</div>

		<div class="directory-archive__intro">
			<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque feugiat turpis eget efficitur mollis. Phasellus vitae tincidunt orci, quis convallis mauris. Proin nec felis at metus lobortis maximus eget nec massa. Donec ornare dictum vehicula. Curabitur eu dignissim elit. Mauris faucibus nisl at magna laoreet ullamcorper. Nam nec quam aliquet, fringilla leo tincidunt, convallis ante. Suspendisse vestibulum molestie dui vitae malesuada. Quisque sit amet massa sed nisl fringilla vehicula vel non ligula.</p>
		</div>

		<?php get_template_part( 'partials/directory-listing/archive-categories' ); ?>
	</section>

<? get_footer();

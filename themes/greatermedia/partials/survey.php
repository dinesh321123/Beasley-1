<?php $contest_id = get_the_ID(); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

	<?php Greater_Media\Flexible_Feature_Images\feature_image_preference_is( get_the_ID(), 'top' ) ? get_template_part( 'partials/feature-image-contest' ) : ''; ?>

	<header class="entry__header">

		<time class="entry__date"
			  datetime="<?php echo get_the_time(); ?>"><?php the_date( 'F j, Y' ); ?></time>
		<h2 class="entry__title" itemprop="headline"><a
				href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<?php get_template_part( 'partials/social-share' ); ?>

	</header>

	<?php Greater_Media\Flexible_Feature_Images\feature_image_preference_is( get_the_ID(), 'inline' ) ? get_template_part( 'partials/feature-image-contest' ) : ''; ?>

	<div class="contest__restrictions">

		<div class="contest__restriction--signin">
			<p>
				You must be signed in to participate in the survey!
			</p>

			<p>
				<a href="<?php echo esc_url( gmr_contests_get_login_url() ); ?>">Sign in here</a>
			</p>
		</div>

		<div class="contest__restriction--one-entry">
			<p>You have already taken this survey!</p>
		</div>

	</div>

	<?php the_content(); ?>

	<?php get_template_part( 'partials/article', 'footer' ); ?>

	<?php get_template_part( 'partials/submission', 'tiles' ); ?>

</article>

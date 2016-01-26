<?php $contest_id = get_the_ID(); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

	<section class="col__inner--left">

		<header class="entry__header">

			<time class="entry__date"
			      datetime="<?php echo get_the_time(); ?>"><?php the_date( 'F j, Y' ); ?></time>
			<h2 class="entry__title" itemprop="headline"><a
					href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			<?php get_template_part( 'partials/social-share' ); ?>

		</header>


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

	</section>

	<section class="col__inner--right">
		<section id="contest-form" class="contest__form"<?php gmr_contest_container_attributes(); ?>></section>
		<div class="desktop">
			<?php do_action( 'acm_tag', 'mrec-body' ); ?>
		</div>
	</section>

	<?php get_template_part( 'partials/submission', 'tiles' ); ?>

</article>

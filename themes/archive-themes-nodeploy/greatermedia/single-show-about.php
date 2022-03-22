<?php get_header(); ?>

	<div class="container">

		<?php the_post(); ?>

		<?php get_template_part( 'show-header' ); ?>

		<section class="content">

			<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article" itemscope
			         itemtype="http://schema.org/BlogPosting">

				<header class="entry-header">

					<h2 class="entry-title" itemprop="headline"><a
							href="<?php the_permalink(); ?>">About <?php the_title(); ?></a></h2>

				</header>

				<!-- <div id="logo">

						<?php
				$logo_id = get_post_meta( get_the_ID(), 'logo_image', true );
				if ( $logo_id ) {
					echo wp_get_attachment_image( $logo_id );
				}
				?>

					</div> -->
				<?php the_content(); ?>

			</article>

		</section>

		<?php get_sidebar(); ?>

		<?php
		$personalities = GreaterMedia\Shows\get_show_personalities( get_the_ID() );
		if ( count( $personalities ) > 0 ): ?>
			<section class="show__personalities">
				<?php foreach ( $personalities as $personality ) : ?>
					<article class="personality personality-<?php echo intval( $personality->ID ); ?>">
						<div class="personality__avatar">
							<?php echo get_avatar( $personality->ID ); ?>
						</div>
						<?php
						$content = apply_filters( 'the_content', get_the_author_meta( 'description', $personality->ID ) );
						$c       = explode( "</p>", $content );
						$n       = count( $c ) - 1;
						?>
						<div class="personality__meta<?php echo $n <= 1 ? ' not-has-hidden' : ''; ?>">
							<span
								class="personality__name h1"><?php echo esc_html( $personality->data->display_name ); ?></span>

							<div class="personality__bio"><?php echo $content; ?></div>
							<button class="person-toggle more-btn">more</button>
						</div>
						<?php
						$social = GreaterMedia\Shows\get_personality_social_ul( $personality );
						?>
					</article>
				<?php endforeach; ?>
			</section>
		<?php endif; ?>

	</div>

<?php get_footer();
<?php get_header(); ?>

	<main class="main" role="main">

		<div class="container">

			<?php the_post(); ?>

			<?php get_template_part( 'show-header' ); ?>

			<section class="content">

				<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

					<header class="entry-header">

						<h2 class="entry-title" itemprop="headline"><a href="<?php the_permalink(); ?>">About <?php the_title(); ?></a></h2>

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

						<?php
						$personalities = GreaterMedia\Shows\get_show_personalities( get_the_ID() );
						if ( count( $personalities ) > 0 ): ?>
							<div class="show__personalities">
								<?php foreach( $personalities as $personality ) : ?>
									<div class="personality personality-<?php echo intval( $personality->ID ); ?>">
										<div class="personality__avatar">
											<?php echo get_avatar( $personality->ID ); ?>
										</div>
										<div class="personality__meta">
											<span class="personality__name h1"><?php echo esc_html( $personality->data->display_name ); ?></span>
											<p class="personality__bio"><?php echo esc_html( get_the_author_meta( 'description', $personality->ID ) ); ?></p>
										</div>
											<?php
										$social = GreaterMedia\Shows\get_personality_social_ul( $personality );
										?>
									</div>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>


						
					</div>

				</article>

			</section>

		</div>

	</main>

<?php get_footer();
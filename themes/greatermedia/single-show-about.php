<?php get_header(); ?>

	<main class="main" role="main">

		<div class="container">

			<section class="content">

				<?php the_post(); ?>

					<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

						<header class="entry-header">

							<h2 class="entry-title" itemprop="headline"><a href="<?php the_permalink(); ?>">About <?php the_title(); ?></a></h2>

						</header>

						<div id="logo">
							
							<?php
								$logo_id = get_post_meta( get_the_ID(), 'logo_image', true );
								if ( $logo_id ) {
									echo wp_get_attachment_image( $logo_id );
								}
							?>

						</div>
						<hr>
						<div class="entry-content">

							<div class="about-the-show">
								<?php the_content(); ?>
							</div>
							<?php
							$personalities = GreaterMedia\Shows\get_show_personalities( get_the_ID() );
							if ( count( $personalities ) > 0 ): ?>
								<hr>
								<div class="show-personalities">
									<h4>Personalities</h4>
									<?php foreach( $personalities as $personality ) : ?>
										<div class="personality personality-<?php echo intval( $personality->ID ); ?>">
											<?php echo get_avatar( $personality->ID ); ?>
											<div class="personality-name"><?php echo esc_html( $personality->data->display_name ); ?></div>
											<div class="personality-bio"><?php echo esc_html( get_the_author_meta( 'description', $personality->ID ) ); ?></div>
											<?php
											$social = GreaterMedia\Shows\get_personality_social_ul( $personality );
											?>
										</div>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>

							<hr>

							<?php echo '<div>';
							if ( get_post_meta( get_the_ID(), 'show_homepage', true ) ) {
								$term = false; // To account for the (nearly impossible) case of function _not_ existing
								if ( function_exists( 'TDS\get_related_term' ) ) {
									$term = TDS\get_related_term( $post->ID );
								}
								if ( $term ) {
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
<?php get_header(); ?>

<div class="content-wrap">
	<?php the_post(); ?>

	<div class="post-content">
		<article id="post-<?php the_ID(); ?>" <?php post_class('post-article'); ?>>

			<!-- Header -->
			<header class="post-header">
				<?php get_template_part( 'partials/show/header' ); ?>
				<!-- Title -->
				<h1 class="post-title"><?php the_title(); ?></h1>
				<div class="post-meta">
					<!-- Author -->
					<div class="post-author">
						<div class="post-author-name">
							<?php echo get_avatar( get_the_author_meta( 'ID' ), 40 ); ?>
							<?php the_author_meta( 'display_name' ); ?>

							<div class="post-date">
								<?php ee_the_date(); ?>
							</div>

						</div>
					</div>

					<!-- Share -->
					<?php ee_the_share_buttons( get_permalink(), get_the_title() ); ?>
				</div>
			</header>

			<div>
				<div>
					<!-- Featured Media -->
					<figure class="post-thumbnail">
						<?php get_template_part( 'partials/featured-media' ); ?>
					</figure>

					<div class="post-thumbnail-caption">
						<?php echo get_the_post_thumbnail_caption( get_the_ID() ); ?>
					</div>

					<?php ee_the_content_with_ads(); ?>

					<!-- Categories -->
					<?php get_template_part( 'partials/content/categories' ); ?>

					<!-- Tags -->
					<?php get_template_part( 'partials/content/tags' ); ?>
				</div>

				
			</div>
		</article>

		<!-- Sidebar -->
		<aside class="post-sidebar">
			<?php get_template_part( 'partials/ads/sidebar-sticky' ); ?>
		</aside>
	</div>

	<!-- Related Articles -->
	<?php get_template_part( 'partials/related-articles' ); ?>

</div>

<?php get_footer(); ?>
<?php

ee_setup_gallery_view_metadata(); // must be called before get_header();
get_header();

ee_switch_to_article_blog();
the_post();

?><article><div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if ( bbgi_featured_image_layout_is( null, 'top' ) ) : ?>
		<section>
			<header class="post-info">
				<?php get_template_part( 'partials/featured-media', 'am-autoheight' ); ?>
			</header>
		</section>
	<?php endif; ?>

	<?php if ( bbgi_featured_image_layout_is( null, 'top' ) ) : ?>
		<section>
			<div class="content-wrap">
				<?php get_template_part( 'partials/show/header' ); ?>
			</div>
		</section>
	<?php else : ?>
		<section>
			<?php get_template_part( 'partials/show/header' ); ?>
		</section>
	<?php endif; ?>
	
	<section>
		<header class="post-info">
			<h1>
				<?php the_title(); ?>
			</h1>

			<div class="post-meta">
				<?php get_template_part( 'partials/content/meta' ); ?>
			</div>
		</header>
	</section>

	<section>
		<div class="entry-content content-wrap">
			<div class="description">
				<?php if ( bbgi_featured_image_layout_is( null, 'inline' ) ) : ?>
					<?php get_template_part( 'partials/featured-media', 'am-autoheight' ); ?>
				<?php endif; ?>

				<?php if ( stripos(get_site_url(),"musthavesandfunfinds") == false ) : ?>
					<?php the_content(); ?>
				<?php else : ?>
					<?php ee_the_content_with_stn_only(); ?>
				<?php endif; ?>

				<?php get_template_part( 'partials/affiliate-marketing-cpt/affiliatemarketingcpt' ); ?>
				<?php get_template_part( 'partials/affiliate-marketing-cpt/footer', 'description-am' ); ?>
				<?php get_template_part( 'partials/affiliate-marketing-cpt/footer', 'signupcode-am' ); ?>
				<?php get_template_part( 'partials/footer/newsletterSignupForm', 'nsf' ); ?>
				<?php get_template_part( 'partials/content/categories' ); ?>
				<?php get_template_part( 'partials/content/tags' ); ?>
			</div>

			<?php get_template_part( 'partials/ads/sidebar-sticky' ); ?>
		</div>
	</section>
	<?php get_template_part( 'partials/related-articles' );	?>

</div></article><?php

restore_current_blog();
get_footer();

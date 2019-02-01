<?php

get_header();

ee_switch_to_article_blog();
the_post(); 

?><div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php get_template_part( 'partials/show/header' ); ?>

	<header class="post-info">
		<h1>
			<?php the_title(); ?>
		</h1>

		<div class="post-meta">
			<?php get_template_part( 'partials/content/meta' ); ?>
		</div>
	</header>

	<div class="entry-content content-wrap">
		<div class="description">
			<?php get_template_part( 'partials/featured-media' ); ?>
			<?php the_content(); ?>

			<?php if ( ( $contest_prize = trim( get_post_meta( get_the_ID(), 'prizes-desc', true ) ) ) ) : ?>
				<div>
					<?php ee_the_subtitle( 'What you win:' ); ?>
					<?php echo wpautop( do_shortcode( $contest_prize ) ); ?>
				</div>
			<?php endif; ?>

			<?php if ( ( $enter = trim( get_post_meta( get_the_ID(), 'how-to-enter-desc', true ) ) ) ) : ?>
				<div>
					<?php ee_the_subtitle( 'How to enter:' ); ?>
					<?php echo wpautop( do_shortcode( $enter ) ); ?>
				</div>
			<?php endif; ?>

			<?php get_template_part( 'partials/content/categories' ); ?>
			<?php get_template_part( 'partials/content/tags' ); ?>
		</div>

		<?php get_template_part( 'partials/ads/sidebar-sticky' ); ?>
	</div>

	<?php get_template_part( 'partials/related-articles' ); ?>
</div><?php

restore_current_blog();
get_footer();

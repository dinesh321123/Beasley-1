<?php

$classes = join( ' ', get_post_class() );

get_header();

the_post(); ?>

<div class="<?php echo $classes; ?>">
	<?php if ( ee_is_first_page() ) :
		get_template_part( 'partials/show/header' );
		get_template_part( 'partials/podcast/header' );
	endif; ?>

	<?php if ( ee_is_first_page() ) : ?>
		<div class="content-wrap">
			<?php get_template_part( 'partials/podcast/actions' ); ?>
		</div>
	<?php endif; ?>

	<div class="episode-content content-wrap">
		<?php get_template_part( 'partials/ads/sidebar-sticky' ); ?>
		<?php get_template_part( 'partials/podcast/episodes' ); ?>
	</div>
</div>

<?php get_footer();

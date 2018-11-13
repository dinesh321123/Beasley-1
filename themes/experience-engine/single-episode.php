<?php get_header(); ?>

<?php the_post(); ?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php get_template_part( 'partials/show/header' ); ?>

	<div class="episode-info">
		<?php if ( get_post_field( 'post_parent' ) > 0 ) : ?>
			<div class="show-meta">
				<p class="show"><?php echo esc_html( get_the_title( get_post_field( 'post_parent' ) ) ); ?></p>
				<?php get_template_part( 'partials/add-to-favorite' ); ?>
			</div>
		<?php endif; ?>

		<div class="show-actions"><?php
			if ( ! ee_is_jacapps() ) :
				ee_the_episode_player();
			endif;

			the_title( '<h1>', '</h1>' );
		?></div>

		<div class="episode-meta">
			<?php if ( ( $duration = ee_get_episode_meta( null, 'duration' ) ) ) : ?>
				<span class="duration"><?php echo esc_html( $duration ); ?></span>
			<?php endif; ?>

			<?php if ( ( $download = ee_get_episode_meta( null, 'download' ) ) ) : ?>
				<a class="btn -empty -nobor" href="<?php echo esc_url( $download ); ?>" target="_blank" rel="noopener noreferrer">Download</a>
			<?php endif; ?>

			<span class="date"><?php ee_the_date(); ?></span>

			<?php get_template_part( 'partials/share' ); ?>
		</div>
	</div>

	<div class="episode-content">
		<div class="description"><?php
			if ( ! ee_is_jacapps() ) :
				add_filter( 'the_content', 'strip_shortcodes', 1 );
				the_content();
				remove_filter( 'the_content', 'strip_shortcodes', 1 );
			else :
				the_content();
			endif;
		?></div>

		<?php get_template_part( 'partials/episode/next-episodes' ); ?>
		<div class="ad -sidebar -right -sticky">
			<?php get_template_part( 'partials/ads/sidebar-sticky' ); ?>
		</div>
	</div>

	<?php get_template_part( 'partials/episode/podcasts' ); ?>
</div>

<?php get_footer(); ?>

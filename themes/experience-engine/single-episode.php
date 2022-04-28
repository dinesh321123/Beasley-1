<?php get_header(); ?>

<?php the_post(); ?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php get_template_part( 'partials/show/header' ); ?>

	<div class="episode-info">
		<?php if ( ( $podcast_id = get_post_field( 'post_parent' ) ) > 0 ) : ?>
			<div class="show-meta">
				<p class="show">
					<a href="<?php echo esc_url( the_permalink( $podcast_id ) ); ?>">
						<?php echo esc_html( get_the_title( $podcast_id ) ); ?>
					</a>
				</p>
			</div>
		<?php endif; ?>

		<div class="show-actions"><?php
			if ( ! ee_is_common_mobile() ) :
				ee_the_episode_player();
			endif;

		?>
			<h1>
				<span><?php the_title(); ?></span>
				<?php ee_the_episode_download(); ?>
			</h1>
		</div>

		<div class="episode-meta">
			<?php if ( ( $duration = ee_get_episode_meta( null, 'duration' ) ) ) : ?>
				<span class="duration"><?php echo esc_html( $duration ); ?></span>
			<?php endif; ?>

			<?php ee_the_episode_download( 'download -nobor' ); ?>
			<span class="date"><?php ee_the_date(); ?></span>
			<?php ee_the_share_buttons( get_permalink(), get_the_title() ); ?>
		</div>
	</div>

	<div class="entry-content content-wrap">
		<div class="description"><?php
			if ( ! ee_is_common_mobile() ) :
				add_filter( 'the_content', 'strip_shortcodes', 1 );
				the_content();
				remove_filter( 'the_content', 'strip_shortcodes', 1 );
			else :
				the_content();
			endif;
		?></div>

		<?php get_template_part( 'partials/episode/next-episodes' ); ?>
		<?php get_template_part( 'partials/episode/podcasts' ); ?>
		<?php get_template_part( 'partials/ads/sidebar-sticky' ); ?>
	</div>
</div>

<?php get_footer(); ?>

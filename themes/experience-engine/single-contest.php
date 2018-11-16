<?php get_header(); ?>

<?php the_post(); ?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php get_template_part( 'partials/show/header' ); ?>

	<?php get_template_part( 'partials/featured-media' ); ?>
	<h1><?php the_title(); ?></h1>

	<div>
		<?php the_content(); ?>
	</div>

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
</div>

<?php get_footer(); ?>

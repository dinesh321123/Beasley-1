<?php $contest_id = get_the_ID(); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
	<?php bbgi_featured_image_layout_is( get_the_ID(), 'top' ) ? get_template_part( 'partials/feature-image-contest' ) : ''; ?>

	<header class="entry__header">
		<time class="entry__date" datetime="<?php echo get_the_time(); ?>"><?php the_date( 'F j, Y' ); ?></time>
		<h2 class="entry__title" itemprop="headline">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			<sup class="entry__title--tag"><?php echo esc_html( gmr_contest_get_type_label() ); ?></sup>
		</h2>

		<?php get_template_part( 'partials/social-share' ); ?>
	</header>

	<?php bbgi_featured_image_layout_is( get_the_ID(), 'inline' ) ? get_template_part( 'partials/feature-image-contest' ) : ''; ?>

	<?php the_content(); ?>

	<?php if ( ( $contest_prize = trim( get_post_meta( get_the_ID(), 'prizes-desc', true ) ) ) ) : ?>
		<div class="contest__description">
			<h3 class="contest__prize--title"><?php _e( 'What you win:', 'greatermedia' ); ?></h3>
			<?php echo wpautop( do_shortcode( $contest_prize ) ); ?>
		</div>
	<?php endif; ?>

	<?php if ( ( $enter = trim( get_post_meta( get_the_ID(), 'how-to-enter-desc', true ) ) ) ) : ?>
		<div class="contest__description">
			<h3 class="contest__prize--title"><?php _e( 'How to enter:', 'greatermedia' ); ?></h3>
			<?php echo wpautop( do_shortcode( $enter ) ); ?>
		</div>
	<?php endif; ?>

	<?php if ( ( $contest_rules = trim( get_post_meta( get_the_ID(), 'rules-desc', true ) ) ) ) : ?>
		<div class="contest__description">
			<p>
				<a class="contest-attr--rules-toggler pjax-exclude" href="#" data-toggle="collapse" data-target="#contest-rules" data-alt-text="Hide Contest Rules">
					<?php _e( 'Show Contest Rules', 'greatermedia' ); ?>
				</a>
			</p>

			<div id="contest-rules" class="contest-attr--rules" style="display:none;"><?php echo wpautop( do_shortcode( $contest_rules ) ); ?></div>
		</div>
	<?php endif; ?>

	<?php get_template_part( 'partials/article', 'footer' ); ?>
</article>

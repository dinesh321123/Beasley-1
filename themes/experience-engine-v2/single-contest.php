<?php

get_header();

ee_switch_to_article_blog();
the_post();


ob_start();
if ( ( $contest_rules = trim( get_post_meta( get_the_ID(), 'rules-desc', true ) ) ) ) : ?>
	<div id="contenttermsdescription" class="contest__description">
		<p>
			<button id="contest-rules-toggle" class="contest-attr--rules-toggler" data-toggle="collapse" data-target="#contest-rules" data-alt-text="Hide Contest Rules">
				View contest rules
			</button>
		</p>
		<div id="contest-rules" class="contest-attr--rules">
			<h4>Contest Rules</h4>
			<?php echo wpautop( do_shortcode( $contest_rules ) ); ?>
		</div>
	</div>
<?php endif;
$contest_rules_output = ob_get_clean(); ?>
<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="post-info">
		<?php if ( bbgi_featured_image_layout_is( null, 'top' ) || bbgi_featured_image_layout_is( null, 'poster' ) ) : ?>
			<?php get_template_part( 'partials/featured-media' ); ?>
		<?php endif; ?>

		<h1>
			<?php the_title(); ?>
		</h1>

		<div class="post-meta">
			<?php get_template_part( 'partials/content/meta' ); ?>
		</div>
	</header>

	<div class="entry-content content-wrap">
		<div class="description">
			<?php if ( bbgi_featured_image_layout_is( null, 'inline' ) ) : ?>
				<?php get_template_part( 'partials/featured-media' ); ?>
			<?php endif; ?>

			<?php
			if (ee_is_common_mobile()) {
				echo do_shortcode( '[show-on-device devices="ios"]<span class="apple-rules-whiz"><br/>This contest is in no way affiliated with or endorsed by Apple.  <a href="#contenttermsdescription">See official contest rules</a><br/></span>[/show-on-device]' );
			}
			?>

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

			<?php echo $contest_rules_output; ?>
			<div style="font-size: small"><?php echo $_SERVER['HTTP_USER_AGENT'] ?></div>
			<?php get_template_part( 'partials/footer/common', 'description' ); ?>
			<?php get_template_part( 'partials/content/categories' ); ?>
			<?php get_template_part( 'partials/content/tags' ); ?>
		</div>

		<?php get_template_part( 'partials/ads/sidebar-sticky' ); ?>
	</div>

	<?php get_template_part( 'partials/related-articles' ); ?>
</div><?php

restore_current_blog();
get_footer();

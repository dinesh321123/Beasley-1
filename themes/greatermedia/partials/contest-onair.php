<?php $contest_id = get_the_ID(); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf collapsed' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

	<?php if ( has_post_thumbnail() ) : ?>
		<div class="contest__thumbnail" style='background-image: url(<?php gm_post_thumbnail_url( 'gmr-contest-thumbnail' ); ?>)'></div>
	<?php endif; ?>

	<header class="entry__header">
		<?php $encoded_permalink = urlencode( get_permalink() ); ?>
		<?php $encoded_title = urlencode( get_the_title() ); ?>

		<time class="entry__date" datetime="<?php echo get_the_time(); ?>"><?php the_date( 'F j' ); ?></time>
		<h2 class="entry__title" itemprop="headline">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			<sup class="entry__title--tag"><?php echo esc_html( gmr_contest_get_type_label() ); ?></sup>
		</h2>

		<a class="icon-facebook social-share-link" href="http://www.facebook.com/sharer/sharer.php?u=<?php echo $encoded_permalink; ?>&title=<?php echo $encoded_title; ?>"></a>
		<a class="icon-twitter social-share-link" href="http://twitter.com/home?status=<?php echo $encoded_title; ?>+<?php echo $encoded_permalink; ?>"></a>
		<a class="icon-google-plus social-share-link" href="https://plus.google.com/share?url=<?php echo $encoded_permalink; ?>"></a>
	</header>

	<?php the_content(); ?>

	<?php if ( ( $contest_prize = trim( get_post_meta( get_the_ID(), 'prizes-desc', true ) ) ) ) : ?>
		<div class="contest__description">
			<h3 class="contest__prize--title"><?php _e( 'What you win:', 'greatermedia' ); ?></h3>
			<?php echo wpautop( $contest_prize ); ?>
		</div>
	<?php endif; ?>

	<?php if ( ( $enter = trim( get_post_meta( get_the_ID(), 'how-to-enter-desc', true ) ) ) ) : ?>
		<div class="contest__description">
			<?php echo wpautop( $enter ); ?>
		</div>
	<?php endif; ?>

	<?php if ( ( $contest_rules = trim( get_post_meta( get_the_ID(), 'rules-desc', true ) ) ) ) : ?>
	<div class="contest__description">
		<p>
			<a class="contest-attr--rules-toggler pjax-exclude" href="#" data-toggle="collapse" data-target="#contest-rules" data-alt-text="Hide Contest Rules">
				<?php _e( 'Show Contest Rules', 'greatermedia' ); ?>
			</a>
		</p>

		<div id="contest-rules" class="contest-attr--rules" style="display:none;"><?php echo wpautop( $contest_rules ); ?></div>
	</div>
	<?php endif; ?>

	<?php get_template_part( 'partials/post', 'footer' ); ?>
</article>

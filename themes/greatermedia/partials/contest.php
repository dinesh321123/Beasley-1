<?php $contest_id = get_the_ID(); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf collapsed' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

	<div class="ad__inline--right desktop">
		<?php do_action( 'acm_tag', 'mrec-lists' ); ?>
	</div>
	
	<section class="col__inner--left">

		<header class="entry__header">
			<time class="entry__date" datetime="<?php echo get_the_time(); ?>"><?php the_date( 'F j' ); ?></time>
			<h2 class="entry__title" itemprop="headline">
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				<sup class="entry__title--tag"><?php echo esc_html( gmr_contest_get_type_label() ); ?></sup>
			</h2>

			<?php get_template_part( 'partials/social-share' ); ?>
		</header>

		<div class="contest__restrictions">
			<div class="contest__restriction--not-started">
				<p>The contest is not started yet.</p>
			</div>

			<div class="contest__restriction--finished">
				<p>The contest is already finished.</p>
			</div>

			<div class="contest__restriction--max-entries">
				<p>This contest has reached maximum number of entries!</p>
			</div>

			<div class="contest__restriction--age">
				<p>
					Please, <a href="<?php echo esc_url( gmr_contests_get_login_url() ); ?>">sign in</a>
					or confirm that you are at least <?php echo absint( get_post_meta( $contest_id, 'contest-min-age', true ) ); ?> years old.
				</p>
				<p>
					<a class="contest__restriction--min-age-yes" href="#">Yes, I am</a>
					<a class="contest__restriction--min-age-no" href="#">No, I am not</a>
				</p>
			</div>

			<div class="contest__restriction--age-fails">
				<p>You must be at least <?php echo absint( get_post_meta( $contest_id, 'contest-min-age', true ) ); ?> years old to enter the contest!</p>
			</div>

			<div class="contest__restriction--signin">
				<p>
					You must be signed in to enter the contest!
				</p>
				<p>
					<a href="<?php echo esc_url( gmr_contests_get_login_url() ); ?>">Sign in here</a>
				</p>
			</div>

			<div class="contest__restriction--one-entry">
				<p>You have already entered this contest!</p>
			</div>
		</div>

		<div class="contest__entry--link">
			<a href="#contest-form" class="contest__entry--btn">Enter Contest</a>
		</div>

		<?php the_content(); ?>

		<?php if ( ( $contest_prize = trim( get_post_meta( get_the_ID(), 'prizes-desc', true ) ) ) ) : ?>
			<div class="contest__description">
				<h3 class="contest__prize--title"><?php _e( 'What you win:', 'greatermedia' ); ?></h3>
				<?php echo wpautop( $contest_prize ); ?>
			</div>
		<?php endif; ?>

		<?php if ( ( $enter = trim( get_post_meta( get_the_ID(), 'how-to-enter-desc', true ) ) ) ) : ?>
			<div class="contest__description">
				<h3 class="contest__prize--title"><?php _e( 'How to enter:', 'greatermedia' ); ?></h3>
				<?php echo wpautop( $enter ); ?>
			</div>
		<?php endif; ?>

		<?php if ( ( $contest_rules = trim( get_post_meta( get_the_ID(), 'rules-desc', true ) ) ) ) : ?>
		<div class="contest__description">
			<p>
				<a class="contest-attr--rules-toggler pjax-exclude" href="#" data-toggle="collapse" data-target="#contest-rules" data-alt-text="Hide Contest Rules">
					<?php _e( 'view contest rules', 'greatermedia' ); ?>
				</a>
			</p>

			<div id="contest-rules" class="contest-attr--rules" style="display:none;"><?php echo wpautop( $contest_rules ); ?></div>
		</div>
		<?php endif; ?>

		<?php get_template_part( 'partials/post', 'footer' ); ?>

	</section>

	<section id="contest-form" class="col__inner--right contest__form"<?php gmr_contest_container_attributes(); ?>></section>

	<?php get_template_part( 'partials/submission', 'tiles' ); ?>

</article>

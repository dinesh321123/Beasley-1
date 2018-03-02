<?php
/**
 * Entry partial
 *
 * This is a work-in-progress and will eventually be broken into multiple
 * partials for the individual formats and post types. But for now this one
 * covers them all.
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry2' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
	<?php
	if ( has_post_thumbnail() || 'tribe_events' == $post->post_type ) :
		if ( has_post_format( 'audio' ) ) {
			$thumbnail_size = 'gm-entry-thumbnail-1-1';
		} else {
			$thumbnail_size = 'gm-entry-thumbnail-4-3';
		}
	?>
		<section class="entry2__thumbnail">
			<a href="<?php the_permalink(); ?>">
				<div class="entry2__thumbnail__image" style='background-image: url(<?php gm_post_thumbnail_url( $thumbnail_size ); ?>)'></div>
				<div class="entry2__thumbnail__icon"></div>
			</a>
		</section>
	<?php endif; ?>

	<section class="entry2__meta">
		<?php
			$category = get_the_category();

			if( isset( $category[0] ) ){
				echo '<a href="' . esc_url( get_category_link($category[0]->term_id ) ) . '" class="entry2__category">' . esc_html( $category[0]->cat_name ) . '</a>';
			}
		?>

		<h2 class="entry2__title" itemprop="headline"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

		<div class="entry2__excerpt">
			<?php the_excerpt(); ?>
		</div>
	</section>

</article>

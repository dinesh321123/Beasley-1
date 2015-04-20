<?php 
/**
 * Entry partial
 * 
 * This is a work-in-progress and will eventually be broken into multiple 
 * partials for the individual formats and post types. But for now this one 
 * covers them all. 
 */
$layout = sprintf( 'gmr_category_%d_layout', get_queried_object_id() );
$layout = get_option( $layout );
if ( ! empty( $layout ) ) {
	$layout = 'category-' . $layout;
}
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
				<?php if ( empty ( $layout ) ) { ?>
					<div class="entry2__thumbnail__icon"></div>
				<?php } ?>
				<?php if ( is_archive() && ! empty ( $layout ) && has_post_format( 'video' ) ) { ?>
					<div class="top-three__play <?php echo esc_html( $layout ); ?>"></div>
				<?php } ?>
			</a>
		</section>
	<?php endif; ?>

	<section class="entry2__meta">
		<time datetime="<?php the_time( 'c' ); ?>" class="entry2__date"><?php the_time( 'F j, Y' ); ?></time>
		
		<h2 class="entry2__title" itemprop="headline"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		
		<div class="entry2__excerpt">
			<?php the_excerpt(); ?>
		</div>		
	</section>

	<footer class="entry2__footer">
		<?php
		$category = get_the_category();

		if( isset( $category[0] ) ){
			echo '<a href="' . esc_url( get_category_link($category[0]->term_id ) ) . '" class="entry2__footer--category">' . esc_html( $category[0]->cat_name ) . '</a>';
		}
		?>

		<a href="<?php the_permalink(); ?>" class="entry2__footer--read-more"><?php _e( 'read more', 'greatermedia' ); ?></a>
	</footer>
</article>

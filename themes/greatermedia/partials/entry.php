<?php 
/**
 * Entry partial
 * 
 * This is a work-in-progress and will eventually be broken into multiple 
 * partials for the individual formats and post types. But for now this one 
 * covers them all. 
 */

global $post;
$post_classes = array( 'entry2' );

if ( has_post_thumbnail() || 'tribe_events' == $post->post_type ) {
	$post_classes[] = 'has-thumbnail';
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( $post_classes ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
	<?php 
	if ( has_post_thumbnail() || 'tribe_events' == $post->post_type ) : 
		if ( has_post_format( 'audio' ) ) {
			$thumbnail_size = 'gm-entry-thumbnail-1-1';
		} else {
			$thumbnail_size = 'gm-entry-thumbnail-4-3';
		}
	?>
		<section class="entry2__thumbnail" style='background-image: url(<?php gm_post_thumbnail_url( $thumbnail_size ); ?>)'>
			<a href="<?php the_permalink(); ?>">
				<?php if ( 'tribe_events' == $post->post_type): ?>
					<div class='entry2__thumbnail--event-date'>
						<div class='entry2__thumbnail--day-of-week'><?php echo tribe_get_start_date( get_the_ID(), false, 'l' ); ?></div>
						<div class='entry2__thumbnail--month-and-day'><?php echo tribe_get_start_date( get_the_ID(), false, 'M j' ); ?></div>										
					</div>
				<?php endif; ?>
			</a>								
		</section>
	<?php endif; ?>

	<section class="entry2__meta">
		<?php if ( 'tribe_events' != $post->post_type): ?>
			<time datetime="<?php the_time( 'c' ); ?>" class="entry2__date"><?php the_time( 'F j' ); ?></time>
		<?php endif; ?>
		<h2 class="entry2__title" itemprop="headline"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				
		<?php if ( 'tribe_events' == $post->post_type ): ?>
			<ul class="entry2__event--details">
				<?php 
				// @todo I should probably be a function, because this type of 
				// crazy doesn't belong in the main template files.
				echo esc_html( implode(', ', array_filter( array( tribe_get_start_time(), tribe_get_venue(), tribe_get_cost() ) ) ) );
				?>
			</ul>
		<?php else: ?>
			<div class="entry2__excerpt">
				<?php the_excerpt(); ?>
			</div>
		<?php endif; ?>
		
	</section>

	<footer class="entry2__footer">
		<?php
		$category = get_the_category();

		if( isset( $category[0] ) ){
			echo '<a href="' . esc_url( get_category_link($category[0]->term_id ) ) . '" class="entry2__footer--category">' . esc_html( $category[0]->cat_name ) . '</a>';
		}
		?>
	</footer>
</article>

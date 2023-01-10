<?php
	$category_archive_data = get_query_var( 'category_archive_data' );
	$category_archive_post = isset($category_archive_data['category_archive_post']) ? $category_archive_data['category_archive_post'] : null;

	$cap_is_video = in_array( 'format-video', get_post_class('', $category_archive_post) );
	if ($cap_is_video) {
		echo "<div class='format-video'>";
	}
?>

<div class="post-thumbnail">
	<a href="<?php ee_the_permalink($category_archive_post); ?>" aria-label="Post thumbnail description link">
		<?php ee_the_lazy_thumbnail( $category_archive_post, true ); ?>
		<?php if( has_post_format( 'video', $category_archive_post->ID ) ) : ?>
			<div class="post-video-overlay">
				<?php $label_id = 'label-' . uniqid(); ?>
				<svg viewBox="0 0 17 24" xmlns="http://www.w3.org/2000/svg" role="img"
					aria-labelledby="<?php echo esc_attr( $label_id ); ?> video-icon-desc-<?php echo esc_attr( $label_id ); ?>">
					<title id="<?php echo esc_attr( $label_id ); ?>">Video Icon</title>
					<description id="video-icon-desc-<?php echo esc_attr( $label_id ); ?>">A circular icon, with a red play button that symbolizes that the post is a video</description>
					<path d="M16.1836 12.0055L0.910156 23.124L0.910156 0.887031L16.1836 12.0055Z"></path>
				</svg>
			</div>
		<?php endif; ?>

		<?php if( stripos( esc_attr($category_archive_post->feed_title), ' Miss') !== false ) : ?>
			<?php ee_the_sponsored_by_thumbnail_overlay( $category_archive_post->ID ); ?>
		<?php endif; ?>
	</a>
</div>
<?php
	if ($cap_is_video) {
		echo "</div>";
	}
?>
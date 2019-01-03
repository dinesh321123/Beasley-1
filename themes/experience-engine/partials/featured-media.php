<?php if ( has_post_thumbnail() ) : ?>
	<div class="post-thumbnail-wrapper">
		<div class="post-thumbnail">
			<?php ee_the_lazy_thumbnail(); ?>
			<?php bbgi_the_image_attribution(); ?>
		</div>

		<?php $thumbnail_caption = get_the_post_thumbnail_caption( get_the_ID() ); ?>
		<?php if( $thumbnail_caption ) : ?>
			<div class="post-thumbnail-caption">
				<?php echo $thumbnail_caption; ?>
			</div>
		<?php endif; ?>
	</div>
<?php endif; ?>
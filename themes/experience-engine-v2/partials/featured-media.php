<?php if ( has_post_thumbnail() ) : ?>
	<div class="post-thumbnail-wrapper">
		<div class="post-thumbnail featured-media">
			<?php ee_the_lazy_thumbnail(); ?>
		</div>

		<?php $thumbnail_caption = get_the_post_thumbnail_caption( get_the_ID() ); ?>
		<?php if( $thumbnail_caption ) : ?>
			<div class="post-thumbnail-caption">
				<?php echo $thumbnail_caption; ?>
			</div>
		<?php endif; ?>
	</div>
<?php endif; ?>

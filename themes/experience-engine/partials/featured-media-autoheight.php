<?php if ( has_post_thumbnail() ) : ?>
	<div class="post-thumbnail-wrapper">
		<div class="post-thumbnail featured-media -autoheight"><?php 
			$callback = function( $html ) {
				return str_replace( '<div ', '<div data-autoheight="1" ', $html );
			};

			add_filter( 'post_thumbnail_html', $callback );
			ee_the_lazy_thumbnail(); 
			remove_filter( 'post_thumbnail_html', $callback );
		?></div>

		<?php $thumbnail_caption = get_the_post_thumbnail_caption( get_the_ID() ); ?>
		<?php if( $thumbnail_caption ) : ?>
			<div class="post-thumbnail-caption">
				<?php echo $thumbnail_caption; ?>
			</div>
		<?php endif; ?>
	</div>
<?php endif; ?>

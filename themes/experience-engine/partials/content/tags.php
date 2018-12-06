<?php if ( has_tag() ) : ?>
	<div class="post-tags">
		<?php the_tags( '<div class="post-tag-label">Tags</div><div class="post-tag-items">', ',', '</div>' ); ?>
	</div>
<?php endif; ?>

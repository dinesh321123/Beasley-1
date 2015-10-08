<ul>
	<?php foreach ( $entries as $entry ): ?>
		<?php $entry_ugc = GreaterMediaUserGeneratedContent::for_post_id( $entry->ID ); ?>
		<li class="contest-entry-ugc-preview"><?php echo $entry_ugc->render_preview(); ?></li>
	<?php endforeach; ?>
</ul>
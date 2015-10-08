<p class="contest-link">
	<a href="<?php echo esc_attr( get_the_permalink( $contest->ID ) ); ?>" target="_blank"><?php echo get_the_title( $contest->ID ); ?></a>
</p>
<p class="contest-dates">
	<?php echo date_i18n( get_option( 'date_format' ), $start_date ); ?>&nbsp;-&nbsp;<?php echo date_i18n( get_option( 'date_format' ), $end_date ); ?>
</p>
<ul class="contest-types">
	<?php foreach ( $contest_types as $contest_type ) : ?>
		<li class="dashicons dashicons-tag"><?php echo $contest_type->name; ?></li>
	<?php endforeach; ?>
</ul>
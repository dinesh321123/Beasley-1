<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cannot access pages directly.' );
} ?>

<div class="wrap">
	<h2>Listener Submission Moderation</h2>

	<form action="/ugc/bulk">
		<?php $wp_list_table->display_tablenav( 'top' ); ?>
		<table class="wp-list-table widefat fixed posts listener-submissions">
			<?php $wp_list_table->display_rows(); ?>
		</table>
		<?php $wp_list_table->display_tablenav( 'bottom' ); ?>
	</form>
</div>
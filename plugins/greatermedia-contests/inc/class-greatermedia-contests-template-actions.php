<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

class GreaterMediaContestsTemplateActions {

	function __construct() {

		add_action( 'gmr_contest_list_filter', array( __CLASS__, 'contest_list_filter' ) );

	}

	public static function contest_list_filter() {

		$terms = get_terms( 'contest_type' );
		$query_var = get_query_var( 'contest_type' );

		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
	?>
			<ul id="contest-filter">
		<?php
			foreach ($terms as $key => $term) {

				if ( 0 === $key ) {
				?>
					<li "<?php if ( empty( $query_var ) ) { echo 'class="selected"'; } ?>><a href="<?php echo get_post_type_archive_link( GMR_CONTEST_CPT ); ?>"><?php esc_html_e( 'All', 'greatermedia_contests' ); ?></a></li>
				<?php
				}
			?>

				<li <?php if ( $term->slug === $query_var ) { echo 'class="selected"'; } ?>><a href="<?php echo get_post_type_archive_link( GMR_CONTEST_CPT ) . 'type/' . esc_attr( $term->slug, 'greatermedia_contests' ); ?>"><?php esc_html_e( $term->name, 'greatermedia_contests' ); ?></a></li>

			<?php
			}
		?>
			</ul>
	<?php
		}
	}
}

$GreaterMediaContestsTemplateActions = new GreaterMediaContestsTemplateActions();

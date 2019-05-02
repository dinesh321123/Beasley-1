<?php
/**
 * Songs Archive Template
 *
 * This template takes the Call Sign queried and uses the Now Playing
 * endpoint to show a list of recent songs. This template renders a
 * placeholder that is filled in by the SongArchive React component.
 *
 * @package Experience Engine
 * @since   1.0.0
 */

get_header();

$call_sign = get_query_var( GMR_LIVE_STREAM_CPT );

$query_params = [
	'limit'  => 100,
	'offset' => 0,
];

$endpoint = 'https://nowplaying.bbgi.com/' . esc_attr( $call_sign ) . '/list?' . http_build_query( $query_params );

$stream_query = new WP_Query( [
	'post_type'           => GMR_LIVE_STREAM_CPT,
	'meta_key'            => 'call_sign',
	'meta_value'          => $call_sign,
	'posts_per_page'      => 1,
	'ignore_sticky_posts' => 1,
	'no_found_rows'       => true,
	'fields'              => 'ids',
] );

$streams = $stream_query->posts;

if ( ! empty( $streams ) ) {
	$description = get_post_meta( $streams[0], 'description', true );
} else {
	$description = $call_sign;
}
?>

	<div class="container">

		<section class="content">

			<div class="song-archive-prerender"
				data-callsign="<?php echo esc_attr( $call_sign ); ?>"
				data-endpoint="<?php echo esc_url( $endpoint ); ?>"
				data-description="<?php echo esc_attr( $description ); ?>"
				>

			</div>

		</section>

	</div>

<?php get_footer(); ?>

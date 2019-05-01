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

?>

	<div class="container">

		<section class="content">

			<div class="song-archive-prerender"
				data-callsign="<?php echo esc_attr( $call_sign ); ?>"
				data-endpoint="<?php echo esc_url( $endpoint ); ?>"
				>

			</div>

		</section>

	</div>

<?php get_footer(); ?>
